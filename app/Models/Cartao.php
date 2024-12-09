<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Cartao extends Model
{
    use HasFactory;

    protected $table = 'cartao';

    protected $primaryKey = 'ID_Cartao';

    public function show($Ano_Mes){
        $cartoes = DB::table('cartao')
            ->select('cartao.ID_Cartao', 'cartao.Nome', 'cartao.Bandeira', 'cartao.Dia_Vencimento',
                'cartao.ID_Conta', 'cartao.Cor',
                DB::raw("'000' as Valor"), DB::raw("'000' as Gasto_Total")
                //'fatura.Ano_Mes', 'fatura.data_fechamento', '''0' as Valor''
            )
            //->leftJoin('fatura', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            //and fatura.Ano_Mes = '2024-11'
            ->groupBy('cartao.ID_Cartao')
            ->orderBy('cartao.Nome','ASC')
            //->toSql(); dd($despesas);
            ->get();
        foreach($cartoes as $cartao) {
            $soma = DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('despesa', 'despesa.ID_Despesa','=', 'fatura.ID_Despesa')
                ->where([
                    ['fatura.Ano_Mes','=', $Ano_Mes],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao]
                ])
            ->get();
            $cartao->Valor = $soma[0]->Valor;

            $soma = DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('despesa', 'despesa.ID_Despesa','=', 'fatura.ID_Despesa')
                ->where([
                    //['fatura.Ano_Mes','=', $Ano_Mes],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao]
                ])
                ->get();
            $cartao->Gasto_Total = $soma[0]->Valor;
        }
        return $cartoes;
    }
}
