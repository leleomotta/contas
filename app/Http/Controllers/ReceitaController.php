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
    public function efetiva(Request $request)
    {
        $receita = Receita::find($request->ID_Receita);
        $receita->Efetivada = !$receita->Efetivada;
        $receita->save();
        $dateFilter = $request->date_filter;
        if (is_null($dateFilter) ) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        $url ='/receitas?date_filter=' . $dateFilter;
        return redirect::to($url);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $receita = new Receita();

        $receita->Descricao = $request->Descricao;
        $receita->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $request["Efetivada"] = (isset($request["Efetivada"]))?1:0;
        $receita->Efetivada = $request->Efetivada;

        $receita->save();

        $url ='/receitas?date_filter=' . Carbon::now()->isoFormat('Y') . '-' .
        Carbon::now()->isoFormat('MM');
        return redirect::to($url);
        //return redirect()->route('receitas.showAll');

    }

    /**
     * Display the specified resource.
     */
    public function show(Receita $receita)
    {
        //
    }

    public function showAll(Request $request){
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->where('Tipo','R');
            $query->orderBy('Nome','ASC');
        })->get();

        $dateFilter = $request->date_filter;
        if (is_null($dateFilter) ) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
            Carbon::now()->isoFormat('MM');
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
            'pendente' => $receitas->pendente($categoria, $conta, $texto, $start_date, $end_date),
            'recebido' => $receitas->recebido($categoria, $conta, $texto, $start_date, $end_date),
            'contas' => $contas,
            'categorias' => $categorias
        ]);

    }

    public function filter(Request $request){
        $start_date = implode("-",array_reverse(explode("/",substr($request->datas,0,10) )));
        $end_date = implode("-",array_reverse(explode("/",substr($request->datas,13,10) )));

        $request["chkCategoria"] = (isset($request["chkCategoria"]))?1:0;
        $request["chkConta"] = (isset($request["chkConta"]))?1:0;
        $request["chkTexto"] = (isset($request["chkTexto"]))?1:0;
        $request["chkDatas"] = (isset($request["chkDatas"]))?1:0;

        if($request["chkCategoria"]){
            $categoria = $request->categoria;
        }
        else{
            $categoria = $request->null;
        }

        if($request["chkConta"]){
            $conta = $request->conta;
        }
        else{
            $conta = $request->null;
        }

        if($request["chkTexto"]){
            $texto = $request->texto;
        }
        else{
            $texto = $request->null;
        }

        if(! $request["chkDatas"]){
            $start_date = '0001-01-01';
            $end_date = '9999-12-31';
        }

        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->where('Tipo','R');
            $query->orderBy('Nome','ASC');
        })->get();

        $receitas = new Receita();

        return view('receitaListar',
            [
                'receitas' => $receitas->filter($categoria, $conta, $texto, $start_date, $end_date),
                'pendente' => $receitas->pendente($categoria, $conta, $texto, $start_date, $end_date),
                'recebido' => $receitas->recebido($categoria, $conta, $texto, $start_date, $end_date),
                'categorias' => $categorias,
                'contas' => $contas,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */

    public function edit(int $ID_Receita) {

        $receita = Receita::find($ID_Receita);

        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->where('Tipo','R');
            $query->orderBy('Nome','ASC');
        })->get();

        return view('receitaEditar', [
            'receita' => $receita,
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    public function update(Request $request, int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);
        $receita->Descricao = $request->Descricao;
        $receita->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $request["Efetivada"] = (isset($request["Efetivada"]))?1:0;
        $receita->Efetivada = $request->Efetivada;

        $receita->save();

        return redirect()->route('receitas.showAll');


    }

    /**
     * Remove the specified resource from storage.
     */
    //public function destroy(Receita $receita)
    public function destroy(int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);
        try {
            DB::beginTransaction();

            $receita->delete();

            DB::commit();
            /*return redirect()->route('receitas.showAll', [
                'page' => Request::capture()->page
            ]);*/
            $url ='/receitas?date_filter=' . \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                \Carbon\Carbon::now()->isoFormat('MM');
            return redirect::to($url);

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function new(){
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        //$categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','R');
        $categorias = (new \App\Models\Categoria)->show('R');
        return view('receitaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);

    }
}
