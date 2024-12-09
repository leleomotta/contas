<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CartaoController extends Controller
{
    public function destroy_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);
        try {
            DB::beginTransaction();
            $fatura->delete();
            $despesa->delete();

            DB::commit();

            //$url ='/fatura?ID_Cartao=' . $request->ID_Cartao;
            //return redirect::to($url);
            return redirect()->route('cartoes.fatura',array('ID_Cartao' => $request->ID_Cartao) );

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function store_despesa(Request $request){
        // despesa
        //dd($request);
        $cartao = Cartao::find($request->ID_Cartao);

        $despesa = new Despesa();

        $despesa->Descricao = $request->Descricao;
        $despesa->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));
        $despesa->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $despesa->ID_Conta = $cartao->ID_Conta;
        $despesa->ID_Categoria = $request->Categoria;
        $request["Efetivada"] = 0;
        $despesa->save();

        $fatura = new Fatura();
        $fatura->ID_Cartao = $request->ID_Cartao;
        $fatura->ID_Despesa = $despesa->ID_Despesa;
        $fatura->Fechada = 0;
        $fatura->Ano_Mes = $request->Ano . '-' . str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);

        $fatura->save();

        //$url ='/fatura?ID_Cartao=' . $request->ID_Cartao;
        //return redirect::to($url);
        return redirect()->route('cartoes.fatura',array('ID_Cartao' => $request->ID_Cartao) );
    }

    public function edit_despesa(Request $request) {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);

        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        return view('fatura_despesaEditar', [
            'despesa' => $despesa,
            'fatura' => $fatura,
            'categorias' => $categorias
        ]);
    }

    public function update_despesa(Request $request){
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);

        $despesa->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $despesa->Descricao = $request->Descricao;
        $despesa->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));

        $despesa->ID_Categoria = $request->Categoria;

        $fatura->Ano_Mes = $request->Ano . '-' . str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);

        $despesa->save();
        $fatura->save();

        return redirect()->route('cartoes.fatura',array('ID_Cartao' => $request->ID_Cartao) );
    }

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

        //$fatura = (new \App\Models\Fatura)->show($Ano_Mes,$request->ID_Cartao);
        $fatura = new Fatura();

        return view('faturaListar', [
            'faturas' => $fatura->show($Ano_Mes,$request->ID_Cartao),
            'totalFatura' => $fatura->totalFatura($Ano_Mes,$request->ID_Cartao)
        ]);
    }

    public function fatura_fechar(Request $request)
    {
        if (is_null($request->Ano_Mes) ) {
            $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        else
        {
            $Ano_Mes = $request->Ano_Mes;
        }
        $ID_Cartao = $request->ID_Cartao;

        (new \App\Models\Fatura)->fatura_fechar($Ano_Mes,$ID_Cartao);

        //$fatura = (new \App\Models\Fatura)->show($Ano_Mes,$request->ID_Cartao);
        $fatura = new Fatura();

        return view('faturaListar', [
            'faturas' => $fatura->show($Ano_Mes,$ID_Cartao),
            'totalFatura' => $fatura->totalFatura($Ano_Mes,$ID_Cartao)
        ]);

    }

    public function fatura_reabrir(Request $request)
    {
        if (is_null($request->Ano_Mes) ) {
            $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        else
        {
            $Ano_Mes = $request->Ano_Mes;
        }
        $ID_Cartao = $request->ID_Cartao;

        (new \App\Models\Fatura)->fatura_reabrir($Ano_Mes,$ID_Cartao);

        //$fatura = (new \App\Models\Fatura)->show($Ano_Mes,$request->ID_Cartao);
        $fatura = new Fatura();

        return view('faturaListar', [
            'faturas' => $fatura->show($Ano_Mes,$ID_Cartao),
            'totalFatura' => $fatura->totalFatura($Ano_Mes,$ID_Cartao)
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
        $Ano_Mes = '2024-11';
        $cartoes = new Cartao();

        return view('cartaoListar', [
            'cartoes' => $cartoes->show($Ano_Mes)
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
