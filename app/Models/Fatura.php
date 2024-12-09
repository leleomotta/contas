<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Fatura extends Model
{
    use HasFactory;

    protected $table = 'fatura';
    //protected $primaryKey = ['ID_Despesa', 'ID_Cartao'];
    protected $primaryKey = 'ID_Despesa'; //-> no sistema de questão eu tive que desativar isso
    //protected $primaryKey = ['ID_Cartao', 'Ano_Mes'];

    public function show($Ano_Mes, $ID_Cartao){
        $retorno = DB::table('fatura')
            ->select('fatura.Ano_Mes', 'fatura.Fechada', 'fatura.ID_Cartao', 'despesa.ID_Despesa',
            'despesa.Data', 'despesa.Descricao', 'despesa.Valor', 'categoria.Nome as NomeCategoria' )
            //->select('receita.*')
            ->join('despesa', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->where('fatura.Ano_Mes',$Ano_Mes)
            ->where('fatura.ID_Cartao', $ID_Cartao)

    //->toSql(); dd($retorno);
            ->get();
    //dd($retorno);
        return $retorno;
    }

    public function totalFatura($Ano_Mes, $ID_Cartao){
        $retorno = DB::table('despesa')
            ->select('despesa.valor as Valor')
            ->join('fatura', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->where('fatura.ID_Cartao',$ID_Cartao)
            ->where('fatura.Ano_Mes',$Ano_Mes)
            ->get();

        return $retorno->sum('Valor');
    }

    public function fatura_fechar($Ano_Mes, $ID_Cartao){

        $data_fechamento = Carbon::now()->isoFormat('Y-MM-D');

        $retorno = DB::table('fatura')
            ->select('fatura.ID_Despesa', 'fatura.ID_Cartao', 'fatura.Ano_Mes', 'fatura.data_fechamento',
                'fatura.fechada')
            ->where('fatura.ID_Cartao',$ID_Cartao)
            ->where('fatura.Ano_Mes',$Ano_Mes)
            //->toSql(); dd($retorno);
            ->get();

        foreach($retorno as $despesa) {
            $efetiva = Despesa::find($despesa->ID_Despesa);
            $efetiva->Efetivada = 1;
            $efetiva->save();

            Fatura::where(function ($query) use ($ID_Cartao,$Ano_Mes) {
                $query->where('ID_Cartao', '=', $ID_Cartao)
                    ->where('Ano_Mes', '=', $Ano_Mes);
            })->update(['fechada'=>'1','data_fechamento'=>$data_fechamento] );

        }


    }

    public function fatura_reabrir($Ano_Mes, $ID_Cartao){

        $data_fechamento = Carbon::now()->isoFormat('Y-MM-D');

        $retorno = DB::table('fatura')
            ->select('fatura.ID_Despesa', 'fatura.ID_Cartao', 'fatura.Ano_Mes', 'fatura.data_fechamento',
                'fatura.fechada')
            ->where('fatura.ID_Cartao',$ID_Cartao)
            ->where('fatura.Ano_Mes',$Ano_Mes)
            //->toSql(); dd($retorno);
            ->get();

        foreach($retorno as $despesa) {
            $efetiva = Despesa::find($despesa->ID_Despesa);
            $efetiva->Efetivada = 0;
            $efetiva->save();

            Fatura::where(function ($query) use ($ID_Cartao,$Ano_Mes) {
                $query->where('ID_Cartao', '=', $ID_Cartao)
                    ->where('Ano_Mes', '=', $Ano_Mes);
            })->update(['fechada'=>'0','data_fechamento'=>null ] );

        }


    }
}
