<?php

namespace App\Http\Controllers;

use App\Models\Recorrencia;
use App\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;

class RecorrenciaController extends Controller
{
    public function gerarRecorrencias($mes, $ano)
    {
        $recorrencias = Recorrencia::where('Ativa', 1)->get();

        foreach ($recorrencias as $rec) {
            $dataReferencia = Carbon::createFromDate($ano, $mes, 1);

            switch ($rec->Periodicidade) {
                case 'Mensal':
                    $dia = min($rec->Dia_vencimento, $dataReferencia->daysInMonth);
                    $data = $dataReferencia->copy()->day($dia);
                    break;

                case 'Anual':
                    $inicio = Carbon::parse($rec->Data_inicio);
                    $data = Carbon::createFromDate($ano, $inicio->month, $inicio->day);
                    break;

                case 'Semanal':
                    $inicio = Carbon::parse($rec->Data_inicio);
                    $diaSemana = $inicio->dayOfWeek;
                    $data = $dataReferencia->copy()->startOfMonth()->next($diaSemana);
                    break;

                default:
                    continue 2;
            }

            if (!is_null($rec->Data_fim) && $data->gt(Carbon::parse($rec->Data_fim))) {
                continue;
            }

            if ($data->lt(Carbon::parse($rec->Data_inicio))) {
                continue;
            }

            $jaExiste = Despesa::where('Descricao', $rec->Descricao)
                ->whereDate('Data', $data->toDateString())
                ->exists();

            if (!$jaExiste) {
                $despesa = new Despesa();
                $despesa->Descricao = $rec->Descricao;
                $despesa->Valor = $rec->Valor;
                $despesa->Data = $data->toDateString();
                $despesa->ID_Categoria = $rec->ID_Categoria;
                $despesa->ID_Conta = $rec->ID_Conta;
                $despesa->ID_Cartao = $rec->ID_Cartao;
                $despesa->Efetivada = 0;
                $despesa->save();
            }
        }

        return response('Recorrências geradas com sucesso!', 200);
    }
}
