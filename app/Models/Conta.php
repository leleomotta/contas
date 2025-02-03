<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conta extends Model
{
    use HasFactory;

    protected $table = 'conta';

    protected $primaryKey = 'ID_Conta';

    public function show($start_date, $end_date, $arquivada){

        $contas = DB::table('conta')
            ->select('conta.ID_Conta', 'conta.Descricao', 'conta.Banco', 'conta.Imagem', 'conta.Cor', 'conta.Arquivada',
                DB::raw("000 as Despesas"), DB::raw('000 as Receitas'),
                DB::raw("'MOTTA' as Saldo"))
            ->where('conta.Arquivada', $arquivada)
            ->groupBy('conta.ID_Conta')
            //->toSql(); dd($contas);
            ->get();

        foreach($contas as $conta){
            $receita = DB::table('conta')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(receita.Valor) as Receitas') )
                ->leftJoin('receita', function($join) {
                    $join->on('receita.ID_Conta', '=', 'conta.ID_Conta')
                        ->where('receita.Efetivada', '=', 1);
                })
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->groupBy('conta.ID_Conta')
                //->toSql(); dd($receita);
                ->get();

            $despesa = DB::table('conta')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(despesa.Valor) as Despesas') )

                ->leftJoin('despesa', function($join) {
                    $join->on('despesa.ID_Conta', '=', 'conta.ID_Conta')
                        ->where('despesa.Efetivada', '=', 1);
                })
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->groupBy('conta.ID_Conta')
                ->get();

            $tranferencias_Saida = DB::table('transferencia')
                ->select('transferencia.ID_Transferencia',
                    DB::raw('sum(transferencia.Valor) as Saida') )

                ->where('transferencia.ID_Conta_Origem', '=', $conta->ID_Conta)

                ->get();

            $tranferencias_Entrada = DB::table('transferencia')
                ->select('transferencia.ID_Transferencia',
                    DB::raw('sum(transferencia.Valor) as Saida') )

                ->where('transferencia.ID_Conta_Destino', '=', $conta->ID_Conta)

                ->get();


            $conta->Despesas = $despesa[0]->Despesas;
            $conta->Receitas = $receita[0]->Receitas;

            $conta->Saldo = $receita[0]->Saldo_inicial + $receita[0]->Receitas + $tranferencias_Entrada[0]->Saida - $despesa[0]->Despesas - $tranferencias_Saida[0]->Saida;
        }

        return $contas;
    }

    public function showAll(){

        //$contas = DB::table('conta')->paginate(99999);
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->where('Arquivada',0);
            $query->orderBy('Banco','ASC');
        })->get();
        return $contas;
    }
}
