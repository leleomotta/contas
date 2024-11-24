<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Imagem;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class ContaController extends Controller
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
        $conta = new Conta();

        //$prova->idUsuario = (session()->get('UsuarioLogado'))->idUsuario;

        $conta->Descricao = $request->Descricao;
        $conta->Banco = $request->Banco;
        //$conta->Saldo_Inicial = str_replace("R$ ","",$request->Saldo_Inicial);
        $conta->Saldo_Inicial =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Saldo_Inicial)));
        //dd($conta->Saldo_Inicial);
        $conta->Cor = $request->corConta;

        $imagens = $request->files->all();
        if (isset($imagens['imagem'])) {
            $conta->Imagem = Imagem::CriaImagem($imagens, 'imagem');
        }

        //$prova->DataProva = implode("-",array_reverse(explode("/",$request->DataProva)));

        $conta->save();
        return redirect()->route('contas.showAll');
    }

    /**
     * Display the specified resource.
     */
    public function show(Conta $conta)
    {
        //
    }


    public function showAll(){


        $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');

        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        $contas = new Conta();

        return view('contaListar', [
            'contas' => $contas->show($start_date, $end_date)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conta $conta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conta $conta)
    {
        //
    }

    public function new(){
        $bancos = null;
        return view('contaCriar', [
            'bancos' => $bancos
        ]);

    }
}
