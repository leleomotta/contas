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

    public function showAll(){
/*
        $transferencias = Transferencia::where(function ($query) {
            $query->select('*');
            $query->orderBy('Data','DESC');
        })->get();
        return $transferencias;
        */

        $transferencias = DB::table('transferencia')
            ->select('ID_Conta_Origem', 'ID_Conta_Destino' , 'conta.Banco', 'conta.Descricao',
                'transferencia.Data', 'transferencia.Valor')
            ->join('conta', 'transferencia.ID_Conta_Destino', '=', 'conta.ID_Conta')
            ->orderBy('transferencia.ID_Conta_Origem','ASC');
        return $transferencias->get();
    }

    public function showContaOrigem(){
        $contasOrigem = DB::table('transferencia')
            ->select('ID_Conta_Origem', 'conta.Banco', 'conta.Descricao')
            ->join('conta', 'transferencia.ID_Conta_Origem', '=', 'conta.ID_Conta')
            ->distinct('transferencia.ID_Conta_Origem')
            ->orderBy('transferencia.ID_Conta_Origem','ASC');
        return $contasOrigem->get();
    }
}
