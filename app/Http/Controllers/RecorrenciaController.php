<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\{Recorrencia, Despesa, Fatura, Cartao};
use Carbon\Carbon;

class RecorrenciaController extends Controller
{
    public function store(Request $request)
    {
        $recorrencia = new Recorrencia();

        // Define os campos básicos da recorrência
        $recorrencia->Descricao = $request->Descricao;
        $recorrencia->Valor = str_replace(",", ".", str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $recorrencia->ID_Categoria = $request->ID_Categoria;
        $recorrencia->TipoPagamento = $request->TipoPagamento;

        // Define o meio de pagamento
        if ($request->TipoPagamento === 'conta') {
            $recorrencia->ID_Conta = $request->ID_Conta;
        } elseif ($request->TipoPagamento === 'cartao') {
            $recorrencia->ID_Cartao = $request->ID_Cartao;
        }

        // Define a periodicidade e datas
        $recorrencia->Periodicidade = $request->Periodicidade;
        $recorrencia->Dia_vencimento = $request->DiaVencimento;
        $recorrencia->Data_inicio = Carbon::createFromFormat('d/m/Y', $request->DataInicio)->format('Y-m-d');

        if (!empty($request->DataFim)) {
            $recorrencia->Data_fim = Carbon::createFromFormat('d/m/Y', $request->DataFim)->format('Y-m-d');
        }

        // Define o status da recorrência (ativa ou não)
        $recorrencia->Ativa = isset($request->Ativa) ? 1 : 0;
        $recorrencia->save();

        return Redirect::to('/recorrencias');
    }

    public function gerarRecorrencias($mes, $ano)
    {
        $recorrencias = Recorrencia::where('Ativa', 1)->get();

        foreach ($recorrencias as $recorrencia) {
            $dataInicio = Carbon::parse($recorrencia->Data_inicio);
            $dataFim = $recorrencia->Data_fim ? Carbon::parse($recorrencia->Data_fim) : null;

            $diasNoMes = Carbon::create($ano, $mes, 1)->daysInMonth;

            for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                $dataAtual = Carbon::create($ano, $mes, $dia);

                // Verifica se a data atual está no intervalo de recorrência
                if ($dataAtual->lt($dataInicio)) continue;
                if ($dataFim && $dataAtual->gt($dataFim)) continue;

                $gerar = false;

                // Define a lógica de geração baseada na periodicidade
                switch ($recorrencia->Periodicidade) {
                    case 'mensal':
                        $gerar = $dataAtual->day == (int)$recorrencia->Dia_vencimento;
                        break;
                    case 'anual':
                        [$dia, $mesRef] = explode('/', $recorrencia->Dia_vencimento);
                        $gerar = $dataAtual->day == (int)$dia && $dataAtual->month == (int)$mesRef;
                        break;
                    case 'semanal':
                        $gerar = strtolower($dataAtual->locale('pt_BR')->isoFormat('dddd')) == strtolower($recorrencia->Dia_vencimento);
                        break;
                }

                if (!$gerar) continue;

                // Evita gerar duplicidade
                $jaExiste = Despesa::where('Descricao', $recorrencia->Descricao)
                    ->whereDate('Data', $dataAtual->toDateString())
                    ->where('Valor', $recorrencia->Valor)
                    ->exists();

                // Se já existir uma despesa com os mesmos dados na mesma data, ignora
                if ($jaExiste) {
                    continue;
                }

                // Cria a despesa recorrente
                $despesa = new Despesa();
                $despesa->Descricao = $recorrencia->Descricao;
                $despesa->Valor = $recorrencia->Valor;
                $despesa->Data = $dataAtual->toDateString();
                $despesa->ID_Categoria = $recorrencia->ID_Categoria;
                $despesa->Efetivada = 0;

                if ($recorrencia->TipoPagamento === 'conta') {
                    // Despesa com conta vinculada
                    $despesa->ID_Conta = $recorrencia->ID_Conta;
                    $despesa->save();
                } elseif ($recorrencia->TipoPagamento === 'cartao') {
                    // Despesa com cartão vinculada
                    $despesa->save();

                    // Busca o dia de fechamento da fatura do cartão
                    $cartao = Cartao::find($recorrencia->ID_Cartao);
                    $diaFechamento = $cartao->Dia_Fechamento_Fatura;

                    // Determina o mês de referência da fatura
                    $referencia = $dataAtual->copy();
                    if ($dataAtual->day > $diaFechamento) {
                        $referencia->addMonth();
                    }

                    $anoMes = $referencia->format('Y-m');

                    // Cria o vínculo na fatura
                    $fatura = new Fatura();
                    $fatura->ID_Cartao = $recorrencia->ID_Cartao;
                    $fatura->ID_Despesa = $despesa->ID_Despesa;
                    $fatura->Ano_Mes = $anoMes;
                    $fatura->Fechada = 0;
                    $fatura->save();
                }
            }
        }
    }
}
