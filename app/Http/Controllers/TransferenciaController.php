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
     * Remove a transferência especificada do armazenamento.
     *
     * @param int $ID_Transferencia
     * @return \Illuminate\Http\RedirectResponse
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

    /**
     * Edita a transferência especificada.
     *
     * @param int $ID_Transferencia
     * @return \Illuminate\View\View
     */
    public function edit(int $ID_Transferencia)
    {
        $transferencia = Transferencia::find($ID_Transferencia);
        $contas = (new \App\Models\Conta)->showAll();

        return view('transferenciaEditar', [
            'transferencia' => $transferencia,
            'contas' => $contas
        ]);
    }

    /**
     * Exibe a listagem do recurso.
     *
     * @return void
     */
    public function index()
    {
        // Esta função está vazia, o showAll é utilizado
    }

    /**
     * Exibe o formulário para criar uma nova transferência.
     *
     * @return \Illuminate\View\View
     */
    public function new()
    {
        $contas = (new \App\Models\Conta)->showAll();

        return view('transferenciaCriar', [
            'contas' => $contas
        ]);
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Transferencia $transferencia
     * @return void
     */
    public function show(Transferencia $transferencia)
    {
        // Esta função está vazia
    }

    /**
     * Exibe a listagem completa de transferências.
     *
     * @return \Illuminate\View\View
     */
    public function showAll()
    {
        $transferencias = Transferencia::query()
            // ✅ Garante que existe conta de origem relacionada
            ->whereHas('contaOrigem')
            // ✅ Garante que existe conta de destino relacionada
            ->whereHas('contaDestino')
            // ✅ Carrega as relações (evita N+1)
            ->with(['contaOrigem', 'contaDestino'])
            ->orderBy('Data', 'DESC')
            ->orderBy('ID_Transferencia', 'DESC')
            ->get();

        // ✅ Monta as abas sem nulos e com índices 0..N
        $contasOrigem = $transferencias->pluck('contaOrigem')->filter()->unique('ID_Conta')->values();
        $contasDestino = $transferencias->pluck('contaDestino')->filter()->unique('ID_Conta')->values();

        return view('transferenciaListar', compact('transferencias', 'contasOrigem', 'contasDestino'));
    }

    /**
     * Salva uma nova transferência no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $transferencia = new Transferencia();

        $transferencia->Data = implode("-", array_reverse(explode("/", $request->Data)));
        $transferencia->Valor = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $transferencia->ID_Conta_Origem = $request->Conta_Origem;
        $transferencia->ID_Conta_Destino = $request->Conta_Destino;

        $transferencia->save();
        return redirect()->route('transferencias.showAll');
    }

    /**
     * Atualiza a transferência especificada no armazenamento.
     *
     * @param Request $request
     * @param int $ID_Transferencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $ID_Transferencia)
    {
        $transferencia = Transferencia::find($ID_Transferencia);

        $transferencia->Data = implode("-", array_reverse(explode("/", $request->Data)));
        $transferencia->Valor = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $transferencia->ID_Conta_Origem = $request->Conta_Origem;
        $transferencia->ID_Conta_Destino = $request->Conta_Destino;

        $transferencia->save();

        return redirect()->route('transferencias.showAll');
    }
}
