<?php

namespace App\Http\Controllers;

use App\Models\Icone;
use Illuminate\Http\Request;

class IconeController extends Controller
{
    /**
     * Remove o recurso especificado do armazenamento.
     *
     * @param Icone $icone
     * @return void
     */
    public function destroy(Icone $icone)
    {
        // Implemente a lógica para excluir um ícone
    }

    /**
     * Exibe a listagem do recurso.
     *
     * @return void
     */
    public function index()
    {
        // Implemente a lógica para exibir a lista de ícones
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Icone $icone
     * @return void
     */
    public function show(Icone $icone)
    {
        // Implemente a lógica para exibir um ícone específico
    }

    /**
     * Exibe a listagem completa de ícones.
     *
     * @param Icone $icone
     * @return void
     */
    public function showAll(Icone $icone)
    {
        // Implemente a lógica para exibir todos os ícones
    }

    /**
     * Armazena um recurso recém-criado.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        // Implemente a lógica para salvar um novo ícone
    }

    /**
     * Atualiza o recurso especificado no armazenamento.
     *
     * @param Request $request
     * @param Icone $icone
     * @return void
     */
    public function update(Request $request, Icone $icone)
    {
        // Implemente a lógica para atualizar um ícone
    }
}
