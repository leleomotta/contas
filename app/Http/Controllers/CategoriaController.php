<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    public function showAll(){
        Paginator::useBootstrap();
        //$categorias = Categoria::paginate(999);
        $despesasPai = Categoria::where(function ($query) {
            $query->select('*');
            $query->where('Tipo','D');
            $query->WhereNull('ID_Categoria_Pai');
            $query->orderBy('Nome','ASC');
        })->get();

        $despesas = Categoria::where(function ($query) {
            $query->select('*');
            $query->where('Tipo','X');
        })->get();


//a ideia é ir rodando e acrescentando quem tem filho na mão
        foreach($despesasPai as $desp) {
            $X = Categoria::where(function ($query) use ($desp) {
                $query->select('*');
                $query->where('ID_Categoria',$desp->ID_categoria);
                $query->WhereNull('ID_Categoria_Pai');
                $query->orderBy('Nome','ASC');
            })->get();

        }
        //$despesas = $despesas->merge($cartao);

        return view('categoriaListar', [
            'categorias' => $despesas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        //
    }
}
