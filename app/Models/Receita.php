<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Receita extends Model
{
    use HasFactory;

    protected $table = 'receita';

    protected $primaryKey = 'ID_Receita';

    public function conta()
    {
        return $this->hasOne(Conta::class, 'ID_Conta', 'ID_Conta');
    }

    public function filter($categoria, $conta, $texto, $start_date, $end_date){
        $filtros = DB::table('receita')
            ->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->orderBy('Data','DESC');


        if (!is_null($categoria) ){
            $filtros = $filtros->where("receita.ID_Categoria", "=", $categoria);
        }

        if (!is_null($conta) ){
            $filtros = $filtros->where("receita.ID_Conta", "=", $conta);
        }

        if (!is_null($texto) ){
            $filtros = $filtros->where("receita.Descricao", "LIKE", "%" . $texto . "%");
        }

        if ($start_date != '0001-01-01'){
            $filtros = $filtros->whereBetween('Data',[$start_date,$end_date]);
        }
        //dd($filtros);
        //dd($filtros->toSql());
        return $filtros->get();
    }

    public function show($start_date, $end_date){
        return $this->receitas($start_date,$end_date,null);
    }

    public function receitas($start_date, $end_date, $conta){
        $receitas = DB::table('receita')
            //->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->select(
                'receita.ID_Receita', 'receita.Efetivada', 'receita.Data',
                'receita.Descricao', 'receita.Valor',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'icone.Link as Icone', 'conta.Banco', 'categoria.Cor')
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone');
            //->where('Efetivada', 1);
            if (! is_null($start_date) ) {
                $receitas->where('Data', '>=', $start_date);
            }
            if (! is_null($end_date) ) {
                $receitas->where('Data', '<=', $end_date);
            }
            if (! is_null($conta) ){
                $receitas->where('conta.ID_Conta', $conta);
            }
            $receitas->orderBy('Data','DESC');
            //$receitas->toSql(); dd($receitas);

        return $receitas->get();
    }

    public function receitasPendente($categoria, $conta, $texto, $start_date, $end_date){
        //arrumar a prendencia e recebidos sobre filtro
        $retorno = DB::table('receita')
            //->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->select('receita.*')
            ->where('Efetivada','0')
            ->whereBetween('Data',
                [
                    $start_date,
                    $end_date
                ]
            );

            if (!is_null($categoria) ){
                $retorno = $retorno->where("receita.ID_Categoria", "=", $categoria);
            }

            if (!is_null($conta) ){
                $retorno = $retorno->where("receita.ID_Conta", "=", $conta);
            }

            if (!is_null($texto) ){
                $retorno = $retorno->where("receita.Descricao", "LIKE", "%" . $texto . "%");
            }

        //dd($retorno->toSql());
            $retorno->get();

        return $retorno->sum('Valor');
    }

    public function receitasRecebido($categoria, $conta, $texto, $start_date, $end_date){
        $retorno = DB::table('receita')
            //->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            //->select('receita.*')
            ->where('Efetivada','1')
            ->whereBetween('Data',
                [
                    $start_date,
                    $end_date
                ]
            );
            if (!is_null($categoria) ){
             $retorno = $retorno->where("receita.ID_Categoria", "=", $categoria);
            }

            if (!is_null($conta) ){
                $retorno = $retorno->where("receita.ID_Conta", "=", $conta);
            }

            if (!is_null($texto) ){
                $retorno = $retorno->where("receita.Descricao", "LIKE", "%" . $texto . "%");
            }

        //dd($retorno->toSql());
            $retorno->get();

        return $retorno->sum('Valor');
    }

}

