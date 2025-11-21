<?php

namespace App\Http\Controllers;

// Importa as classes necessárias
use Illuminate\Http\Request; // Para lidar com a requisição HTTP e os filtros
use App\Models\{Categoria, Conta, Cartao, Despesa, Receita}; // Models do seu sistema
use Carbon\Carbon; // Para manipulação de datas
use Illuminate\Support\Facades\DB; // Para usar funções de banco de dados (DB::raw)

class RelatorioController extends Controller
{
    /**
     * Exibe a página de análise avançada com dados filtrados.
     *
     * @param Request $request Contém os filtros enviados pelo formulário (GET)
     * @return \Illuminate\View\View Retorna a view 'relatorioAnalitico' com os dados processados
     */

    public function analitico(Request $request)
    {
        // --- 1. CARREGAR DADOS PARA OS FILTROS ---
        $categorias = Categoria::orderBy('Nome')->get();
        $contas = Conta::orderBy('Nome')->get();
        $cartoes = Cartao::orderBy('Nome')->get();

        // --- 2. PARSE DOS FILTROS ---
        $dataInicio = Carbon::createFromFormat('d/m/Y', $request->input('data_inicio', Carbon::now()->startOfYear()->format('d/m/Y')))->startOfDay();
        $dataFim = Carbon::createFromFormat('d/m/Y', $request->input('data_fim', Carbon::now()->endOfYear()->format('d/m/Y')))->endOfDay();
        $tipo = $request->input('tipo', 'todos');
        $dateFormat = '%Y-%m';
        $phpFormat = 'Y-m';
        $filtroCategorias = $request->input('categorias');
        $filtroContas = $request->input('contas');
        $filtroCartoes = $request->input('cartoes');

        // --- 3. GERAR LABELS (EIXO X) ---
        $labels = collect();
        // O ERRO ESTAVA AQUI: Ao chamar startOfMonth() direto, você alterava as variáveis originais.
        // CORREÇÃO: Usamos copy() para não estragar a dataFim usada nas queries SQL abaixo.
        $periodIterator = new \Carbon\CarbonPeriod(
            $dataInicio->copy()->startOfMonth(),'1 month', $dataFim->copy()->startOfMonth()
        );
        foreach ($periodIterator as $date) {
            $labels->push($date->format($phpFormat));
        }


        // --- 4. PREPARA DADOS PARA "EVOLUÇÃO FINANCEIRA" (COM DEBUG) ---

        // [A] RECEITAS
        $receitasQuery = Receita::select(DB::raw("DATE_FORMAT(Data, '$dateFormat') as periodo"), DB::raw('SUM(Valor) as total'))
            ->whereBetween('Data', [$dataInicio, $dataFim])
            ->where('Efetivada', 1)
            ->when($filtroCategorias, fn ($q) => $q->whereIn('ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('ID_Conta', $filtroContas))
            ->groupBy('periodo');

        // --- DEBUG RECEITAS ---
        // dd('SQL Receitas:', $receitasQuery->toSql(), $receitasQuery->getBindings());

        $receitasAgrupadas = $receitasQuery->pluck('total', 'periodo');


        // [B] DESPESAS SEM CARTÃO (Débito)
        $despesasSemCartaoQuery = DB::table('despesa')
            ->select(DB::raw("DATE_FORMAT(despesa.Data, '$dateFormat') as periodo"), DB::raw('SUM(despesa.Valor) as total'))
            ->whereBetween('despesa.Data', [$dataInicio, $dataFim])
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))->from('fatura')->whereColumn('despesa.ID_Despesa', 'fatura.ID_Despesa');
            })
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('despesa.ID_Conta', $filtroContas))
            ->when($filtroCartoes, fn ($q) => $q->whereRaw('1 = 0'))
            ->groupBy('periodo');

        // --- DEBUG DESPESAS SEM CARTÃO ---
        // dd('SQL Sem Cartão:', $despesasSemCartaoQuery->toSql(), $despesasSemCartaoQuery->getBindings());

        $despesasSemCartaoGrouped = $despesasSemCartaoQuery->pluck('total', 'periodo');


        // [C] DESPESAS CARTÃO PAGO
        $despesasCartaoPagoQuery = DB::table('fatura')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->select(DB::raw("DATE_FORMAT(fatura.Data_fechamento, '$dateFormat') as periodo"), DB::raw('SUM(despesa.Valor) as total'))
            ->where('fatura.Fechada', 1)
            ->whereBetween('fatura.Data_fechamento', [$dataInicio, $dataFim])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('fatura.Conta_fechamento', $filtroContas))
            ->when($filtroCartoes, fn ($q) => $q->whereIn('fatura.ID_Cartao', $filtroCartoes))
            ->groupBy('periodo');

        // --- DEBUG CARTÃO PAGO ---
        // dd('SQL Cartão Pago:', $despesasCartaoPagoQuery->toSql(), $despesasCartaoPagoQuery->getBindings());

        $despesasCartaoPagoGrouped = $despesasCartaoPagoQuery->pluck('total', 'periodo');


        // [D] DESPESAS CARTÃO ABERTO
        $despesasCartaoAbertoQuery = DB::table('fatura')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->select(DB::raw("fatura.Ano_Mes as periodo"), DB::raw('SUM(despesa.Valor) as total'))
            ->whereNull('fatura.Data_fechamento')
            // ATENÇÃO AQUI: Verifique se o formato gerado pelo Carbon bate com o do banco (ex: '2025-12')
            ->whereBetween('fatura.Ano_Mes', [$dataInicio->format('Y-m'), $dataFim->format('Y-m')])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroCartoes, fn ($q) => $q->whereIn('fatura.ID_Cartao', $filtroCartoes))
            ->when($filtroContas, fn ($q) => $q->whereRaw('1 = 0')) // Zera se houver filtro de conta
            ->groupBy('periodo');

        // --- DEBUG CARTÃO ABERTO ---
        // dd('SQL Cartão Aberto:', $despesasCartaoAbertoQuery->toSql(), $despesasCartaoAbertoQuery->getBindings());

        $despesasCartaoAbertoGrouped = $despesasCartaoAbertoQuery->pluck('total', 'periodo');


        // --- DEBUG GERAL DOS RESULTADOS ---
        // Se quiser ver os arrays finais antes de somar, descomente abaixo:
        /*
        dd(
            'Range Datas:', $dataInicio->toDateTimeString(), $dataFim->toDateTimeString(),
            'Sem Cartão:', $despesasSemCartaoGrouped,
            'Cartão Pago:', $despesasCartaoPagoGrouped,
            'Cartão Aberto:', $despesasCartaoAbertoGrouped
        );
        */

        // [E] COMBINA OS TOTAIS DE DESPESAS
        $despesasTotais = $labels->mapWithKeys(function ($mes) use ($despesasSemCartaoGrouped, $despesasCartaoPagoGrouped, $despesasCartaoAbertoGrouped) {
            $total = $despesasSemCartaoGrouped->get($mes, 0)
                + $despesasCartaoPagoGrouped->get($mes, 0)
                + $despesasCartaoAbertoGrouped->get($mes, 0);
            return [$mes => $total];
        });

        $evolucaoData = [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Receitas', 'backgroundColor' => 'rgba(40, 167, 69, 0.8)', 'data' => $labels->map(fn ($l) => $receitasAgrupadas->get($l, 0))->values()],
                ['label' => 'Despesas', 'backgroundColor' => 'rgba(220, 53, 69, 0.8)', 'data' => $despesasTotais->values()]
            ]
        ];

        // --- 5. DADOS PARA TABELA DETALHADA ---

        // Query Receitas (Mantida)
        $queryReceitas = Receita::select(
            'receita.Data',
            DB::raw("CAST(receita.Descricao AS CHAR) as Descricao"),
            'receita.Valor',
            DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"),
            DB::raw("CAST(CONCAT(IFNULL(conta.Nome, 'N/A'), ' - ', IFNULL(conta.Banco, '')) AS CHAR) as Conta_Banco"),
            DB::raw("CAST(NULL AS CHAR) as Cartao_Nome"),
            DB::raw("CAST('R' AS CHAR) as Tipo")
        )
            ->leftJoin('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->whereBetween('receita.Data', [$dataInicio, $dataFim])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('receita.ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('receita.ID_Conta', $filtroContas));

        // Query Despesas SEM Cartão
        $queryDespesasSemCartao = DB::table('despesa')
            ->select(
                'despesa.Data',
                DB::raw("CAST(despesa.Descricao AS CHAR) as Descricao"),
                'despesa.Valor',
                DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"),
                DB::raw("CAST(CONCAT(IFNULL(conta.Nome, ''), ' - ', IFNULL(conta.Banco, '')) AS CHAR) as Conta_Banco"),
                DB::raw("CAST(NULL AS CHAR) as Cartao_Nome"),
                DB::raw("CAST('D' AS CHAR) as Tipo")
            )
            ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->whereBetween('despesa.Data', [$dataInicio, $dataFim])
            ->whereNotExists(function($query) { $query->select(DB::raw(1))->from('fatura')->whereColumn('despesa.ID_Despesa', 'fatura.ID_Despesa'); })
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('despesa.ID_Conta', $filtroContas))
            ->when($filtroCartoes, fn ($q) => $q->whereRaw('1 = 0'));

        // Query Cartão PAGO
        $queryCartaoPago = DB::table('fatura')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->select(
                'fatura.Data_fechamento as Data', // Data efetiva do pagamento
                DB::raw("CAST(despesa.Descricao AS CHAR) as Descricao"),
                'despesa.Valor',
                DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"),
                DB::raw("CAST(CONCAT(IFNULL(conta.Nome, 'N/A'), ' - ', IFNULL(conta.Banco, '')) AS CHAR) as Conta_Banco"),
                DB::raw("CAST(cartao.Nome AS CHAR) as Cartao_Nome"),
                DB::raw("CAST('D' AS CHAR) as Tipo")
            )
            ->where('fatura.Fechada', 1)
            ->whereBetween('fatura.Data_fechamento', [$dataInicio, $dataFim])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('fatura.Conta_fechamento', $filtroContas))
            ->when($filtroCartoes, fn ($q) => $q->whereIn('fatura.ID_Cartao', $filtroCartoes));

        // Query Cartão ABERTO (NOVO)
        $queryCartaoAberto = DB::table('fatura')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->select(
            // Como a data de fechamento é NULL, usamos a data da compra para exibir na tabela,
            // ou montamos uma data ficticia baseada no mês. Vou usar a data da compra para ficar informativo.
                'despesa.Data',
                DB::raw("CAST(despesa.Descricao AS CHAR) as Descricao"),
                'despesa.Valor',
                DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"),
                DB::raw("CAST('Fatura em Aberto' AS CHAR) as Conta_Banco"), // Indicativo visual
                DB::raw("CAST(cartao.Nome AS CHAR) as Cartao_Nome"),
                DB::raw("CAST('D' AS CHAR) as Tipo")
            )
            ->whereNull('fatura.Data_fechamento')
            ->whereBetween('fatura.Ano_Mes', [$dataInicio->format('Y-m'), $dataFim->format('Y-m')])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroCartoes, fn ($q) => $q->whereIn('fatura.ID_Cartao', $filtroCartoes))
            ->when($filtroContas, fn ($q) => $q->whereRaw('1 = 0')); // Zera se filtrar conta

        // União Final
        if ($tipo == 'R') {
            $detalhadoData = $queryReceitas->orderBy('Data', 'desc')->get();
        } elseif ($tipo == 'D') {
            $detalhadoData = $queryDespesasSemCartao
                ->unionAll($queryCartaoPago)
                ->unionAll($queryCartaoAberto)
                ->orderBy('Data', 'desc')->get();
        } else {
            $detalhadoData = $queryReceitas
                ->unionAll($queryDespesasSemCartao)
                ->unionAll($queryCartaoPago)
                ->unionAll($queryCartaoAberto)
                ->orderBy('Data', 'desc')->get();
        }

        // --- 6. INDICADORES (HELPER ATUALIZADO) ---
        $getDespesasPorCategoria = function($startDate, $endDate) use ($filtroCategorias, $filtroContas, $filtroCartoes) {
            // 1. Sem Cartão
            $semCartao = DB::table('despesa')
                ->select(DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"), DB::raw('SUM(despesa.Valor) as total'))
                ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
                ->whereBetween('despesa.Data', [$startDate, $endDate])
                ->whereNotExists(function($q) { $q->select(DB::raw(1))->from('fatura')->whereColumn('despesa.ID_Despesa', 'fatura.ID_Despesa'); })
                ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
                ->when($filtroContas, fn ($q) => $q->whereIn('despesa.ID_Conta', $filtroContas))
                ->when($filtroCartoes, fn ($q) => $q->whereRaw('1 = 0'))
                ->groupBy('Categoria_Nome')->pluck('total', 'Categoria_Nome');

            // 2. Cartão Pago
            $cartaoPago = DB::table('fatura')
                ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
                ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
                ->select(DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"), DB::raw('SUM(despesa.Valor) as total'))
                ->where('fatura.Fechada', 1)
                ->whereBetween('fatura.Data_fechamento', [$startDate, $endDate])
                ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
                ->when($filtroContas, fn ($q) => $q->whereIn('fatura.Conta_fechamento', $filtroContas))
                ->when($filtroCartoes, fn ($q) => $q->whereIn('fatura.ID_Cartao', $filtroCartoes))
                ->groupBy('Categoria_Nome')->pluck('total', 'Categoria_Nome');

            // 3. Cartão Aberto
            $cartaoAberto = DB::table('fatura')
                ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
                ->leftJoin('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
                ->select(DB::raw("CAST(IFNULL(categoria.Nome, 'Sem Categoria') AS CHAR) as Categoria_Nome"), DB::raw('SUM(despesa.Valor) as total'))
                ->whereNull('fatura.Data_fechamento')
                ->whereBetween('fatura.Ano_Mes', [$startDate->format('Y-m'), $endDate->format('Y-m')])
                ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
                ->when($filtroCartoes, fn ($q) => $q->whereIn('fatura.ID_Cartao', $filtroCartoes))
                ->when($filtroContas, fn ($q) => $q->whereRaw('1 = 0'))
                ->groupBy('Categoria_Nome')->pluck('total', 'Categoria_Nome');

            // Soma tudo
            $allCats = $semCartao->keys()->merge($cartaoPago->keys())->merge($cartaoAberto->keys())->unique();
            $finalResult = collect();
            foreach($allCats as $cat) {
                $finalResult[$cat] = $semCartao->get($cat, 0) + $cartaoPago->get($cat, 0) + $cartaoAberto->get($cat, 0);
            }
            return $finalResult;
        };

        // --- 7. GERA INDICADORES E RETORNA VIEW ---
        $currentDespesasCategorias = $getDespesasPorCategoria($dataInicio, $dataFim);

        $topDespesasData = [
            'labels' => $currentDespesasCategorias->sortDesc()->take(10)->keys()->values(),
            'datasets' => [['data' => $currentDespesasCategorias->sortDesc()->take(10)->values(), 'backgroundColor' => ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997', '#17a2b8', '#007bff', '#6f42c1', '#e83e8c', '#6c757d']]]
        ];

        $currentDays = $dataFim->diffInDays($dataInicio);
        $prevDataFim = $dataInicio->copy()->subDay();
        $prevDataInicio = $prevDataFim->copy()->subDays($currentDays);
        $previousDespesas = $getDespesasPorCategoria($prevDataInicio, $prevDataFim);

        $allCategorias = $currentDespesasCategorias->keys()->merge($previousDespesas->keys())->unique();
        $comparativoData = $allCategorias->map(function ($categoria) use ($currentDespesasCategorias, $previousDespesas) {
            $atual = $currentDespesasCategorias->get($categoria, 0);
            $anterior = $previousDespesas->get($categoria, 0);
            $variacao = $atual - $anterior;
            $variacao_perc = ($anterior > 0) ? ($variacao / $anterior) * 100 : ($atual > 0 ? 100 : 0);
            return ['categoria' => $categoria, 'atual' => $atual, 'anterior' => $anterior, 'variacao' => $variacao, 'variacao_perc' => $variacao_perc];
        })->sortByDesc('atual');

        return view('relatorioAnalitico', [
            'categorias' => $categorias, 'contas' => $contas, 'cartoes' => $cartoes,
            'evolucaoData' => $evolucaoData, 'comparativoData' => $comparativoData,
            'topDespesasData' => $topDespesasData, 'detalhadoData' => $detalhadoData,
            'inputs' => $request->all(), 'periodoAnterior' => $prevDataInicio->format('d/m/Y') . ' - ' . $prevDataFim->format('d/m/Y')
        ]);
    }
}
