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
        // Termo digitado pelo usuário (ex.: "uber")
        $q = trim((string) $request->query('q', ''));

        // ID do cartão selecionado no formulário (opcional, mas melhora MUITO a relevância)
        $idCartao = $request->query('ID_Cartao');

        // Limite defensivo para não retornar “um caminhão” de dados
        $limit = (int) $request->query('limit', 15);
        $limit = max($limit, 1);
        $limit = min($limit, 50);

        // Query base: só despesas que estão ligadas na fatura (cartão)
        $query = DB::table('despesa')
            ->join('fatura', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->selectRaw('TRIM(despesa.Descricao) as Descricao, COUNT(*) as total')
            ->whereNotNull('despesa.Descricao')
            ->whereRaw("TRIM(despesa.Descricao) <> ''");

        // Se o usuário selecionou um cartão, filtramos por ele
        if (!empty($idCartao)) {
            $query->where('fatura.ID_Cartao', (int) $idCartao);
        }

        // Se veio termo, aplica LIKE
        if ($q !== '') {
            $query->where('despesa.Descricao', 'like', '%' . $q . '%');
        }

        // Agrupa por descrição “limpa”, ordena por mais frequentes, limita e retorna só as descrições
        $descricoes = $query
            ->groupBy(DB::raw('TRIM(despesa.Descricao)'))
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('Descricao');

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
        $Ano_Mes = $request->Ano_Mes ?: Carbon::now()->format('Y-m');

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
     * Exibe o formulário para criar uma nova despesa de fatura.
     */
    public function new_despesa()
    {
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::all();
        $categorias = (new \App\Models\Categoria)->show('D');

        return view('fatura_despesaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
            'cartoes' => $cartoes,
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
        $cartoes = collect($cartoesModel->show($Ano_Mes));


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
     * Salva uma nova despesa parcelada ou não para uma fatura.
     */
    public function store_despesa(Request $request)
    {
        $Ano = $request->Ano;
        $Mes = str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);
        $Ano_Mes = $Ano . '-' . $Mes;

        $faturaExistente = Fatura::where('ID_Cartao', $request->ID_Cartao)
            ->where('Ano_Mes', $Ano_Mes)
            ->first();

        if ($faturaExistente && $faturaExistente->Fechada) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Fatura ' . $Ano_Mes . ' já está finalizada. Não é possível adicionar novas despesas.']);
        }

        $cartao = Cartao::find($request->ID_Cartao);
        $descricaoOriginal = $request->Descricao;
        $valorStr = $request->Valor;
        $valorTotal = floatval(str_replace(",", ".", str_replace(".", "", str_replace("R$ ", "", $valorStr))));
        $data = implode("-", array_reverse(explode("/", $request->Data)));
        $parcelada = $request->Parcelada === 'sim';
        $numParcelas = $parcelada ? max((int) $request->NumeroParcelas, 1) : 1;

        $valorBase = floor(($valorTotal / $numParcelas) * 100) / 100;
        $diferenca = round($valorTotal - ($valorBase * $numParcelas), 2);

        $dataBaseParcela = Carbon::createFromDate((int) $request->Ano, (int) $request->Mes, 1);

        for ($i = 1; $i <= $numParcelas; $i++) {
            $valorParcela = $valorBase;
            if ($i <= $diferenca * 100) {
                $valorParcela += 0.01;
            }

            $dataParcela = $dataBaseParcela->copy()->addMonths($i - 1);
            $anoMesParcela = $dataParcela->format('Y-m');

            $faturaParcelaExistente = Fatura::where('ID_Cartao', $request->ID_Cartao)
                ->where('Ano_Mes', $anoMesParcela)
                ->first();

            if ($faturaParcelaExistente && $faturaParcelaExistente->Fechada) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['Fatura ' . $anoMesParcela . ' já está finalizada. Não é possível adicionar novas despesas.']);
            }

            $despesa = new Despesa();
            $despesa->Descricao = $parcelada ? "{$descricaoOriginal} ({$i}/{$numParcelas})" : $descricaoOriginal;
            $despesa->Valor = $valorParcela;
            $despesa->ValorTotal = $valorTotal;
            $despesa->Parcela = $parcelada ? $i : null;
            $despesa->TotalParcelas = $parcelada ? $numParcelas : null;
            $despesa->Data = $data;
            $despesa->ID_Conta = $cartao->ID_Conta;
            $despesa->ID_Categoria = $request->Categoria;
            $despesa->Efetivada = 0;
            $despesa->save();

            $fatura = new Fatura();
            $fatura->ID_Cartao = $request->ID_Cartao;
            $fatura->ID_Despesa = $despesa->ID_Despesa;
            $fatura->Fechada = 0;
            $fatura->Ano_Mes = $anoMesParcela;
            $fatura->save();
        }

        return redirect()->route('cartoes.fatura', [
            'ID_Cartao' => $request->ID_Cartao,
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
