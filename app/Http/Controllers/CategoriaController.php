<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CategoriaController extends Controller
{
    /**
     * Remove a categoria especificada do armazenamento.
     *
     * @param int $ID_Categoria
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $ID_Categoria)
    {
        $categoria = Categoria::find($ID_Categoria);

        try {
            DB::beginTransaction();
            $categoria->delete();
            DB::commit();
            return redirect()->route('categorias.showAll');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('naoapagado', true);
            return back();
        }
    }

    /**
     * Edita a categoria especificada.
     *
     * @param int $ID_Categoria
     * @return \Illuminate\View\View
     */
    public function edit(int $ID_Categoria)
    {
        $categoria = Categoria::leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
            ->where('categoria.ID_Categoria', $ID_Categoria)
            ->select('categoria.*', 'icone.*')
            ->first();

        $icones = (new \App\Models\Icone)->showAll();

        return view('categoriaEditar', [
            'categoria' => $categoria,
            'icones' => $icones
        ]);
    }

    /**
     * Exibe a listagem do recurso.
     *
     * @return void
     */
    public function index()
    {
        // Esta função está vazia
    }

    /**
     * Exibe o formulário para criar uma nova categoria.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {
        $icones = (new \App\Models\Icone)->showAll();

        return view('categoriaCriar', [
            'categoriaPai' => $request->ID_Categoria_Pai,
            'TipoCategoria' => $request->Tipo,
            'icones' => $icones
        ]);
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Categoria $categoria
     * @return void
     */
    public function show(Categoria $categoria)
    {
        // Esta função está vazia
    }

    /**
     * Exibe a listagem completa de categorias.
     *
     * @return \Illuminate\View\View
     */
    public function showAll()
    {
        $despesas = (new \App\Models\Categoria)->show('D');
        $receitas = (new \App\Models\Categoria)->show('R');

        return view('categoriaListar', [
            'despesas' => $despesas,
            'receitas' => $receitas,
        ]);
    }

    /**
     * Salva uma nova categoria no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $categoria = new Categoria();

        $categoria->Nome = $request->Nome;
        $categoria->Cor = $request->corCategoria;
        if ($request->TipoCat == NULL){
            $categoria->Tipo = $request->TipoCat2;
        }
        else{
            $categoria->Tipo = $request->TipoCat;
        }
        $categoria->Icone = $request->Icone;
        $categoria->ID_Categoria_Pai = $request->ID_Categoria_Pai;

        $categoria->save();
        return redirect()->route('categorias.showAll');
    }

    /**
     * Atualiza a categoria especificada no armazenamento.
     *
     * @param Request $request
     * @param int $ID_Categoria
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $ID_Categoria)
    {
        $categoria = Categoria::find($ID_Categoria);

        $categoria->Nome = $request->Nome;
        $categoria->Cor = $request->corCategoria;
        $categoria->ID_Icone = $request->Icone;

        $categoria->save();

        return redirect()->route('categorias.showAll');
    }
}
