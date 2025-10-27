<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Categoria, Conta, Cartao, Despesa, Receita};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    /**
     * Função privada para construir a query de despesas.
     */
    private function buildDespesasQuery($dataInicio, $dataFim, $filtroCategorias, $filtroContas, $filtroCartoes)
    {
        return Despesa::select(
            'despesa.Data',
            DB::raw("CAST(despesa.Descricao AS CHAR CHARACTER SET utf8mb4) as Descricao"),
            'despesa.Valor',
            'despesa.ID_Categoria',
            'despesa.ID_Conta',
            'fatura.ID_Cartao',
            DB::raw("CAST('D' AS CHAR CHARACTER SET utf8mb4) as Tipo"),
            DB::raw("CAST(categoria.Nome AS CHAR CHARACTER SET utf8mb4) as Categoria_Nome"),
            DB::raw("CAST(CONCAT(IFNULL(conta.Nome, ''), ' - ', IFNULL(conta.Banco, '')) AS CHAR CHARACTER SET utf8mb4) as Conta_Nome"),
            DB::raw("CAST(cartao.Nome AS CHAR CHARACTER SET utf8mb4) as Cartao_Nome")
        )
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->leftJoin('fatura', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->leftJoin('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->whereBetween('despesa.Data', [$dataInicio, $dataFim])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('despesa.ID_Categoria', $filtroCategorias))
            ->when($filtroContas || $filtroCartoes, function ($q) use ($filtroContas, $filtroCartoes) {
                $q->where(function ($sub) use ($filtroContas, $filtroCartoes) {
                    if ($filtroContas) $sub->orWhereIn('despesa.ID_Conta', $filtroContas);
                    if ($filtroCartoes) $sub->orWhereIn('fatura.ID_Cartao', $filtroCartoes);
                });
            });
    }

    /**
     * Exibe a página de análise avançada.
     */
    public function analitico(Request $request)
    {
        // --- 1. CARREGAR DADOS DOS FILTROS ---
        $categorias = Categoria::orderBy('Nome')->get();
        $contas = Conta::orderBy('Nome')->get();
        $cartoes = Cartao::orderBy('Nome')->get();

        // --- 2. PARSE DOS FILTROS DE ENTRADA ---
        $dataInicio = Carbon::createFromFormat('d/m/Y', $request->input('data_inicio', Carbon::now()->startOfYear()->format('d/m/Y')))->startOfDay();
        $dataFim = Carbon::createFromFormat('d/m/Y', $request->input('data_fim', Carbon::now()->endOfYear()->format('d/m/Y')))->endOfDay();
        $tipo = $request->input('tipo', 'todos');
        $agrupar = $request->input('agrupar', 'month');
        $filtroCategorias = $request->input('categorias');
        $filtroContas = $request->input('contas');
        $filtroCartoes = $request->input('cartoes');

        // --- 3. CONSULTA BASE DE RECEITAS ---
        $queryReceitas = Receita::select(
            'receita.Data',
            DB::raw("CAST(receita.Descricao AS CHAR CHARACTER SET utf8mb4) as Descricao"),
            'receita.Valor',
            'receita.ID_Categoria',
            'receita.ID_Conta',
            DB::raw('NULL as ID_Cartao'),
            DB::raw("CAST('R' AS CHAR CHARACTER SET utf8mb4) as Tipo"),
            DB::raw("CAST(categoria.Nome AS CHAR CHARACTER SET utf8mb4) as Categoria_Nome"),
            DB::raw("CAST(CONCAT(conta.Nome, ' - ', conta.Banco) AS CHAR CHARACTER SET utf8mb4) as Conta_Nome"),
            DB::raw("CAST(NULL AS CHAR CHARACTER SET utf8mb4) as Cartao_Nome")
        )
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->whereBetween('receita.Data', [$dataInicio, $dataFim])
            ->when($filtroCategorias, fn ($q) => $q->whereIn('receita.ID_Categoria', $filtroCategorias))
            ->when($filtroContas, fn ($q) => $q->whereIn('receita.ID_Conta', $filtroContas))
            ->when($filtroCartoes, fn ($q) => $q->whereRaw('1 = 0'));

        // --- 4. CONSULTA BASE DE DESPESAS (Período Atual) ---
        $queryDespesas = $this->buildDespesasQuery($dataInicio, $dataFim, $filtroCategorias, $filtroContas, $filtroCartoes);

        // --- 5. COMBINA AS CONSULTAS (UNION) ---
        if ($tipo == 'R') {
            $queryUnion = $queryReceitas;
        } elseif ($tipo == 'D') {
            $queryUnion = $queryDespesas;
        } else {
            $queryUnion = (clone $queryReceitas)->unionAll($queryDespesas);
        }

        // --- 6. PREPARA DADOS PARA "TODOS OS LANÇAMENTOS" (Tabela) ---
        $detalhadoData = (clone $queryUnion)->orderBy('Data', 'desc')->get();

        // --- 7. PREPARA DADOS PARA "EVOLUÇÃO FINANCEIRA" (Gráfico) ---
        $dateFormat = match ($agrupar) {
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
            'year' => '%Y'
        };

        $receitasAgrupadas = (clone $queryReceitas)
            ->select(DB::raw("DATE_FORMAT(Data, '$dateFormat') as periodo"), DB::raw('SUM(Valor) as total'))
            ->groupBy('periodo')->orderBy('periodo')->get()->pluck('total', 'periodo');

        $despesasAgrupadas = (clone $queryDespesas)
            ->select(DB::raw("DATE_FORMAT(Data, '$dateFormat') as periodo"), DB::raw('SUM(Valor) as total'))
            ->groupBy('periodo')->orderBy('periodo')->get()->pluck('total', 'periodo');

        // *** CORREÇÃO AQUI: ->values() ***
        // Força $labels a ser um array numérico [0 => '2025-01', 1 => '2025-02']
        $labels = $receitasAgrupadas->keys()->merge($despesasAgrupadas->keys())->unique()->sort()->values();

        $evolucaoData = [
            'labels' => $labels, // Agora é um array
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.8)',
                    // *** CORREÇÃO AQUI: ->values() ***
                    // Força 'data' a ser um array numérico [0 => 15000, 1 => 13000]
                    'data' => $labels->map(fn ($label) => $receitasAgrupadas->get($label, 0))->values(),
                ],
                [
                    'label' => 'Despesas',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.8)',
                    // *** CORREÇÃO AQUI: ->values() ***
                    'data' => $labels->map(fn ($label) => $despesasAgrupadas->get($label, 0))->values(),
                ]
            ]
        ];
        // *** FIM DA CORREÇÃO 7 ***


        // --- 8. PREPARA DADOS PARA "TOP 10 DESPESAS" (Gráfico) ---
        $topDespesasQuery = (clone $queryDespesas)
            ->select(
                DB::raw("CAST(categoria.Nome AS CHAR CHARACTER SET utf8mb4) as Categoria_Nome"),
                DB::raw('SUM(Valor) as total')
            )
            ->groupBy('categoria.Nome')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $topDespesasData = [
            // *** CORREÇÃO AQUI: ->values() ***
            'labels' => $topDespesasQuery->pluck('Categoria_Nome')->values(),
            'datasets' => [[
                // *** CORREÇÃO AQUI: ->values() ***
                'data' => $topDespesasQuery->pluck('total')->values(),
                'backgroundColor' => ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997', '#17a2b8', '#007bff', '#6f42c1', '#e83e8c', '#6c757d'],
            ]]
        ];
        // *** FIM DA CORREÇÃO 8 ***

        // --- 9. PREPARA DADOS PARA "COMPARATIVO DE DESPESAS" (Tabela) ---
        $currentDays = $dataFim->diffInDays($dataInicio);
        $prevDataFim = $dataInicio->copy()->subDay();
        $prevDataInicio = $prevDataFim->copy()->subDays($currentDays);

        $currentDespesas = (clone $queryDespesas)
            ->select(
                DB::raw("CAST(categoria.Nome AS CHAR CHARACTER SET utf8mb4) as Categoria_Nome"),
                DB::raw('SUM(Valor) as total')
            )
            ->groupBy('categoria.Nome')
            ->pluck('total', 'Categoria_Nome');

        $queryDespesasAnterior = $this->buildDespesasQuery($prevDataInicio, $prevDataFim, $filtroCategorias, $filtroContas, $filtroCartoes);

        $previousDespesas = (clone $queryDespesasAnterior)
            ->select(
                DB::raw("CAST(categoria.Nome AS CHAR CHARACTER SET utf8mb4) as Categoria_Nome"),
                DB::raw('SUM(Valor) as total')
            )
            ->groupBy('categoria.Nome')
            ->pluck('total', 'Categoria_Nome');

        $allCategorias = $currentDespesas->keys()->merge($previousDespesas->keys())->unique();

        $comparativoData = $allCategorias->map(function ($categoria) use ($currentDespesas, $previousDespesas) {
            $atual = $currentDespesas->get($categoria, 0);
            $anterior = $previousDespesas->get($categoria, 0);
            $variacao = $atual - $anterior;
            $variacao_perc = ($anterior > 0) ? ($variacao / $anterior) * 100 : ($atual > 0 ? 100 : 0);

            return ['categoria' => $categoria, 'atual' => $atual, 'anterior' => $anterior, 'variacao' => $variacao, 'variacao_perc' => $variacao_perc];
        })->sortByDesc('atual');


        // --- 10. RETORNA A VIEW COM TODOS OS DADOS ---
        return view('relatorioAnalitico', [
            'categorias' => $categorias,
            'contas' => $contas,
            'cartoes' => $cartoes,
            'evolucaoData' => $evolucaoData,
            'comparativoData' => $comparativoData,
            'topDespesasData' => $topDespesasData,
            'detalhadoData' => $detalhadoData,
            'inputs' => $request->all(),
            'periodoAnterior' => $prevDataInicio->format('d/m/Y') . ' - ' . $prevDataFim->format('d/m/Y')
        ]);
    }
}
