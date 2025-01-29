<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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
    public function edit(int $ID_Transferencia) {
        $transferencia = Transferencia::find($ID_Transferencia);

        $contas = (new \App\Models\Conta)->showAll();

        return view('transferenciaEditar', [
            'transferencia' => $transferencia,
            'contas' => $contas
        ]);
    }

    public function update(Request $request, int $ID_Transferencia)
    {
        $transferencia = Transferencia::find($ID_Transferencia);

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
     * Remove the specified resource from storage.
     */
    public function destroy(int $ID_Transferencia)
    {
        $transferencia = Transferencia::find($ID_Transferencia);
        try {
            DB::beginTransaction();
            $transferencia->delete();
            DB::commit();
            return redirect()->route('transferencias.showAll');
        } catch (\Exception $e) {
            DB::rollback();
            return back();
        }
    }

    public function new(){
        $contas = (new \App\Models\Conta)->showAll();

        return view('transferenciaCriar', [
            'contas' => $contas
        ]);
    }
}
