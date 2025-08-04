<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Imagem;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class ContaController extends Controller
{
    /**
     * Remove a conta especificada do armazenamento.
     */
    public function destroy(Conta $conta)
    {
        //
    }

    /**
     * Edita a conta especificada.
     */
    public function edit(int $ID_Conta)
    {
        $conta = Conta::find($ID_Conta);

        return view('contaEditar', [
            'conta' => $conta,
        ]);

    }

    /**
     * Exibe a listagem do recurso.
     */
    public function index()
    {
        //
    }

    /**
     * Exibe o formulário para criar uma nova conta.
     */
    public function new()
    {
        $bancos = null;
        return view('contaCriar', [
            'bancos' => $bancos
        ]);

    }

    /**
     * Exibe o recurso especificado.
     */
    public function show(Conta $conta)
    {
        //
    }

    /**
     * Exibe a listagem completa de contas ativas e arquivadas.
     */
    public function showAllGemini(Request $request){
        $dateFilter = $request->date_filter;

        if (is_null($dateFilter) ) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        $contas = new Conta();

        $contasAtivas = $contas->show($start_date, $end_date,0);
        $contasArquivadas = $contas->show($start_date, $end_date,1);

        // Preenche os dados de transferências para cada conta
        foreach($contasAtivas as $conta) {
            $transferencias = new Transferencia();
            $conta->Entradas = $transferencias->tranferenciasEntrada($start_date, $end_date, $conta->ID_Conta);
            $conta->Saidas = $transferencias->tranferenciasSaida($start_date, $end_date, $conta->ID_Conta);
        }

        foreach($contasArquivadas as $conta) {
            $transferencias = new Transferencia();
            $conta->Entradas = $transferencias->tranferenciasEntrada($start_date, $end_date, $conta->ID_Conta);
            $conta->Saidas = $transferencias->tranferenciasSaida($start_date, $end_date, $conta->ID_Conta);
        }

        return view('contaListar', [
            'contasAtivas' => $contasAtivas,
            'contasArquivadas' => $contasArquivadas,
        ]);
    }

    public function showAll(Request $request){

        $dateFilter = $request->date_filter;

        if (is_null($dateFilter) ) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();

        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        /* Como era
        $start_date = Carbon::createFromDate('2014','06')->startOfMonth()->toDateString();
        $end_date = Carbon::createFromDate('2014','06')->endOfMonth()->toDateString();
        */

        $contas = new Conta();

        return view('contaListar', [
            //'contas' => $contas->show($start_date, $end_date)
            'contasAtivas' => $contas->show($start_date, $end_date,0),
            'contasArquivadas' => $contas->show($start_date, $end_date,1),
        ]);
    }

    /**
     * Salva uma nova conta no banco de dados.
     */
    public function store(Request $request)
    {
        $conta = new Conta();

        $conta->Nome = $request->Nome;
        $conta->Descricao = $request->Descricao;
        $conta->Banco = $request->Banco;
        $conta->Saldo_Inicial =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Saldo_Inicial)));
        $conta->Cor = $request->corConta;

        $imagens = $request->files->all();
        if (isset($imagens['imagem'])) {
            $conta->Imagem = Imagem::CriaImagem($imagens, 'imagem');
        }

        $conta->save();
        return redirect()->route('contas.showAll');
    }

    /**
     * Atualiza a conta especificada no armazenamento.
     */
    public function update(Request $request, int $ID_Conta)
    {
        $conta = Conta::find($ID_Conta);
        $conta->Nome = $request->Nome;
        $conta->Descricao = $request->Descricao;
        $conta->Banco = $request->Banco;
        $conta->Saldo_Inicial =
            str_replace(",",'.',str_replace(".","",
                str_replace("R$ ","",$request->Saldo_Inicial)));

        $conta->Cor = $request->corConta;

        $imagens = $request->files->all();
        if ($request["ApagaFoto"] == 1) {
            $conta->Imagem = null;
        } else {
            if (isset($imagens['imagem'])) {
                $conta->Imagem = Imagem::CriaImagem($imagens, 'imagem');
            }
        }

        $request["Arquivada"] = (isset($request["Arquivada"]))?1:0;
        $conta->Arquivada = $request->Arquivada;

        $conta->save();

        return redirect()->route('contas.showAll');
    }
}
