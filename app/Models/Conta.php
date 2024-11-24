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

    public function show($start_date, $end_date){

        $contas = DB::table('conta')
            ->select('conta.ID_Conta', 'conta.Descricao', 'conta.Banco', 'conta.Imagem', 'conta.Cor',
                DB::raw('sum(despesa.Valor) as Despesas'), DB::raw('sum(receita.Valor) as Receitas'),
                DB::raw("'MOTTA' as Saldo"))

            ->leftJoin('despesa', function($join) use ($start_date, $end_date) {
                $join->on('despesa.ID_Conta', '=', 'conta.ID_Conta')
                    ->where('despesa.Efetivada', '=', 1)
                    ->where('despesa.Data', '>=', $start_date)
                    ->where('despesa.Data', '<=', $end_date);
                    //->on('despesa.Data','>=', "'" . $start_date)
                    //->on('despesa.Data','<=', DB::raw($end_date));
            })
            ->leftJoin('receita', function($join) use ($start_date, $end_date) {
                $join->on('receita.ID_Conta', '=', 'conta.ID_Conta')
                    ->where('receita.Efetivada', '=', 1)
                    ->where('receita.Data', '>=', $start_date)
                    ->where('receita.Data', '<=', $end_date);
            })
            ->groupBy('conta.ID_Conta')
            //->toSql(); dd($contas);
            ->get();
            //dd($contas);

        foreach($contas as $conta){
            $soma = DB::table('conta')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(despesa.Valor) as Despesas'),
                    DB::raw('sum(receita.Valor) as Receitas') )

                ->leftJoin('despesa', function($join) {
                    $join->on('despesa.ID_Conta', '=', 'conta.ID_Conta')
                        ->where('despesa.Efetivada', '=', 1);
                })
                ->leftJoin('receita', function($join) {
                    $join->on('receita.ID_Conta', '=', 'conta.ID_Conta')
                        ->where('receita.Efetivada', '=', 1);
                })
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->groupBy('conta.ID_Conta')
                //->toSql(); dd($contas);
                ->get();
            //dd($soma[0]->Despesas);

            $conta->Saldo = $soma[0]->Saldo_inicial + $soma[0]->Receitas - $soma[0]->Despesas;

        }

        return $contas;
    }

    public function showAll(){

        //$contas = DB::table('conta')->paginate(99999);
        $contas = Conta::where(function ($query) {
            $query->select('*');
            $query->orderBy('Banco','ASC');
        })->get();
        return $contas;
    }
}
