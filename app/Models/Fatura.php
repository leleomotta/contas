<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fatura extends Model
{
    use HasFactory;

    protected $table = 'fatura';

    //protected $primaryKey = 'ID_Despesa'; -> no sistema de questão eu tive que desativar isso

    public function show($Ano_Mes, $ID_Cartao){
        $retorno = DB::table('fatura')
            ->select('fatura.Ano_Mes', 'fatura.Fechada', 'fatura.ID_Cartao',
            'despesa.Data', 'despesa.Descricao', 'despesa.Valor', 'categoria.Nome as NomeCategoria' )
            //->select('receita.*')
            ->join('despesa', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->where('fatura.Ano_Mes',$Ano_Mes)
            ->where('fatura.ID_Cartao', $ID_Cartao)

        //dd($retorno->toSql());
        ->get();
//dd($retorno);
        return $retorno;
    }
}
