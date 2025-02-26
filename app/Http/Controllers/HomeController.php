<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function showAll(Request $request){
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
        $receitas = (new \App\Models\Receita)->show($start_date, $end_date);


        //$receitas = $receitas->groupBy('NomeCategoria')->map(function ($categoria) { return $categoria->sum('Valor'); });
        //$despesas = $despesas->groupBy('NomeCategoria')->map(function ($categoria) { return $categoria->sum('Valor'); });
        //$bunda = $despesas->values();
        //dd($bunda[11]);
        //$chaves = $despesas->keys();
        //dd($chaves[0]); // "Outros"

        return view('home', [
            'despesas' => $despesas,
            'receitas' => $receitas
        ]);

        /*
        return view('despesaListar', [
            'despesas' => $despesas->sum('Valor'),
            'pendente' => $despesas->where('Efetivada','=',0)->sum('Valor'),
            'pago' => $despesas->where('Efetivada','=',1)->sum('Valor'),
            'contas' => $contas,
            'categorias' => $categorias
        ]);
*/
    }

}
