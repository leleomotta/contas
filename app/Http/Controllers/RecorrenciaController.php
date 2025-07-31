<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\{Recorrencia, Despesa, Fatura, Cartao};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class RecorrenciaController extends Controller
{
    // Armazena uma nova recorrência no banco de dados
    public function store(Request $request)
    {
        $recorrencia = new Recorrencia();

        // Define os campos básicos da recorrência
        $recorrencia->Descricao = $request->Descricao;
        $recorrencia->Valor = str_replace(",", ".", str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $recorrencia->ID_Categoria = $request->ID_Categoria;

        // Define o meio de pagamento com base nos IDs
        if ($request->has('ID_Conta')) {
            $recorrencia->ID_Conta = $request->ID_Conta;
        }
        if ($request->has('ID_Cartao')) {
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

    // Geração das despesas recorrentes para um determinado mês e ano
    public function gerarRecorrencias($mes, $ano)
    {
        $recorrencias = Recorrencia::where('Ativa', 1)->get();

        foreach ($recorrencias as $recorrencia) {
            $dataInicio = Carbon::parse($recorrencia->Data_inicio);
            $dataFim = $recorrencia->Data_fim ? Carbon::parse($recorrencia->Data_fim) : null;

            $diasNoMes = Carbon::create($ano, $mes, 1)->daysInMonth;

            for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                $dataAtual = Carbon::create($ano, $mes, $dia);

                // Verifica se a data atual está dentro do intervalo da recorrência
                if ($dataAtual->lt($dataInicio)) {
                    continue; // pula se estiver antes da data inicial
                }
                if ($dataFim && $dataAtual->gt($dataFim)) {
                    continue; // pula se estiver após a data final
                }

                $gerar = false;

                switch ($recorrencia->Periodicidade) {
                    case 'Mensal':
                        // Limpa e valida o dia
                        $diaVenc = (int) trim($recorrencia->Dia_vencimento);
                        Log::info("Dia do vencimento: $diaVenc | Dia atual: " . $dataAtual->day);
                        if ($diaVenc <= 0 || $diaVenc > 31) {
                            Log::info('Dia de vencimento inválido. Pulando.');
                            continue 2;
                        }
                        $gerar = $dataAtual->day == $diaVenc;
                        Log::info("Gerar despesa mensal? " . ($gerar ? 'Sim' : 'Não'));
                        break;

                    case 'Anual':
                        // Verifica formato correto (dd/mm)
                        if (!preg_match('/^\d{2}\/\d{2}$/', $recorrencia->Dia_vencimento)) {
                            continue 2; // pula para a próxima recorrência
                        }
                        [$diaRec, $mesRec] = explode('/', $recorrencia->Dia_vencimento);
                        if (!checkdate((int)$mesRec, (int)$diaRec, $dataAtual->year)) {
                            continue 2;
                        }
                        $gerar = $dataAtual->day == (int)$diaRec && $dataAtual->month == (int)$mesRec;
                        break;

                    case 'Semanal':
                        // Lista dos dias da semana com a primeira letra maiúscula
                        $diasSemana = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                        $diaSemana = $diasSemana[$dataAtual->dayOfWeek];
                        $gerar = $diaSemana === $recorrencia->Dia_vencimento;
                        break;
                }

                // Se a data não for válida para gerar, pula
                Log::info("Garantindo novamente se gerar despesa mensal? " . ($gerar ? 'Sim' : 'Não'));
                if (!$gerar) continue;

                // Evita gerar duplicatas para o mesmo dia e descrição
                $jaExiste = Despesa::where('Descricao', $recorrencia->Descricao)
                    ->whereDate('Data', $dataAtual->toDateString())
                    ->where('Valor', $recorrencia->Valor)
                    ->exists();

                Log::info("Agora vamos garantir se Já existe? " . ($jaExiste ? 'Sim' : 'Não'));
                if ($jaExiste) continue;

                // Criação da despesa
                $despesa = new Despesa();
                $despesa->Descricao = $recorrencia->Descricao;
                $despesa->Valor = $recorrencia->Valor;
                $despesa->Data = $dataAtual->toDateString();
                $despesa->ID_Categoria = $recorrencia->ID_Categoria;
                $despesa->Efetivada = 0;
                Log::info("ID_Conta na recorrencia: " . $recorrencia->ID_Conta);
                Log::info("Teste do vazio " . (!empty($recorrencia->ID_Conta) ? 'Sim' : 'Não'));
                // Define se a despesa é por conta
                if (!is_null($recorrencia->ID_Conta)) {
                    // Se existe um valor definido (inclusive zero) para ID_Conta, considera como conta vinculada
                    $despesa->ID_Conta = $recorrencia->ID_Conta;
                    $despesa->save();
                    Log::info("Despesa salva com ID Conta (ID = {$recorrencia->ID_Conta}) para recorrência ID {$recorrencia->ID_Recorrencia}.");
                } elseif (!is_null($recorrencia->ID_Cartao)) {
                    // Se não tem conta mas tem cartão, vincula à fatura
                    $despesa->save();
                    Log::info("Despesa salva com ID Cartão (ID = {$recorrencia->ID_Cartao}) para recorrência ID {$recorrencia->ID_Recorrencia}.");
                    $cartao = Cartao::find($recorrencia->ID_Cartao);
                    if (!$cartao) continue;

                    $diaFechamento = $cartao->Dia_Fechamento_Fatura;

                    // Define o mês de referência da fatura
                    $referencia = $dataAtual->copy();
                    if ($dataAtual->day > $diaFechamento) {
                        $referencia->addMonth();
                    }
                    $anoMes = $referencia->format('Y-m');

                    // Cria o vínculo com a fatura
                    $fatura = new Fatura();
                    $fatura->ID_Cartao = $recorrencia->ID_Cartao;
                    $fatura->ID_Despesa = $despesa->ID_Despesa;
                    $fatura->Ano_Mes = $anoMes;
                    $fatura->Fechada = 0;
                    $fatura->save();
                }
            }
        }

        // Retorna mensagem simples para exibir no navegador
        return response('Recorrências geradas com sucesso para ' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '/' . $ano);
    }
}
