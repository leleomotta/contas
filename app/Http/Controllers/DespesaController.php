<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\Recorrencia;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DespesaController extends Controller
{
    /**
     * Gera o date_filter (YYYY-MM) a partir de uma data (YYYY-MM-DD).
     * Se a data estiver vazia/inválida, usa um fallback (ex: request('date_filter'))
     * e, se ainda assim não existir, usa o mês atual.
     */
    private function dateFilterFromDate(?string $date, ?string $fallbackDateFilter = null): string
    {
        if (!empty($date)) {
            try {
                return Carbon::parse($date)->format('Y-m'); // ex: 2025-12
            } catch (\Exception $e) {
                // cai para fallback
            }
        }

        if (!empty($fallbackDateFilter)) {
            return $fallbackDateFilter;
        }

        return Carbon::now()->format('Y-m');
    }

    /**
     * Redireciona para a listagem de despesas no mês da própria despesa.
     * - Prioridade: mês da Data da despesa
     * - Fallback: date_filter da request
     * - Último fallback: mês atual
     */
    private function redirectToDespesasMonth(Despesa $despesa, Request $request)
    {
        $dateFilter = $this->dateFilterFromDate($despesa->Data, $request->input('date_filter'));

        return redirect()->route('despesas.showAll', [
            'date_filter' => $dateFilter,
        ]);
    }

    /**
     * Monta a URL da listagem de despesas preservando o date_filter (YYYY-MM).
     * Se não vier date_filter, usa como fallback o mês/ano da própria despesa.
     */
    private function despesasListUrl(?string $dateFilter = null, ?string $fallbackDate = null): string
    {
        if (empty($dateFilter)) {
            // Se não veio filtro, tenta usar a data da despesa; se não tiver, usa o mês atual
            $ref = $fallbackDate ? Carbon::parse($fallbackDate) : Carbon::now();
            $dateFilter = $ref->isoFormat('Y') . '-' . $ref->isoFormat('MM');
        }

        return '/despesas?date_filter=' . $dateFilter;
    }

    /**
     * Retorna descrições (únicas) já existentes em "despesa" para autocomplete.
     *
     * Por que assim?
     * - TRIM() evita duplicidade por espaços ("Aluguel" vs "Aluguel ").
     * - GROUP BY retorna apenas valores únicos.
     * - COUNT(*) permite ordenar por "mais usadas" (melhor UX).
     * - limit evita payload gigante.
     *
     * Query params esperados:
     * - q (string): termo digitado (opcional)
     * - limit (int): quantidade máxima (opcional, padrão 15, máximo 50)
     */
    public function descricoes(Request $request)
    {
        // Termo digitado pelo usuário (ex: "alug")
        $q = trim((string) $request->query('q', ''));

        // Limite defensivo (evita retorno absurdo)
        $limit = (int) $request->query('limit', 15);
        $limit = max($limit, 1);
        $limit = min($limit, 50);

        $descricoes = \App\Models\Despesa::query()
            // Seleciona a descrição "limpa" + total de ocorrências (para ordenar)
            ->selectRaw('TRIM(Descricao) as Descricao, COUNT(*) as total')
            ->whereNotNull('Descricao')
            // Ignora strings vazias (ou só espaços)
            ->whereRaw("TRIM(Descricao) <> ''")
            // Se veio termo, filtra por LIKE
            ->when($q !== '', function ($query) use ($q) {
                // LIKE com %termo% para combinar no meio também
                $query->where('Descricao', 'like', '%' . $q . '%');
            })
            // Agrupa por descrição (única)
            ->groupBy(DB::raw('TRIM(Descricao)'))
            // Ordena: mais usadas primeiro
            ->orderByDesc('total')
            // Tira variações e limita
            ->limit($limit)
            // Retorna apenas a coluna Descricao (array simples)
            ->pluck('Descricao');

        return response()->json($descricoes);
    }


    /**
     * Remove a despesa especificada do armazenamento.
     *
     * @param int $ID_Despesa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $ID_Despesa)
    {
        $despesa = Despesa::findOrFail($ID_Despesa);

        // REGRA: despesa EFETIVADA não pode ser excluída
        if ((int) $despesa->Efetivada === 1) {
            return $this->redirectToDespesasMonth($despesa, $request)
                ->with('error', 'Esta despesa está EFETIVADA e não pode ser excluída. Desefetive para excluir.');
        }

        try {
            DB::beginTransaction();

            $despesa->delete();

            DB::commit();

            return $this->redirectToDespesasMonth($despesa, $request)
                ->with('success', 'Despesa excluída com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();

            // Se der erro, volta para o mês da despesa também
            return $this->redirectToDespesasMonth($despesa, $request)
                ->with('error', 'Erro ao excluir despesa.');
        }
    }

    /**
     * Edita a despesa especificada.
     *
     * @param int $ID_Despesa
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, int $ID_Despesa)
    {
        $despesa = Despesa::find($ID_Despesa);

        // REGRA: despesa EFETIVADA não pode ser editada
        if ((int) $despesa->Efetivada === 1) {
            return $this->redirectToDespesasMonth($despesa, $request)
                ->with('error', 'Esta despesa está EFETIVADA e não pode ser editada. Desefetive para alterar.');
        }
        $contas = (new Conta)->showAll();
        $categorias = (new Categoria)->show('D');

        return view('despesaEditar', [
            'despesa' => $despesa,
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    /**
     * Alterna o status de efetivada de uma despesa.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function efetiva(Request $request)
    {
        $despesa = Despesa::findOrFail($request->ID_Despesa);

        // Permite efetivar e desefetivar (regra do sistema)
        $despesa->Efetivada = !$despesa->Efetivada;
        $despesa->save();

        return $this->redirectToDespesasMonth($despesa, $request)
            ->with('success', $despesa->Efetivada ? 'Despesa efetivada com sucesso.' : 'Despesa desefetivada com sucesso.');
    }


    /**
     * Filtra a listagem de despesas com base nos parâmetros da requisição.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function filter(Request $request)
    {
        $start_date = implode("-", array_reverse(explode("/", substr($request->datas, 0, 10))));
        $end_date = implode("-", array_reverse(explode("/", substr($request->datas, 13, 10))));

        $request["chkCategoria"] = (isset($request["chkCategoria"])) ? 1 : 0;
        $request["chkConta"] = (isset($request["chkConta"])) ? 1 : 0;
        $request["chkTexto"] = (isset($request["chkTexto"])) ? 1 : 0;
        $request["chkDatas"] = (isset($request["chkDatas"])) ? 1 : 0;

        $categoria = $request["chkCategoria"] ? $request->categoria : null;
        $conta = $request["chkConta"] ? $request->conta : null;
        $texto = $request["chkTexto"] ? $request->texto : null;

        if (!$request["chkDatas"]) {
            $start_date = '0001-01-01';
            $end_date = '9999-12-31';
        }

        $contas = (new Conta)->showAll();
        $categorias = (new Categoria)->showAll()->where('Tipo','=','D');
        $despesas = (new Despesa)->filter($categoria, $conta, $texto, $start_date, $end_date);

        return view('despesaListar',
            [
                'despesas' => $despesas,
                'pendente' => $despesas->where('Efetivada','=',0)->sum('Valor'),
                'pago' => $despesas->where('Efetivada','=',1)->sum('Valor'),
                'categorias' => $categorias,
                'contas' => $contas,
            ]);
    }

    /**
     * Exibe a listagem de despesas.
     *
     * @return void
     */
    public function index()
    {
        // Esta função está vazia, o showAll está sendo usado para a listagem
    }

    /**
     * Exibe o formulário para criar uma nova despesa.
     *
     * @return \Illuminate\View\View
     */
    public function new()
    {
        $contas = (new Conta)->showAll();
        $categorias = (new Categoria)->show('D');

        return view('despesaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    /**
     * Exibe a listagem completa de despesas.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showAll(Request $request)
    {
        $contas = (new Conta)->showAll();
        $categorias = (new Categoria)->showAll()->where('Tipo','=','D');
        $dateFilter = $request->date_filter;

        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }
        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        //$despesas = (new Despesa)->show($start_date, $end_date);
        $despesas = (new Despesa)->showAgrupado($start_date, $end_date);

        return view('despesaListar', [
            'despesas' => $despesas,
            'pendente' => $despesas->where('Efetivada','=',0)->sum('Valor'),
            'pago' => $despesas->where('Efetivada','=',1)->sum('Valor'),
            'contas' => $contas,
            'categorias' => $categorias
        ]);
    }

    /**
     * Salva uma nova despesa no banco de dados, com suporte a parcelamento.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $valorStr = $request->Valor;
        $valorTotal = floatval(str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $valorStr))));
        $dataInicial = Carbon::createFromFormat('d/m/Y', $request->Data);

        $parcelada = $request->Parcelada === 'sim';
        $numParcelas = $parcelada ? max((int) $request->NumeroParcelas, 1) : 1;

        if ($parcelada) {
            $valorBase = floor(($valorTotal / $numParcelas) * 100) / 100;
            $diferenca = round($valorTotal - ($valorBase * $numParcelas), 2);

            for ($i = 1; $i <= $numParcelas; $i++) {
                $valorParcela = $valorBase;
                if ($i <= $diferenca * 100) {
                    $valorParcela += 0.01;
                }

                $dataParcela = $dataInicial->copy()->addMonths($i - 1);

                $despesa = new Despesa();
                $despesa->Descricao = "{$request->Descricao} ({$i}/{$numParcelas})";
                $despesa->Valor = $valorParcela;
                $despesa->ValorTotal = $valorTotal;
                $despesa->Parcela = $i;
                $despesa->TotalParcelas = $numParcelas;
                $despesa->Data = $dataParcela->format('Y-m-d');
                $despesa->ID_Conta = $request->Conta;
                $despesa->ID_Categoria = $request->Categoria;
                $despesa->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
                $despesa->save();
            }
        } else {
            // Lógica para despesa única
            $despesa = new Despesa();
            $despesa->Descricao = $request->Descricao;
            $despesa->Valor = $valorTotal;
            $despesa->Data = $dataInicial->format('Y-m-d');
            $despesa->ID_Conta = $request->Conta;
            $despesa->ID_Categoria = $request->Categoria;
            $despesa->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
            $despesa->save();
        }

        $dateFilter = $dataInicial->format('Y-m'); // mês da despesa lançada (data inicial)

        return redirect()->route('despesas.showAll', [
            'date_filter' => $dateFilter,
        ]);

    }

    /**
     * Atualiza a despesa especificada no banco de dados.
     *
     * @param Request $request
     * @param int $ID_Despesa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $ID_Despesa)
    {
        $despesa = Despesa::findOrFail($ID_Despesa);

        // REGRA: despesa EFETIVADA não pode ser alterada
        if ((int) $despesa->Efetivada === 1) {
            return $this->redirectToDespesasMonth($despesa, $request)
                ->with('error', 'Esta despesa está EFETIVADA e não pode ser alterada. Desefetive para alterar.');
        }

        $despesa->Descricao = $request->Descricao;
        $despesa->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $despesa->Data = implode("-", array_reverse(explode("/", $request->Data)));
        $despesa->ID_Conta = $request->Conta;
        $despesa->ID_Categoria = $request->Categoria;
        $despesa->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
        $despesa->save();

        return $this->redirectToDespesasMonth($despesa, $request)
            ->with('success', 'Despesa atualizada com sucesso.');
    }


}
