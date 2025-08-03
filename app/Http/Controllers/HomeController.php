<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Cria uma nova instância do controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe o painel de controle da aplicação.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Exibe todos os dados do dashboard para o mês selecionado.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showAll(Request $request)
    {
        $dateFilter = $request->date_filter;

        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }
        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        $despesas = (new \App\Models\Despesa)->show($start_date, $end_date);
        $receitas = (new \App\Models\Receita)->show($start_date, $end_date);
        $despesasCartao = (new \App\Models\Despesa)->cartaoAberto(Carbon::createFromDate($start_date)->isoFormat('Y') .
            '-' . Carbon::createFromDate($start_date)->isoFormat('MM'));
        $despesasCartao = $despesasCartao->merge((new \App\Models\Despesa)->cartaoPago($start_date, $end_date, null));

        return view('home', [
            'despesas' => $despesas,
            'receitas' => $receitas,
            'despesasCartao' => $despesasCartao
        ]);
    }
}
