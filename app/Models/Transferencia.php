<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = 'transferencia';

    protected $primaryKey = 'ID_Transferencia';

    public function showAll()
    {
        /*
                $transferencias = Transferencia::where(function ($query) {
                    $query->select('*');
                    $query->orderBy('Data','DESC');
                })->get();
                return $transferencias;
                */

        $transferencias = DB::table('transferencia')
            ->select('ID_Transferencia', 'ID_Conta_Origem', 'ID_Conta_Destino', 'conta.Banco', 'conta.Nome', 'conta.Descricao',
                'transferencia.Data', 'transferencia.Valor')
            ->join('conta', 'transferencia.ID_Conta_Destino', '=', 'conta.ID_Conta')
            //->orderBy('transferencia.ID_Conta_Origem','ASC');
            ->orderBy('transferencia.Data', 'DESC');

        return $transferencias->get();
    }

    public function showContaOrigem()
    {
        $contasOrigem = DB::table('transferencia')
            ->select('ID_Conta_Origem', 'conta.Banco', 'conta.Nome', 'conta.Descricao')
            ->join('conta', 'transferencia.ID_Conta_Origem', '=', 'conta.ID_Conta')
            ->distinct('transferencia.ID_Conta_Origem')
            ->orderBy('transferencia.ID_Conta_Origem', 'ASC');
        //->toSql(); dd($contasOrigem);
        return $contasOrigem->get();
    }

    public function showContaDestino()
    {
        $contasDestino = DB::table('transferencia')
            ->select('ID_Conta_Destino', 'conta.Banco', 'conta.Nome', 'conta.Descricao')
            ->join('conta', 'transferencia.ID_Conta_Destino', '=', 'conta.ID_Conta')
            ->distinct('transferencia.ID_Conta_Destino')
            ->orderBy('transferencia.ID_Conta_Destino', 'ASC');
        //->toSql(); dd($contasDestino);
        return $contasDestino->get();
    }


    public function tranferenciasSaida($start_date, $end_date, $contaOrigem){
        $tranferenciasSaida = DB::table('transferencia')
            ->select('transferencia.ID_Transferencia', 'transferencia.Data',
                'transferencia.Valor', 'transferencia.Observacao');
            if (! is_null($start_date) ) {
                $tranferenciasSaida->where('Data', '>=', $start_date);
            }
            if (!is_null($end_date)) {
                $tranferenciasSaida->where('Data', '<=', $end_date);
            }
            if (!is_null($contaOrigem)) {
                $tranferenciasSaida->where('transferencia.ID_Conta_Origem', '=', $contaOrigem);
            }
        return $tranferenciasSaida->get();
    }

    public function tranferenciasEntrada($start_date, $end_date, $contaDestino){
        $tranferenciasEntrada = DB::table('transferencia')
            ->select('transferencia.ID_Transferencia', 'transferencia.Data',
                'transferencia.Valor', 'transferencia.Observacao');
        if (! is_null($start_date) ) {
            $tranferenciasEntrada->where('Data', '>=', $start_date);
        }
        if (!is_null($end_date)) {
            $tranferenciasEntrada->where('Data', '<=', $end_date);
        }
        if (!is_null($contaDestino)) {
            $tranferenciasEntrada->where('transferencia.ID_Conta_Destino', '=', $contaDestino);
        }
        return $tranferenciasEntrada->get();
    }


}
