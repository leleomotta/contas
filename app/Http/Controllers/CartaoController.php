<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;

class CartaoController extends Controller
{
    public function new_despesa(){
        $contas = (new \App\Models\Conta)->showAll();

        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        return view('fatura_despesaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }
    public function fatura(Request $request)
    {

        if (is_null($request->Ano_Mes) ) {
            $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        else
        {
            $Ano_Mes = $request->Ano_Mes;
        }

        $fatura = (new \App\Models\Fatura)->show($Ano_Mes,$request->ID_Cartao);

        return view('faturaListar', [
            'faturas' => $fatura
        ]);
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
        $cartao = new Cartao();

        $cartao->Nome = $request->Nome;
        $cartao->Bandeira = $request->Bandeira;
        $cartao->ID_Conta = $request->Conta;
        $cartao->Cor = $request->corCartao;

        $cartao->save();

        return redirect()->route('cartoes.showAll');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cartao $cartao)
    {
        //
    }

    public function showAll(){
        Paginator::useBootstrap();
        $cartoes = Cartao::paginate(999);
        //$contas = Conta::paginate((Session::get('configuracao'))->Paginacao);
        //Session::forget('filtros');

        return view('cartaoListar', [
            'cartoes' => $cartoes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cartao $cartao)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cartao $cartao)
    {
        //
    }

    public function new(){
        $contas = (new \App\Models\Conta)->showAll();

        return view('cartaoCriar', [
            'contas' => $contas,
        ]);

    }
}
