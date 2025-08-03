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
     * Remove a conta especificada do armazenamento.
     *
     * @param Conta $conta
     * @return void
     */
    public function destroy(Conta $conta)
    {
        // Implemente a lógica para excluir uma conta
    }

    /**
     * Edita a conta especificada.
     *
     * @param int $ID_Conta
     * @return \Illuminate\View\View
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
     *
     * @return void
     */
    public function index()
    {
        // Esta função está vazia
    }

    /**
     * Exibe o formulário para criar uma nova conta.
     *
     * @return \Illuminate\View\View
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
     *
     * @param Conta $conta
     * @return void
     */
    public function show(Conta $conta)
    {
        // Esta função está vazia
    }

    /**
     * Exibe a listagem completa de contas ativas e arquivadas.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showAll(Request $request)
    {
        $dateFilter = $request->date_filter;

        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }
        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();

        $contas = new Conta();

        return view('contaListar', [
            'contasAtivas' => $contas->show($start_date, $end_date, 0),
            'contasArquivadas' => $contas->show($start_date, $end_date, 1),
        ]);
    }

    /**
     * Salva uma nova conta no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $conta = new Conta();

        $conta->Nome = $request->Nome;
        $conta->Descricao = $request->Descricao;
        $conta->Banco = $request->Banco;
        $conta->Saldo_Inicial = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Saldo_Inicial)));
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
     *
     * @param Request $request
     * @param int $ID_Conta
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $ID_Conta)
    {
        $conta = Conta::find($ID_Conta);
        $conta->Nome = $request->Nome;
        $conta->Descricao = $request->Descricao;
        $conta->Banco = $request->Banco;
        $conta->Saldo_Inicial = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Saldo_Inicial)));
        $conta->Cor = $request->corConta;

        $imagens = $request->files->all();
        if ($request["ApagaFoto"] == 1) {
            $conta->Imagem = null;
        } else {
            if (isset($imagens['imagem'])) {
                $conta->Imagem = Imagem::CriaImagem($imagens, 'imagem');
            }
        }

        $request["Arquivada"] = (isset($request["Arquivada"])) ? 1 : 0;
        $conta->Arquivada = $request->Arquivada;

        $conta->save();

        return redirect()->route('contas.showAll');
    }
}
