<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Transferencia;
use Illuminate\Http\Request;

class TransferenciaController extends Controller
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
        $transferencia = new Transferencia();

        $transferencia->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $transferencia->Valor =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Valor)));

        $transferencia->ID_Conta_Origem = $request->Conta_Origem;
        $transferencia->ID_Conta_Destino = $request->Conta_Destino;

        $transferencia->save();
        return redirect()->route('transferencias.showAll');
    }

    /**
     * Display the specified resource.
     */
    public function show(transferencia $transferencia)
    {
        //
    }

    public function showAll(){

        $transferencias = new Transferencia();

        return view('transferenciaListar', [
            'transferencias' => $transferencias->showAll(),
            'contasOrigem' => $transferencias->showContaOrigem()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transferencia $transferencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(transferencia $transferencia)
    {
        //
    }

    public function new(){
        $contas = (new \App\Models\Conta)->showAll();

        return view('transferenciaCriar', [
            'contas' => $contas
        ]);

    }
}
