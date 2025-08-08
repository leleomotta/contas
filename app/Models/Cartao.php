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
                DB::raw("'000' as Valor"), DB::raw("'000' as Gasto_Total"), DB::raw("'000' as N_Despesas"),
                DB::raw("'00-0000' as Ano_Mes")
                //'fatura.Ano_Mes', 'fatura.data_fechamento', '''0' as Valor''
            )
            //->leftJoin('fatura', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            //and fatura.Ano_Mes = '2024-11'
            ->groupBy('cartao.ID_Cartao')
            ->orderBy('cartao.Nome','ASC')
            //->toSql(); dd($despesas);
            ->get();
        foreach($cartoes as $cartao) {

            //Início
            $faturaPrimeiro = Fatura::where('ID_Cartao', $cartao->ID_Cartao)->where('Ano_Mes', $Ano_Mes)->first();

            $Ano_Mes_local = $Ano_Mes;
            if (!is_null($faturaPrimeiro)) {
                $fechada = $faturaPrimeiro->Fechada == 1;
                if ($faturaPrimeiro && $faturaPrimeiro->Fechada == 1) {
                    $data = Carbon::createFromFormat('Y-m', $Ano_Mes)->addMonth();
                    $Ano_Mes_local = $data->format('Y-m');
                }
            }
            $cartao->Ano_Mes = $Ano_Mes_local;
            //Fim

            $soma = DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('despesa', 'despesa.ID_Despesa','=', 'fatura.ID_Despesa')
                ->where([
                    //['fatura.Ano_Mes','=', $Ano_Mes],
                    ['fatura.Ano_Mes','=', $Ano_Mes_local],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao]
                ])
                //->toSql(); dd($soma);
            ->get();
            $cartao->Valor = $soma[0]->Valor;

            /*
            $soma = DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('despesa', 'despesa.ID_Despesa','=', 'fatura.ID_Despesa')
                ->where([
                    //['fatura.Ano_Mes','=', $Ano_Mes],
                    ['fatura.Ano_Mes','=', $Ano_Mes_local],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao]
                ])
                ->get();
            $cartao->Gasto_Total = $soma[0]->Valor;
            */

            $soma = DB::table('fatura')
                ->select(DB::raw('count(despesa.Valor) as N_Despesas'))
                ->join('despesa', 'despesa.ID_Despesa','=', 'fatura.ID_Despesa')
                ->where([
                    //['fatura.Ano_Mes','=', $Ano_Mes],
                    ['fatura.Ano_Mes','=', $Ano_Mes_local],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao]
                ])
                ->get();
            $cartao->N_Despesas = $soma[0]->N_Despesas;

        }
        return $cartoes;
    }
}
