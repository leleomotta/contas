<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DespesaController extends Controller
{
    public function efetiva(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $despesa->Efetivada = !$despesa->Efetivada;
        $despesa->save();
        $dateFilter = $request->date_filter;
        if (is_null($dateFilter) ) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        $url ='/despesas?date_filter=' . $dateFilter;
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
        $despesa = new Despesa();

        $despesa->Descricao = $request->Descricao;
        $despesa->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));
        $despesa->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $despesa->ID_Conta = $request->Conta;
        $despesa->ID_Categoria = $request->Categoria;

        $request["Efetivada"] = (isset($request["Efetivada"]))?1:0;
        $despesa->Efetivada = $request->Efetivada;

        $despesa->save();

        $url ='/despesas?date_filter=' . Carbon::now()->isoFormat('Y') . '-' .
        Carbon::now()->isoFormat('MM');
        return redirect::to($url);
        //return redirect()->route('receitas.showAll');

    }

    /**
     * Display the specified resource.
     */
    public function show(Despesa $despesa)
    {
        //
    }

    public function showAll(Request $request){
        $contas = (new \App\Models\Conta)->showAll();

        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        $dateFilter = $request->date_filter;

        if (is_null($dateFilter) ) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
            Carbon::now()->isoFormat('MM');
        }
        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        $despesas = (new \App\Models\Despesa)->show($start_date, $end_date);

        return view('despesaListar', [
            'despesas' => $despesas,
            'pendente' => $despesas->where('Efetivada','=',0)->sum('Valor'),
            'pago' => $despesas->where('Efetivada','=',1)->sum('Valor'),
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

        $contas = (new \App\Models\Conta)->showAll();

        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        $despesas = (new \App\Models\Despesa)->filter($categoria, $conta, $texto, $start_date, $end_date);

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
     * Update the specified resource in storage.
     */

    public function edit(int $ID_Despesa) {

        $despesa = Despesa::find($ID_Despesa);

        $contas = (new \App\Models\Conta)->showAll();

        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        return view('despesaEditar', [
            'despesa' => $despesa,
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    public function update(Request $request, int $ID_Despesa)
    {
        $despesa = Despesa::find($ID_Despesa);
        $despesa->Descricao = $request->Descricao;
        $despesa->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));
        $despesa->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $despesa->ID_Conta = $request->Conta;
        $despesa->ID_Categoria = $request->Categoria;

        $request["Efetivada"] = (isset($request["Efetivada"]))?1:0;
        $despesa->Efetivada = $request->Efetivada;

        $despesa->save();

        return redirect()->route('despesas.showAll');


    }

    /**
     * Remove the specified resource from storage.
     */
    //public function destroy(Receita $receita)
    public function destroy(int $ID_Despesa)
    {
        $despesa = Despesa::find($ID_Despesa);
        try {
            DB::beginTransaction();

            $despesa->delete();

            DB::commit();
            /*return redirect()->route('receitas.showAll', [
                'page' => Request::capture()->page
            ]);*/
            $url ='/despesas?date_filter=' . \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                \Carbon\Carbon::now()->isoFormat('MM');
            return redirect::to($url);

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function new(){
        $contas = (new \App\Models\Conta)->showAll();

        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        return view('despesaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);

    }
}
