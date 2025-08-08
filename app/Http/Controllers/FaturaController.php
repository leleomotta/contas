<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use Illuminate\Http\Request;

class FaturaController extends Controller
{
    /**
     * Remove o recurso especificado do armazenamento.
     *
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fatura $fatura)
    {
        // Implemente a lógica para excluir uma fatura
    }

    /**
     * Exibe a listagem do recurso.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Implemente a lógica para exibir a lista de faturas
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Http\Response
     */
    public function show(Fatura $fatura)
    {
        // Implemente a lógica para exibir uma fatura específica
    }

    /**
     * Armazena um recurso recém-criado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Implemente a lógica para salvar uma nova fatura
    }

    /**
     * Atualiza o recurso especificado no armazenamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fatura $fatura)
    {
        // Implemente a lógica para atualizar uma fatura
    }
}
