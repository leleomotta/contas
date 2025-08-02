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
     * Remove a despesa especificada do armazenamento.
     *
     * @param int $ID_Despesa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $ID_Despesa)
    {
        $despesa = Despesa::find($ID_Despesa);
        try {
            DB::beginTransaction();

            $despesa->delete();

            DB::commit();

            $url ='/despesas?date_filter=' . Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
            return Redirect::to($url);

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    /**
     * Edita a despesa especificada.
     *
     * @param int $ID_Despesa
     * @return \Illuminate\View\View
     */
    public function edit(int $ID_Despesa)
    {
        $despesa = Despesa::find($ID_Despesa);
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
        $despesa = Despesa::find($request->ID_Despesa);
        $despesa->Efetivada = !$despesa->Efetivada;
        $despesa->save();
        $dateFilter = $request->date_filter;
        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }
        $url ='/despesas?date_filter=' . $dateFilter;
        return Redirect::to($url);
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

        $despesas = (new Despesa)->show($start_date, $end_date);

        return view('despesaListar', [
            'despesas' => $despesas,
            'pendente' => $despesas->where('Efetivada','=',0)->sum('Valor'),
            'pago' => $despesas->where('Efetivada','=',1)->sum('Valor'),
            'contas' => $contas,
            'categorias' => $categorias
        ]);
    }

    /**
     * Salva uma nova despesa no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $despesa = new Despesa();

        $despesa->Descricao = $request->Descricao;
        $despesa->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $despesa->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $despesa->ID_Conta = $request->Conta;
        $despesa->ID_Categoria = $request->Categoria;

        $despesa->Efetivada = (isset($request->Efetivada)) ? 1 : 0;

        $despesa->save();

        $url ='/despesas?date_filter=' . Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        return Redirect::to($url);
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
        $despesa = Despesa::find($ID_Despesa);
        $despesa->Descricao = $request->Descricao;
        $despesa->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $despesa->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $despesa->ID_Conta = $request->Conta;
        $despesa->ID_Categoria = $request->Categoria;

        $despesa->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
        $despesa->save();

        return redirect()->route('despesas.showAll');
    }

    /**
     * Exibe o formulário para criar uma nova recorrência.
     *
     * @return \Illuminate\View\View
     */
    public function recorrencias_new()
    {
        $categorias = Categoria::orderBy('Nome')->get();
        $contas = Conta::orderBy('Nome')->get();
        $cartoes = Cartao::orderBy('Nome')->get();

        return view('recorrenciaCriar', compact('categorias', 'contas', 'cartoes'));
    }

    /**
     * Salva uma nova recorrência no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recorrencias_store(Request $request)
    {
        $recorrencia = new Recorrencia();

        $recorrencia->Descricao = $request->Descricao;
        $recorrencia->Valor = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $recorrencia->ID_Categoria = $request->ID_Categoria;
        $recorrencia->ID_Conta = ($request->TipoPagamento === 'conta') ? $request->ID_Conta : null;
        $recorrencia->ID_Cartao = ($request->TipoPagamento === 'cartao') ? $request->ID_Cartao : null;
        $recorrencia->Dia_vencimento = $request->DiaVencimento;
        $recorrencia->Periodicidade = $request->Periodicidade;
        $recorrencia->Data_inicio = implode("-", array_reverse(explode("/", $request->DataInicio)));

        if (!empty($request->DataFim)) {
            $recorrencia->Data_fim = implode("-", array_reverse(explode("/", $request->DataFim)));
        } else {
            $recorrencia->Data_fim = null;
        }

        $recorrencia->Ativa = isset($request->Ativa) ? 1 : 0;

        $recorrencia->save();

        $url = '/recorrencias';
        return redirect($url);
    }
}
