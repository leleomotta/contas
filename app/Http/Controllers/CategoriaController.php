<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use function PHPUnit\Framework\isNull;

class CategoriaController extends Controller
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
        $categoria = new Categoria();

        $categoria->Nome = $request->Nome;
        $categoria->Cor = $request->corCategoria;
        $categoria->Tipo = $request->Tipo;
        $categoria->save();
        return redirect()->route('categorias.showAll');

    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    public function showAll(){

        $despesas = (new \App\Models\Categoria)->show('D');
        $receitas = (new \App\Models\Categoria)->show('R');

        return view('categoriaListar', [
            'despesas' => $despesas,
            'receitas' => $receitas
        ]);
    }

    public function edit(int $ID_Categoria) {

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $ID_Categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
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

    public function new(){

        //$categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');

        return view('categoriaCriar', [
//            'categorias' => $categorias
        ]);

    }
}
