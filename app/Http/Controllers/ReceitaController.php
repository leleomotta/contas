<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReceitaController extends Controller
{
    /**
     * Remove a receita especificada do armazenamento.
     *
     * @param int $ID_Receita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);
        try {
            DB::beginTransaction();

            $receita->delete();

            DB::commit();
            $url ='/receitas?date_filter=' . \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                \Carbon\Carbon::now()->isoFormat('MM');
            return Redirect::to($url);

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    /**
     * Edita a receita especificada.
     *
     * @param int $ID_Receita
     * @return \Illuminate\View\View
     */
    public function edit(int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);
        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->show('R');

        return view('receitaEditar', [
            'receita' => $receita,
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    /**
     * Alterna o status de efetivada de uma receita.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function efetiva(Request $request)
    {
        $receita = Receita::find($request->ID_Receita);
        $receita->Efetivada = !$receita->Efetivada;
        $receita->save();
        $dateFilter = $request->date_filter;
        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }
        $url ='/receitas?date_filter=' . $dateFilter;
        return redirect::to($url);
    }

    /**
     * Filtra a listagem de receitas com base nos parâmetros da requisição.
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

        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','R');
        $receitas = new Receita();

        return view('receitaListar',
            [
                'receitas' => $receitas->filter($categoria, $conta, $texto, $start_date, $end_date),
                'pendente' => $receitas->receitasPendente($categoria, $conta, $texto, $start_date, $end_date),
                'recebido' => $receitas->receitasRecebido($categoria, $conta, $texto, $start_date, $end_date),
                'categorias' => $categorias,
                'contas' => $contas,
            ]);
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
     * Exibe o formulário para criar uma nova receita.
     *
     * @return \Illuminate\View\View
     */
    public function new()
    {
        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->show('R');
        return view('receitaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    /**
     * Exibe a listagem completa de receitas.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showAll(Request $request)
    {
        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','R');

        $dateFilter = $request->date_filter;
        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }

        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();
        $categoria = null;
        $conta = null;
        $texto = null;

        $receitas = new Receita();

        return view('receitaListar', [
            'receitas' => $receitas->show($start_date, $end_date),
            'pendente' => $receitas->receitasPendente($categoria, $conta, $texto, $start_date, $end_date),
            'recebido' => $receitas->receitasRecebido($categoria, $conta, $texto, $start_date, $end_date),
            'contas' => $contas,
            'categorias' => $categorias
        ]);
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Receita $receita
     * @return void
     */
    public function show(Receita $receita)
    {
        // Esta função está vazia
    }

    /**
     * Salva uma nova receita no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $receita = new Receita();

        $receita->Descricao = $request->Descricao;
        $receita->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $receita->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
        $receita->save();

        $url ='/receitas?date_filter=' . Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        return Redirect::to($url);
    }

    /**
     * Atualiza a receita especificada no armazenamento.
     *
     * @param Request $request
     * @param int $ID_Receita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);
        $receita->Descricao = $request->Descricao;
        $receita->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $receita->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
        $receita->save();

        return redirect()->route('receitas.showAll');
    }
}
