<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;

class ReceitaController extends Controller
{
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
        return redirect()->route('receitas.showAll');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receita $receita)
    {
        //
    }

    /*
     $events = App\Events->orderBy('start', 'asc')->get()->groupBy(
    function($date) {return $date->start->format('F, Y');}
    );

    $resultado = $receitas
            ->groupBy(
                function($date) {return $date->start->format('F, Y');}
            )
            ->paginate(5);
     */

    public function showAll(Request $request){
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->orderBy('Nome','ASC');
        })->get();

        $dateFilter = $request->date_filter;
        $receitas = new Receita();
        //Paginator::useBootstrap();
        /*
        if (is_null($dateFilter) ) {
            //Session::forget('filtros');
            return view('receitaListar', [
                'receitas' => $receitas->showAll(),
                'contas' => $contas->showAll()
            ]);
        }
        */
        //fiz uma alteração para mostrar só as despesas do mês corrente
        if (is_null($dateFilter) ) {
            //Session::forget('filtros');
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');

            return view('receitaListar', [
                'receitas' => $receitas->show($dateFilter),
                'contas' => $contas,
                'categorias' => $categorias
            ]);
        }
        else {
            return view('receitaListar', [
                'receitas' => $receitas->show($dateFilter),
                'contas' => $contas,
                'categorias' => $categorias
            ]);
        }

        /*
        switch($dateFilter){
            case '2024-04':
                //$query->whereBetween('Data',array(2024-9-1,2024-9-3));
                $query->whereBetween('Data',
                    [
                        Carbon::createFromDate($dt->firstOfMonth())->toDateString(),
                        Carbon::createFromDate($dt->lastOfMonth())->toDateString()
                    ]
                );

                break;
            case 'today':
                $query->whereDate('Data',Carbon::today());
                break;
            case 'yesterday':
                $query->wheredate('Data',Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('Data',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('Data',[Carbon::now()->subWeek(),Carbon::now()]);
                break;
            case 'this_month':
                $query->whereMonth('Data',Carbon::now()->month);
                break;
            case 'last_month':
                $query->whereMonth('Data',Carbon::now()->subMonth()->month);
                break;
            case 'this_year':
                $query->whereYear('Data',Carbon::now()->year);
                break;
            case 'last_year':
                $query->whereYear('Data',Carbon::now()->subYear()->year);
                break;
        }
        */
    }

    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $receitas = Receita::whereDate('Data', '>=', $start_date)
            ->whereDate('Data', '<=', $end_date)
            ->get();

        return view('receitaListar',compact('receitas'));

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receita $receita)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receita $receita)
    {
        //
    }

    public function new(){

        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Descricao','ASC');
        })->get();

        $categorias = Categoria::where(function ($query) {
            $query->select('*');
            $query->orderBy('Nome','ASC');
        })->get();

        return view('receitaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);

    }
}
