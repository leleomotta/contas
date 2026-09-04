<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CartaoController extends Controller
{
    /**
     * Retorna descrições (únicas) já existentes de DESPESAS DE CARTÃO.
     *
     * Estratégia:
     * - Join despesa + fatura para garantir que são despesas vinculadas ao cartão (fatura).
     * - TRIM() evita duplicidade por espaços.
     * - GROUP BY garante unicidade.
     * - COUNT(*) permite ordenar por “mais usadas”.
     *
     * Query params:
     * - q (string): termo digitado (opcional)
     * - limit (int): máximo de itens (opcional, padrão 15, máximo 50)
     * - ID_Cartao (int): se informado, filtra somente descrições daquele cartão (opcional)
     */
    public function despesaDescricoes(Request $request)
    {
        // Termo digitado pelo usuário no campo Descrição
        $q = trim((string) $request->query('q', ''));

        // ID do cartão selecionado no formulário
        $idCartao = $request->query('ID_Cartao');

        // Limite defensivo para evitar retorno muito grande
        $limit = (int) $request->query('limit', 15);
        $limit = max($limit, 1);
        $limit = min($limit, 50);

        /*
         * Query base:
         *
         * - despesa: onde está a descrição e a categoria
         * - fatura: garante que estamos olhando despesas de cartão
         * - categoria: busca o nome da categoria da despesa
         * - categoria_pai: busca a categoria pai, quando existir
         */
        $query = DB::table('despesa')
            ->join('fatura', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->selectRaw("TRIM(despesa.Descricao) as descricao")
            ->selectRaw("despesa.ID_Categoria as id_categoria")
            ->selectRaw("
            COALESCE(
                CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome),
                categoria.Nome,
                'Sem categoria'
            ) as categoria
        ")
            ->selectRaw("COUNT(*) as total")
            ->whereNotNull('despesa.Descricao')
            ->whereRaw("TRIM(despesa.Descricao) <> ''");

        /*
         * Se o usuário selecionou um cartão, filtramos as sugestões
         * apenas por despesas já lançadas naquele cartão.
         */
        if (!empty($idCartao)) {
            $query->where('fatura.ID_Cartao', (int) $idCartao);
        }

        /*
         * Se o usuário digitou alguma coisa, filtra pela descrição.
         */
        if ($q !== '') {
            $query->where('despesa.Descricao', 'like', '%' . $q . '%');
        }

        /*
         * Mudança principal:
         *
         * Antes agrupava apenas pela descrição.
         * Agora agrupa por descrição + categoria.
         *
         * Assim, se existir a mesma descrição em categorias diferentes,
         * todas aparecerão no autocomplete.
         */
        $descricoes = $query
            ->groupByRaw("
            TRIM(despesa.Descricao),
            despesa.ID_Categoria,
            categoria.Nome,
            categoria_pai.Nome
        ")
            ->orderByDesc('total')
            ->orderBy('descricao')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'descricao' => $item->descricao,
                    'id_categoria' => $item->id_categoria,
                    'categoria' => $item->categoria,
                    'sugestao' => $item->descricao . ' (' . $item->categoria . ')',
                    'total' => $item->total,
                ];
            });

        return response()->json($descricoes);
    }

    /**
     * Remove o recurso de cartão especificado do armazenamento.
     *
     * @param Cartao $cartao
     * @return void
     */
    public function destroy(Cartao $cartao)
    {
        // Implemente a lógica para excluir um cartão
    }

    /**
     * Remove uma despesa associada a uma fatura.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);

        // Guarda antes de deletar (depois do delete você perde a referência)
        $anoMes = $fatura->Ano_Mes;
        $idCartao = $request->ID_Cartao;

        try {
            DB::beginTransaction();
            $fatura->delete();
            $despesa->delete();

            DB::commit();

            return redirect()->route('cartoes.fatura', [
                'ID_Cartao' => $idCartao,
                'Ano_Mes'   => $anoMes,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return back();
        }
    }

    /**
     * Exibe o formulário para editar uma despesa de fatura.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function edit_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);
        $categorias = (new \App\Models\Categoria)->show('D');

        return view('fatura_despesaEditar', [
            'despesa' => $despesa,
            'fatura' => $fatura,
            'categorias' => $categorias
        ]);
    }

    /**
     * Exibe a fatura de um cartão para um mês/ano específico.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function fatura(Request $request)
    {
        // 1) Prioriza o ID_Cartao que veio na URL (GET). Se não vier, usa o que estiver na sessão.
        $ID_Cartao = $request->filled('ID_Cartao')
            ? (int) $request->ID_Cartao
            : (int) $request->session()->get('ID_Cartao');

        // 2) Se veio na URL, sincroniza a sessão (assim botões/formulários que usam Session ficam corretos).
        if ($request->filled('ID_Cartao')) {
            $request->session()->put('ID_Cartao', $ID_Cartao);
        }

        // 3) Se não vier Ano_Mes, evita tela “em branco” e usa o mês atual.
        //$Ano_Mes = $request->Ano_Mes ?: Carbon::now()->format('Y-m');
        /*
         * Busca o cartão completo porque precisamos
         * do Dia_Fechamento_Fatura.
         */
        $cartaoModel = Cartao::findOrFail($ID_Cartao);


        /*
         * Se Ano_Mes vier explicitamente na URL,
         * respeitamos o mês solicitado.
         *
         * Isso é MUITO IMPORTANTE para permitir navegar
         * por faturas antigas e futuras.
         *
         * Exemplo:
         *
         * /cartoes/fatura?ID_Cartao=1&Ano_Mes=2026-07
         *
         * deverá continuar abrindo julho.
         */
        if ($request->filled('Ano_Mes')) {

            $Ano_Mes = $request->Ano_Mes;

        } else {

            /*
             * Se não foi informado nenhum mês,
             * significa que queremos a FATURA ATUAL.
             *
             * Aplicamos então a mesma regra usada no cadastro.
             */
            $Ano_Mes = Fatura::anoMesFaturaAberta(
                $cartaoModel,
                Carbon::today()
            );

        }


        /*
         * Nome do cartão para a view.
         */
        $cartao = $cartaoModel->Nome;

        // Busca o status de fechamento da fatura
        $faturaPrimeiro = Fatura::where('ID_Cartao', $ID_Cartao)->where('Ano_Mes', $Ano_Mes)->first();
        $cartao = Cartao::where('ID_Cartao', $ID_Cartao)->first()->Nome;

        $fechada = ($faturaPrimeiro && $faturaPrimeiro->Fechada == 1);

        $fatura = new Fatura();
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::all();

        if (is_null($request->session()->get('ID_Cartao'))) {
            $request->session()->put('ID_Cartao', $request->ID_Cartao);
        }

        Log::info('AnoMes: ' . $Ano_Mes);
        Log::info('ID_Cartao: ' . $ID_Cartao);

        return view('fatura_despesaListar', [
            'faturas' => $fatura->show($Ano_Mes, $ID_Cartao),
            'totalFatura' => 0,
            'contas' => $contas,
            'cartao' => $cartao,
            'Ano_Mes' => $Ano_Mes,
            'fechada' => $fechada,
            'ID_Cartao' => $ID_Cartao,
        ]);
    }

    /**
     * Fecha a fatura de um cartão e move as despesas para uma conta.
     */
    public function fatura_fechar(Request $request)
    {
        $Ano_Mes = is_null($request->Ano_Mes)
            ? Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM')
            : $request->Ano_Mes;

        $ID_Cartao = $request->ID_Cartao;
        $Data = implode("-", array_reverse(explode("/", $request->Data_Fechamento)));
        $Conta = $request->Conta;

        (new Fatura)->fatura_fechar($Ano_Mes, $ID_Cartao, $Data, $Conta);

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $ID_Cartao, 'Ano_Mes' => $Ano_Mes]);
    }

    /**
     * Reabre uma fatura fechada.
     */
    public function fatura_reabrir(Request $request)
    {
        $Ano_Mes = is_null($request->Ano_Mes)
            ? Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM')
            : $request->Ano_Mes;

        $ID_Cartao = $request->ID_Cartao;

        (new Fatura)->fatura_reabrir($Ano_Mes, $ID_Cartao);

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $ID_Cartao, 'Ano_Mes' => $Ano_Mes]);
    }

    /**
     * Exibe a listagem do recurso.
     *
     * @return void
     */
    public function index()
    {
        // Esta função está vazia
    }

    /**
     * Exibe o formulário para criar um novo cartão.
     */
    public function new()
    {
        $contas = (new \App\Models\Conta)->showAll();

        return view('cartaoCriar', [
            'contas' => $contas,
        ]);
    }

    /**
     * Exibe o formulário para criar uma nova despesa de cartão.
     *
     * Para cada cartão, calculamos antecipadamente qual é a sua
     * fatura atualmente aberta.
     *
     * Esses valores serão utilizados pelo Blade para preencher
     * automaticamente os campos Ano e Mês quando o usuário
     * selecionar um cartão.
     */
    public function new_despesa(Request $request)
    {
        /*
         * Dados normais da tela.
         */
        $contas = (new \App\Models\Conta)->showAll();

        $cartoes = Cartao::all();

        $categorias = (new \App\Models\Categoria)->show('D');


        /*
         * =============================================================
         * CALCULA A FATURA ABERTA DE CADA CARTÃO
         * =============================================================
         *
         * Exemplo hoje, 03/09/2026:
         *
         * Inter Black
         * fechamento = dia 15
         *
         * resultado:
         *
         * Ano_Fatura_Aberta = 2026
         * Mes_Fatura_Aberta = 8
         */
        foreach ($cartoes as $cartao) {

            /*
             * Utiliza exatamente a regra centralizada que já testamos
             * anteriormente e que retornou corretamente 2026-08.
             */
            $anoMesAberto = Fatura::anoMesFaturaAberta(
                $cartao,
                Carbon::today()
            );


            /*
             * Divide:
             *
             * 2026-08
             *
             * em:
             *
             * 2026
             * 08
             */
            [$ano, $mes] = explode('-', $anoMesAberto);


            /*
             * Criamos propriedades temporárias somente para
             * utilizar na view.
             *
             * Não são gravadas no banco.
             */
            $cartao->Ano_Fatura_Aberta = (int) $ano;

            $cartao->Mes_Fatura_Aberta = (int) $mes;
        }


        /*
         * =============================================================
         * FATURA PADRÃO DA TELA
         * =============================================================
         *
         * Neste ponto já calculamos acima a fatura aberta de CADA cartão.
         *
         * Como a rota:
         *
         * /fatura/despesa/novo
         *
         * pode ser acessada sem nenhum cartão selecionado, precisamos
         * escolher um valor inicial para Ano/Mês.
         *
         * Utilizamos o mês aberto mais antigo entre os cartões.
         *
         * Exemplo hoje, 03/09/2026:
         *
         * Inter  -> 2026-08
         * Nubank -> 2026-09
         *
         * A tela abrirá inicialmente em:
         *
         * 2026-08
         *
         * Quando o usuário escolher um cartão, o JavaScript já existente
         * trocará Ano/Mês para a fatura específica daquele cartão.
         */
        $anoMesFaturaPadrao = $cartoes
            ->map(function ($cartao) {

                /*
                 * Monta novamente o formato YYYY-MM.
                 *
                 * Exemplo:
                 *
                 * Ano_Fatura_Aberta = 2026
                 * Mes_Fatura_Aberta = 8
                 *
                 * resultado:
                 *
                 * 2026-08
                 */
                return sprintf(
                    '%04d-%02d',
                    $cartao->Ano_Fatura_Aberta,
                    $cartao->Mes_Fatura_Aberta
                );

            })

            /*
             * Coloca o mês mais antigo primeiro.
             *
             * Exemplo:
             *
             * 2026-08
             * 2026-09
             */
            ->sort()

            /*
             * Pega o primeiro mês aberto.
             */
            ->first();


        /*
         * Caso não exista nenhum cartão cadastrado,
         * usamos o mês atual apenas como fallback.
         */
        if (!$anoMesFaturaPadrao) {

            $anoMesFaturaPadrao = Carbon::today()->format('Y-m');

        }


        /*
         * Separa:
         *
         * 2026-08
         *
         * em:
         *
         * 2026
         * 8
         */
        [$anoFaturaPadrao, $mesFaturaPadrao] =
            array_map(
                'intval',
                explode('-', $anoMesFaturaPadrao)
            );


        /*
         * Se já existir um cartão selecionado na sessão,
         * podemos abrir a tela diretamente com a fatura dele.
         */
        $ID_Cartao = old(
            'ID_Cartao',
            $request->session()->get('ID_Cartao')
        );


        if ($ID_Cartao) {

            /*
             * Procura o cartão dentro da Collection que já carregamos.
             */
            $cartaoSelecionado = $cartoes->firstWhere(
                'ID_Cartao',
                (int) $ID_Cartao
            );


            if ($cartaoSelecionado) {

                $anoFaturaPadrao =
                    $cartaoSelecionado->Ano_Fatura_Aberta;

                $mesFaturaPadrao =
                    $cartaoSelecionado->Mes_Fatura_Aberta;
            }
        }


        return view('fatura_despesaCriar', [

            'categorias' => $categorias,

            'contas' => $contas,

            'cartoes' => $cartoes,

            /*
             * Valores utilizados nos inputs Ano/Mês.
             */
            'anoFaturaPadrao' => $anoFaturaPadrao,

            'mesFaturaPadrao' => $mesFaturaPadrao,
        ]);
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Cartao $cartao
     * @return void
     */
    public function show(Cartao $cartao)
    {
        // Esta função está vazia
    }

    /**
     * ✅ Exibe a listagem de todos os cartões (AGORA com Ativos/Inativos via Arquivado).
     *
     * URL:
     * - /cartoes?status=ativos   => Arquivado = 0
     * - /cartoes?status=inativos => Arquivado = 1
     *
     * Observação:
     * - Mantive o uso do método Cartao->show($Ano_Mes) para não quebrar sua query atual
     * - Depois filtramos na Collection usando o campo Arquivado.
     */
    public function showAll(Request $request)
    {
        // Mês atual (o seu código usa isoFormat; deixei o mesmo padrão)
        $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');

        // status vindo da URL (default: ativos)
        $status = (string) $request->query('status', 'ativos');

        // Define o valor esperado no banco
        // ativos   => Arquivado = 0
        // inativos => Arquivado = 1
        $arquivado = ($status === 'inativos') ? 1 : 0;

        // Limpa seleção anterior de cartão (se for a lógica que você quer manter)
        Session::forget('ID_Cartao');

        // Usa sua query atual (provavelmente já calcula Ano_Mes, Valor, N_Despesas...)
        $cartoesModel = new Cartao();
        //$cartoes = collect($cartoesModel->show($Ano_Mes));
        /*
         * Não informamos Ano_Mes.
         *
         * Dessa forma cada cartão calcula sua própria
         * fatura atual conforme seu dia de fechamento.
         */
        $cartoes = collect(
            $cartoesModel->show()
        );


        /**
         * Filtra pelos arquivados/ativos.
         * IMPORTANTE:
         * - isso depende do campo "Arquivado" existir no resultado do show().
         * - se vier null, consideramos como 0 (ativo) para não “sumir” cartão antigo.
         */
        $cartoes = $cartoes->filter(function ($c) use ($arquivado) {
            // Se Arquivado não existir, deixa passar só nos "ativos" por segurança
            if (!isset($c->Arquivado)) {
                return $arquivado === 0;
            }

            return (int)$c->Arquivado === $arquivado;
        })->values();

        return view('cartaoListar', [
            'cartoes' => $cartoes,
            'status'  => $status, // para marcar o botão ativo no blade
        ]);

    }

    /**
     * Armazena um novo cartão no banco de dados.
     */
    public function store(Request $request)
    {
        $cartao = new Cartao();
        $cartao->Nome = $request->Nome;
        $cartao->Bandeira = $request->Bandeira;
        $cartao->Dia_Vencimento = $request->Dia_Vencimento;
        $cartao->Dia_Fechamento_Fatura = $request->Dia_Fechamento_Fatura;
        $cartao->ID_Conta = $request->Conta;
        $cartao->Cor = $request->corCartao;

        // Se o campo Arquivado existir, garantimos que novo cartão nasce ativo
        // (se não existir, o Eloquent ignora)
        $cartao->Arquivado = 0;

        $cartao->save();

        return redirect()->route('cartoes.showAll');
    }

    /**
     * Salva uma nova despesa de cartão.
     *
     * A fatura é escolhida AUTOMATICAMENTE utilizando:
     *
     * - Data da compra;
     * - Dia de fechamento do cartão;
     * - Status aberto/fechado da fatura.
     *
     * Para despesas parceladas, a primeira parcela utiliza
     * a fatura calculada pela data da compra e as demais
     * seguem para as próximas faturas abertas.
     */
    public function store_despesa(Request $request)
    {
        /*
         * =============================================================
         * VALIDAÇÃO
         * =============================================================
         */
        $request->validate([
            'Data'      => 'required',
            'Descricao' => 'required',
            'Valor'     => 'required',
            'Categoria' => 'required',
            'ID_Cartao' => 'required|integer',

            /*
             * A fatura é selecionável pelo usuário na tela.
             * Portanto, Ano e Mês também precisam ser validados
             * pelo servidor.
             */
            'Ano'       => 'required|integer|min:1900|max:2500',
            'Mes'       => 'required|integer|min:1|max:12',

        ], [
            'Data.required'      => 'Por favor, informe a data da despesa.',
            'Descricao.required' => 'A descrição da despesa é obrigatória.',
            'Valor.required'     => 'O valor da despesa não pode ficar vazio.',
            'Categoria.required' => 'Selecione uma categoria para organizar seus gastos.',
            'ID_Cartao.required' => 'É necessário selecionar um cartão para esta despesa.',

            'Ano.required'       => 'Informe o ano da fatura.',
            'Ano.integer'        => 'O ano da fatura deve ser um número válido.',

            'Mes.required'       => 'Informe o mês da fatura.',
            'Mes.integer'        => 'O mês da fatura deve ser um número válido.',
            'Mes.min'            => 'O mês da fatura deve estar entre 1 e 12.',
            'Mes.max'            => 'O mês da fatura deve estar entre 1 e 12.',
        ]);


        /*
         * =============================================================
         * CARTÃO
         * =============================================================
         *
         * Precisamos do cartão para saber:
         *
         * - ID_Conta;
         * - Dia_Fechamento_Fatura.
         */
        $cartao = Cartao::findOrFail($request->ID_Cartao);


        /*
         * =============================================================
         * DATA REAL DA COMPRA
         * =============================================================
         *
         * O formulário envia:
         *
         * 31/08/2026
         *
         * Transformamos em um objeto Carbon.
         */
        $dataCompra = Carbon::createFromFormat(
            'd/m/Y',
            $request->Data
        );


        /*
         * =============================================================
         * FATURA ESCOLHIDA PELO USUÁRIO
         * =============================================================
         *
         * A tela já sugere automaticamente a fatura apropriada quando
         * é aberta ou quando o usuário seleciona outro cartão.
         *
         * Entretanto, os campos Ano e Mês são editáveis justamente para
         * permitir que o usuário escolha outra fatura.
         *
         * Por isso, no momento do cadastro devemos RESPEITAR os valores
         * enviados pelo formulário, em vez de recalcular a fatura com
         * base na data da compra.
         *
         * Exemplos:
         *
         * Ano = 2026
         * Mes = 9
         *
         * Resultado:
         *
         * 2026-09
         *
         * Ano = 2026
         * Mes = 12
         *
         * Resultado:
         *
         * 2026-12
         */
        $Ano_Mes = sprintf(
            '%04d-%02d',
            (int) $request->Ano,
            (int) $request->Mes
        );

        /*
 * =============================================================
 * VERIFICA SE A FATURA ESCOLHIDA JÁ ESTÁ FECHADA
 * =============================================================
 *
 * Como a tabela fatura possui uma linha para cada despesa,
 * basta verificar se existe alguma despesa daquele cartão/mês
 * cuja fatura esteja marcada como fechada.
 */
        $faturaFechada = Fatura::where(
            'ID_Cartao',
            $cartao->ID_Cartao
        )
            ->where(
                'Ano_Mes',
                $Ano_Mes
            )
            ->where(
                'Fechada',
                1
            )
            ->exists();


        /*
         * Se a fatura estiver fechada, não permitimos adicionar
         * uma nova despesa nela.
         */
        if ($faturaFechada) {

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'Mes' =>
                        'A fatura de ' .
                        $Ano_Mes .
                        ' já está fechada. Escolha uma fatura em aberto.'
                ]);
        }



        /*
         * =============================================================
         * VALOR DA COMPRA
         * =============================================================
         *
         * Converte:
         *
         * R$ 1.234,56
         *
         * para:
         *
         * 1234.56
         */
        $valorStr = $request->Valor;

        $valorTotal = floatval(
            str_replace(
                ",",
                ".",
                str_replace(
                    ".",
                    "",
                    str_replace("R$ ", "", $valorStr)
                )
            )
        );


        /*
         * Descrição original.
         */
        $descricaoOriginal = $request->Descricao;


        /*
         * Verifica se a compra foi parcelada.
         */
        $parcelada = $request->Parcelada === 'sim';


        /*
         * Quantidade de parcelas.
         *
         * Compra não parcelada sempre terá 1 parcela.
         */
        $numParcelas = $parcelada
            ? max((int) $request->NumeroParcelas, 1)
            : 1;


        /*
         * =============================================================
         * DIVISÃO DO VALOR
         * =============================================================
         *
         * Mantém sua regra atual para distribuir corretamente
         * os centavos entre as parcelas.
         */
        $valorBase = floor(
                ($valorTotal / $numParcelas) * 100
            ) / 100;

        $diferenca = round(
            $valorTotal - ($valorBase * $numParcelas),
            2
        );


        /*
         * Mês da primeira parcela.
         *
         * Exemplo:
         *
         * $Ano_Mes = 2026-09
         *
         * vira:
         *
         * 01/09/2026
         *
         * Internamente usamos somente para controlar
         * o avanço das faturas.
         */
        $mesParcela = Carbon::createFromFormat(
            'Y-m-d',
            $Ano_Mes . '-01'
        );


        /*
         * =============================================================
         * TRANSAÇÃO
         * =============================================================
         *
         * Se acontecer qualquer erro durante o parcelamento,
         * nenhuma parcela fica salva pela metade.
         */
        DB::transaction(function () use (
            $request,
            $cartao,
            $dataCompra,
            $descricaoOriginal,
            $valorTotal,
            $valorBase,
            $diferenca,
            $parcelada,
            $numParcelas,
            $mesParcela,
            $Ano_Mes
        ) {

            for ($i = 1; $i <= $numParcelas; $i++) {

                /*
                 * =============================================================
                 * DEFINE A FATURA DA PARCELA
                 * =============================================================
                 *
                 * Para a PRIMEIRA parcela utilizamos exatamente a fatura
                 * já calculada com base na data da compra e no fechamento.
                 *
                 * Exemplo:
                 *
                 * Compra:       03/09/2026
                 * Fecha:        dia 15
                 * $Ano_Mes:     2026-08
                 *
                 * Portanto:
                 *
                 * primeira parcela -> 2026-08
                 */
                if ($i === 1) {

                    $anoMesParcela = $Ano_Mes;

                } else {

                    /*
                     * A partir da segunda parcela avançamos um mês.
                     *
                     * Exemplo:
                     *
                     * parcela 1 -> 2026-08
                     * parcela 2 -> 2026-09
                     * parcela 3 -> 2026-10
                     */
                    $mesParcela->addMonth();


                    /*
                     * Verifica se a fatura daquele próximo mês está aberta.
                     *
                     * Se já estiver fechada, procura a próxima disponível.
                     */
                    $anoMesParcela = Fatura::proximoAnoMesAberto(
                        $cartao,
                        $mesParcela
                    );
                }


                /*
                 * Atualiza o objeto Carbon para o mês que realmente
                 * será usado pela parcela.
                 */
                $mesParcela = Carbon::createFromFormat(
                    'Y-m-d',
                    $anoMesParcela . '-01'
                );


                /*
                 * =============================================================
                 * Daqui para baixo continua seu código normalmente.
                 * =============================================================
                 */

                $valorParcela = $valorBase;

                if ($i <= $diferenca * 100) {
                    $valorParcela += 0.01;
                }

                $despesa = new Despesa();

                $despesa->Descricao = $parcelada
                    ? "{$descricaoOriginal} ({$i}/{$numParcelas})"
                    : $descricaoOriginal;

                $despesa->Valor = $valorParcela;

                $despesa->ValorTotal = $valorTotal;

                $despesa->Parcela = $parcelada
                    ? $i
                    : null;

                $despesa->TotalParcelas = $parcelada
                    ? $numParcelas
                    : null;

                /*
                 * A data continua sendo a data real da compra.
                 */
                $despesa->Data = $dataCompra->format('Y-m-d');

                $despesa->ID_Conta = $cartao->ID_Conta;

                $despesa->ID_Categoria = $request->Categoria;

                $despesa->Efetivada = 0;

                $despesa->save();


                /*
                 * Cria o relacionamento da despesa com a fatura.
                 */
                $fatura = new Fatura();

                $fatura->ID_Cartao = $cartao->ID_Cartao;

                $fatura->ID_Despesa = $despesa->ID_Despesa;

                $fatura->Fechada = 0;

                /*
                 * ESTA é a informação que determina em qual
                 * fatura a despesa aparece.
                 */
                $fatura->Ano_Mes = $anoMesParcela;

                $fatura->save();
            }
        });


        /*
         * Depois do cadastro abrimos exatamente
         * a fatura onde entrou a primeira parcela.
         */
        return redirect()->route('cartoes.fatura', [
            'ID_Cartao' => $cartao->ID_Cartao,
            'Ano_Mes'   => $Ano_Mes,
        ]);
    }

    /**
     * Atualiza o recurso de cartão especificado no armazenamento.
     */
    public function update(Request $request, Cartao $cartao)
    {
        // Implemente a lógica para atualizar um cartão
    }

    /**
     * Atualiza uma despesa de fatura.
     */
    public function update_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::where('ID_Despesa', $request->ID_Despesa)->first();

        $novoAnoMes = $request->Ano . '-' . str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);

        $faturaNova = Fatura::where('ID_Cartao', $request->ID_Cartao)
            ->where('Ano_Mes', $novoAnoMes)
            ->where('ID_Despesa', '!=', $despesa->ID_Despesa)
            ->first();

        if ($faturaNova && $faturaNova->Fechada) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'A fatura de ' . $novoAnoMes . ' já está finalizada. Para alterar esta despesa, escolha um mês com fatura em aberto.'
                ]);
        }

        $despesa->Data = implode("-", array_reverse(explode("/", $request->Data)));
        $despesa->Descricao = $request->Descricao;
        $despesa->Valor = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $despesa->ID_Categoria = $request->Categoria;

        $fatura->Ano_Mes = $novoAnoMes;

        $despesa->save();
        $fatura->save();

        return redirect()->route('cartoes.fatura', [
            'ID_Cartao' => $request->ID_Cartao,
            'Ano_Mes'   => $novoAnoMes,
        ]);
    }
}
