<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CartaoController extends Controller
{
    /**
     * Remove o recurso de cartão especificado do armazenamento.
     *
     * @param Cartao $cartao
     * @return void
     */
    public function destroy(Cartao $cartao)
    {
        // Implemente a lógica para excluir um cartão
    }

    /**
     * Remove uma despesa associada a uma fatura.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);
        try {
            DB::beginTransaction();
            $fatura->delete();
            $despesa->delete();

            DB::commit();

            return redirect()->route('cartoes.fatura', array('ID_Cartao' => $request->ID_Cartao) );

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    /**
     * Exibe o formulário para editar uma despesa de fatura.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function edit_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);
        $categorias = (new \App\Models\Categoria)->show('D');

        return view('fatura_despesaEditar', [
            'despesa' => $despesa,
            'fatura' => $fatura,
            'categorias' => $categorias
        ]);
    }

    /**
     * Exibe a fatura de um cartão para um mês/ano específico.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function fatura(Request $request)
    {
        $ID_Cartao = $request->session()->get('ID_Cartao') ?? $request->ID_Cartao;

        $Ano_Mes = is_null($request->Ano_Mes)
            ? Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM')
            : $request->Ano_Mes;

        // Busca o status de fechamento da fatura
        $faturaPrimeiro = Fatura::where('ID_Cartao', $ID_Cartao)->where('Ano_Mes', $Ano_Mes)->first();
        $fechada = ($faturaPrimeiro && $faturaPrimeiro->Fechada == 1);

        /*
        if ($faturaPrimeiro && $faturaPrimeiro->Fechada == 1) {
            $data = Carbon::createFromFormat('Y-m', $Ano_Mes)->addMonth();
            $Ano_Mes = $data->format('Y-m');
        }
        */

        $fatura = new Fatura();
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::all();

        if (is_null($request->session()->get('ID_Cartao'))) {
            $request->session()->put('ID_Cartao', $request->ID_Cartao);
        }

        Log::info('AnoMes: ' . $Ano_Mes);
        Log::info('ID_Cartao: ' . $ID_Cartao);

        return view('faturaListar', [
            'faturas' => $fatura->show($Ano_Mes, $ID_Cartao),
            'totalFatura' => $fatura->totalFatura($Ano_Mes, $ID_Cartao),
            'contas' => $contas,
            'cartoes' => $cartoes,
            'Ano_Mes' => $Ano_Mes, // Adiciona o Ano_Mes atualizado à view
            'fechada' => $fechada, // Adiciona a variável 'fechada' à view
        ]);
    }

    /**
     * Fecha a fatura de um cartão e move as despesas para uma conta.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fatura_fechar(Request $request)
    {
        $Ano_Mes = is_null($request->Ano_Mes)
            ? Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM')
            : $request->Ano_Mes;
        $ID_Cartao = $request->ID_Cartao;
        $Data = implode("-", array_reverse(explode("/", $request->Data_Fechamento)));
        $Conta = $request->Conta;

        (new Fatura)->fatura_fechar($Ano_Mes, $ID_Cartao, $Data, $Conta);

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $ID_Cartao, 'Ano_Mes' => $Ano_Mes]);
    }

    /**
     * Reabre uma fatura fechada.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fatura_reabrir(Request $request)
    {
        $Ano_Mes = is_null($request->Ano_Mes)
            ? Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM')
            : $request->Ano_Mes;
        $ID_Cartao = $request->ID_Cartao;

        (new Fatura)->fatura_reabrir($Ano_Mes, $ID_Cartao);

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $ID_Cartao, 'Ano_Mes' => $Ano_Mes]);
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
     * Exibe o formulário para criar um novo cartão.
     *
     * @return \Illuminate\View\View
     */
    public function new()
    {
        $contas = (new \App\Models\Conta)->showAll();

        return view('cartaoCriar', [
            'contas' => $contas,
        ]);
    }

    /**
     * Exibe o formulário para criar uma nova despesa de fatura.
     *
     * @return \Illuminate\View\View
     */
    public function new_despesa()
    {
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::all();
        $categorias = (new \App\Models\Categoria)->show('D');

        return view('fatura_despesaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
            'cartoes' => $cartoes,
        ]);
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Cartao $cartao
     * @return void
     */
    public function show(Cartao $cartao)
    {
        // Esta função está vazia
    }

    /**
     * Exibe a listagem de todos os cartões.
     *
     * @return \Illuminate\View\View
     */
    public function showAll()
    {
        $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        $cartoes = new Cartao();
        Session::forget('ID_Cartao');
        return view('cartaoListar', [
            'cartoes' => $cartoes->show($Ano_Mes)
        ]);
    }

    /**
     * Armazena um novo cartão no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $cartao = new Cartao();
        $cartao->Nome = $request->Nome;
        $cartao->Bandeira = $request->Bandeira;
        $cartao->Dia_Vencimento = $request->Dia_Vencimento;
        $cartao->Dia_Fechamento_Fatura = $request->Dia_Fechamento_Fatura;
        $cartao->ID_Conta = $request->Conta;
        $cartao->Cor = $request->corCartao;

        $cartao->save();

        return redirect()->route('cartoes.showAll');
    }

    /**
     * Salva uma nova despesa parcelada ou não para uma fatura.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store_despesa(Request $request)
    {
        $Ano = $request->Ano;
        $Mes = str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);
        $Ano_Mes = $Ano . '-' . $Mes;

        $faturaExistente = Fatura::where('ID_Cartao', $request->ID_Cartao)
            ->where('Ano_Mes', $Ano_Mes)
            ->first();

        if ($faturaExistente && $faturaExistente->Fechada) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Fatura ' . $Ano_Mes . ' já está finalizada. Não é possível adicionar novas despesas.']);
        }

        $cartao = Cartao::find($request->ID_Cartao);
        $descricaoOriginal = $request->Descricao;
        $valorStr = $request->Valor;
        $valorTotal = floatval(str_replace(",", ".", str_replace(".", "", str_replace("R$ ", "", $valorStr))));
        $data = implode("-", array_reverse(explode("/", $request->Data)));
        $parcelada = $request->Parcelada === 'sim';
        $numParcelas = $parcelada ? max((int) $request->NumeroParcelas, 1) : 1;

        $valorBase = floor(($valorTotal / $numParcelas) * 100) / 100;
        $diferenca = round($valorTotal - ($valorBase * $numParcelas), 2);

        $dataBaseParcela = Carbon::createFromDate((int) $request->Ano, (int) $request->Mes, 1);

        for ($i = 1; $i <= $numParcelas; $i++) {
            $valorParcela = $valorBase;
            if ($i <= $diferenca * 100) {
                $valorParcela += 0.01;
            }

            $dataParcela = $dataBaseParcela->copy()->addMonths($i - 1);
            $anoMesParcela = $dataParcela->format('Y-m');

            $faturaParcelaExistente = Fatura::where('ID_Cartao', $request->ID_Cartao)
                ->where('Ano_Mes', $anoMesParcela)
                ->first();

            if ($faturaParcelaExistente && $faturaParcelaExistente->Fechada) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['Fatura ' . $anoMesParcela . ' já está finalizada. Não é possível adicionar novas despesas.']);
            }

            $despesa = new Despesa();
            $despesa->Descricao = $parcelada ? "{$descricaoOriginal} ({$i}/{$numParcelas})" : $descricaoOriginal;
            $despesa->Valor = $valorParcela;
            $despesa->ValorTotal = $valorTotal;
            $despesa->Parcela = $parcelada ? $i : null;
            $despesa->TotalParcelas = $parcelada ? $numParcelas : null;
            $despesa->Data = $data;
            $despesa->ID_Conta = $cartao->ID_Conta;
            $despesa->ID_Categoria = $request->Categoria;
            $despesa->Efetivada = 0;
            $despesa->save();

            $fatura = new Fatura();
            $fatura->ID_Cartao = $request->ID_Cartao;
            $fatura->ID_Despesa = $despesa->ID_Despesa;
            $fatura->Fechada = 0;
            $fatura->Ano_Mes = $anoMesParcela;
            $fatura->save();
        }

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $request->ID_Cartao]);
    }

    /**
     * Atualiza o recurso de cartão especificado no armazenamento.
     *
     * @param Request $request
     * @param Cartao $cartao
     * @return void
     */
    public function update(Request $request, Cartao $cartao)
    {
        // Implemente a lógica para atualizar um cartão
    }

    /**
     * Atualiza uma despesa de fatura.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::where('ID_Despesa', $request->ID_Despesa)->first();

        $novoAnoMes = $request->Ano . '-' . str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);

        $faturaNova = Fatura::where('ID_Cartao', $request->ID_Cartao)
            ->where('Ano_Mes', $novoAnoMes)
            ->where('ID_Despesa', '!=', $despesa->ID_Despesa)
            ->first();

        if ($faturaNova && $faturaNova->Fechada) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'A fatura de ' . $novoAnoMes . ' já está finalizada. Para alterar esta despesa, escolha um mês com fatura em aberto.'
                ]);
        }

        $despesa->Data = implode("-", array_reverse(explode("/", $request->Data)));
        $despesa->Descricao = $request->Descricao;
        $despesa->Valor = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $despesa->ID_Categoria = $request->Categoria;

        $fatura->Ano_Mes = $novoAnoMes;

        $despesa->save();
        $fatura->save();

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $request->ID_Cartao]);
    }
}
