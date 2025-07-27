<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CartaoController extends Controller
{
    public function destroy(Cartao $cartao)
    {
        //
    }

    public function destroy_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);
        try {
            DB::beginTransaction();
            $fatura->delete();
            $despesa->delete();

            DB::commit();

            //$url ='/fatura?ID_Cartao=' . $request->ID_Cartao;
            //return redirect::to($url);
            return redirect()->route('cartoes.fatura',array('ID_Cartao' => $request->ID_Cartao) );

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function edit_despesa(Request $request) {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::find($request->ID_Despesa);
        $categorias = (new \App\Models\Categoria)->show('D');


        return view('fatura_despesaEditar', [
            'despesa' => $despesa,
            'fatura' => $fatura,
            'categorias' => $categorias
        ]);
    }

    public function fatura(Request $request)
    {

        if (is_null($request->Ano_Mes) ) {
            $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        else
        {
            $Ano_Mes = $request->Ano_Mes;
        }

        $fatura = new Fatura();
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::all();

        if ($request->session()->get('ID_Cartao') == null){
            $request->session()->put('ID_Cartao', $request->ID_Cartao);
        }

        //dd($fatura->count());
        return view('faturaListar', [
            //'faturas' => $fatura->show($Ano_Mes,$request->ID_Cartao),
            //'totalFatura' => $fatura->totalFatura($Ano_Mes,$request->ID_Cartao),

            'faturas' => $fatura->show($Ano_Mes,$request->session()->get('ID_Cartao')),
            'totalFatura' => $fatura->totalFatura($Ano_Mes,$request->session()->get('ID_Cartao')),
            'contas' => $contas,
            'cartoes' => $cartoes,
        ]);
    }

    public function fatura_fechar(Request $request)
    {
        if (is_null($request->Ano_Mes) ) {
            $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        else
        {
            $Ano_Mes = $request->Ano_Mes;
        }
        $ID_Cartao = $request->ID_Cartao;
        $Data = implode("-",array_reverse(explode("/",$request->Data_Fechamento)));

        $Conta = $request->Conta;

        (new \App\Models\Fatura)->fatura_fechar($Ano_Mes,$ID_Cartao,$Data,$Conta);

        //$fatura = (new \App\Models\Fatura)->show($Ano_Mes,$request->ID_Cartao);
        $fatura = new Fatura();
        $contas = (new \App\Models\Conta)->showAll();

        return view('faturaListar', [
            'faturas' => $fatura->show($Ano_Mes,$ID_Cartao),
            'totalFatura' => $fatura->totalFatura($Ano_Mes,$ID_Cartao),
            'contas' => $contas
        ]);

    }

    public function fatura_reabrir(Request $request)
    {
        if (is_null($request->Ano_Mes) ) {
            $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
                Carbon::now()->isoFormat('MM');
        }
        else
        {
            $Ano_Mes = $request->Ano_Mes;
        }
        $ID_Cartao = $request->ID_Cartao;

        (new \App\Models\Fatura)->fatura_reabrir($Ano_Mes,$ID_Cartao);

        //$fatura = (new \App\Models\Fatura)->show($Ano_Mes,$request->ID_Cartao);
        $fatura = new Fatura();
        $contas = (new \App\Models\Conta)->showAll();

        return view('faturaListar', [
            'faturas' => $fatura->show($Ano_Mes,$ID_Cartao),
            'totalFatura' => $fatura->totalFatura($Ano_Mes,$ID_Cartao),
            'contas' => $contas
        ]);

    }

    public function index()
    {
        //
    }

    public function new(){
        $contas = (new \App\Models\Conta)->showAll();

        return view('cartaoCriar', [
            'contas' => $contas,
        ]);

    }

    public function new_despesa(){
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::all();
        //$categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','D');
        $categorias = (new \App\Models\Categoria)->show('D');

        return view('fatura_despesaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
            'cartoes' => $cartoes,
        ]);

    }

    public function show(Cartao $cartao)
    {
        //
    }

    public function showAll(){
        $Ano_Mes = Carbon::now()->isoFormat('Y') . '-' .
            Carbon::now()->isoFormat('MM');
        $cartoes = new Cartao();
        //$request->session()->put('ID_Cartao', $request->ID_Cartao);
        Session::forget('ID_Cartao');
        return view('cartaoListar', [
            'cartoes' => $cartoes->show($Ano_Mes)
        ]);
    }

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

    public function store_despesa(Request $request)
    {
        $Ano = $request->Ano;
        $Mes = str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);
        $Ano_Mes = $Ano . '-' . $Mes;

        // Busca a fatura correspondente
        $faturaExistente = Fatura::where('ID_Cartao', $request->ID_Cartao)
            ->where('Ano_Mes', $Ano_Mes)
            ->first();

        // Verifica se a fatura existe e está finalizada
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

        // Cálculo do valor base e ajuste de centavos
        $valorBase = floor(($valorTotal / $numParcelas) * 100) / 100;
        $diferenca = round($valorTotal - ($valorBase * $numParcelas), 2);

        // Base para calcular fatura de cada parcela (ignora a data da compra e usa o campo Ano/Mes selecionado)
        $dataBaseParcela = \Carbon\Carbon::createFromDate((int) $request->Ano, (int) $request->Mes, 1);

        for ($i = 1; $i <= $numParcelas; $i++) {
            $valorParcela = $valorBase;
            if ($i <= $diferenca * 100) {
                $valorParcela += 0.01;
            }

            // Define a data da fatura da parcela
            $dataParcela = $dataBaseParcela->copy()->addMonths($i - 1);
            $anoMesParcela = $dataParcela->format('Y-m');

            // Verifica se a fatura da parcela está fechada
            $faturaParcelaExistente = Fatura::where('ID_Cartao', $request->ID_Cartao)
                ->where('Ano_Mes', $anoMesParcela)
                ->first();

            if ($faturaParcelaExistente && $faturaParcelaExistente->Fechada) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['Fatura ' . $anoMesParcela . ' já está finalizada. Não é possível adicionar novas despesas.']);
            }

            // Cria a despesa
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

            // Cria ou associa à fatura
            $fatura = new Fatura();
            $fatura->ID_Cartao = $request->ID_Cartao;
            $fatura->ID_Despesa = $despesa->ID_Despesa;
            $fatura->Fechada = 0;
            $fatura->Ano_Mes = $anoMesParcela;
            $fatura->save();
        }

        return redirect()->route('cartoes.fatura', ['ID_Cartao' => $request->ID_Cartao]);
    }

    public function update(Request $request, Cartao $cartao)
    {
        //
    }

    public function update_despesa(Request $request)
    {
        $despesa = Despesa::find($request->ID_Despesa);
        $fatura = Fatura::where('ID_Despesa', $request->ID_Despesa)->first();

        $novoAnoMes = $request->Ano . '-' . str_pad($request->Mes, 2 , '0' , STR_PAD_LEFT);

        // Verifica se está tentando mover para uma fatura fechada
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
