<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Despesa extends Model
{
    use HasFactory;

    protected $table = 'despesa';

    protected $primaryKey = 'ID_Despesa';

    public function conta()
    {
        return $this->hasOne(Conta::class, 'ID_Conta', 'ID_Conta');
    }

    /*
    public function showAll(){

        $receitas = DB::table('receita')
            //->leftJoin('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            //->join('materia', 'prova.idMateria', '=', 'materia.idMateria')
            //->select('prova.*', 'materia.Nome', DB::raw('count(prova_questao.idQuestao) as totQuestoes'))->groupBy('prova.idProva')

            //->toSql(); dd($receitas);
            ->paginate(99999);
        return $receitas;
    }
    */
    public function filter($categoria, $conta, $texto, $start_date, $end_date){
        $filtros = DB::table('despesa')
            ->select('despesa.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->orderBy('Data','DESC');


        if (!is_null($categoria) ){
            $filtros = $filtros->where("despesa.ID_Categoria", "=", $categoria);
        }

        if (!is_null($conta) ){
            $filtros = $filtros->where("despesa.ID_Conta", "=", $conta);
        }

        if (!is_null($texto) ){
            $filtros = $filtros->where("despesa.Descricao", "LIKE", "%" . $texto . "%");
        }

        if ($start_date != '0001-01-01'){
            $filtros = $filtros->whereBetween('Data',[$start_date,$end_date]);
        }
        //dd($filtros);
        //dd($filtros->toSql());
        return $filtros->get();
    }

    public function show($start_date, $end_date){
        //$dt = Carbon::now();
        //$dt->setDateFrom($filtro . '-15');
        $despesas = DB::table('despesa')
            //->select('despesa.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->select('despesa.ID_Despesa', 'despesa.Descricao', 'despesa.Valor', 'despesa.Data',
                    'despesa.Efetivada', 'categoria.Nome as NomeCategoria', 'conta.Banco' )

            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->whereBetween('Data',
                [
                    $start_date,
                    $end_date
                ]
            )
            //->where('despesa.ID_Conta', '1')

            ->whereNull('despesa.ID_Cartao')
            ->orderBy('Data','DESC')
            //->toSql(); dd($despesas);
            ->get();

        $cartao =DB::table('despesa')
            /*
            ->select('despesa.ID_Despesa', 'despesa.Descricao', DB::raw('sum(despesa.Valor) as Valor'),
                'despesa.Data', 'despesa.Efetivada', DB::raw("'cartao.Nome' as NomeCategoria"), 'conta.Banco' )
            */
            ->select('despesa.ID_Despesa', DB::raw("'Cartão' as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                'despesa.Data', 'despesa.Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('cartao', 'despesa.ID_Cartao', '=', 'cartao.ID_Cartao')
            //->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')

            ->whereBetween('Data',
                [
                    $start_date,
                    $end_date
                ]
            )

            ->whereNotNull('despesa.ID_Cartao')
            ->groupBy('despesa.ID_Cartao')
            ->orderBy('Data','DESC')
            //->toSql(); dd($cartao);
            //->paginate(99999);
            ->get();

        $despesas = $despesas->merge($cartao);
        //$despesas = $cartao;
        return $despesas;
    }

    public function pendente($categoria, $conta, $texto, $start_date, $end_date){
        //arrumar a prendencia e recebidos sobre filtro
        $retorno = DB::table('despesa')
            //->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->select('despesa.*')
            ->where('Efetivada','0')
            ->whereBetween('Data',
                [
                    $start_date,
                    $end_date
                ]
            );

            if (!is_null($categoria) ){
                $retorno = $retorno->where("despesa.ID_Categoria", "=", $categoria);
            }

            if (!is_null($conta) ){
                $retorno = $retorno->where("despesa.ID_Conta", "=", $conta);
            }

            if (!is_null($texto) ){
                $retorno = $retorno->where("despesa.Descricao", "LIKE", "%" . $texto . "%");
            }

        //dd($retorno->toSql());
            $retorno->get();

        return $retorno->sum('Valor');
    }

    public function pago($categoria, $conta, $texto, $start_date, $end_date){
        $retorno = DB::table('despesa')
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
             $retorno = $retorno->where("despesa.ID_Categoria", "=", $categoria);
            }

            if (!is_null($conta) ){
                $retorno = $retorno->where("despesa.ID_Conta", "=", $conta);
            }

            if (!is_null($texto) ){
                $retorno = $retorno->where("despesa.Descricao", "LIKE", "%" . $texto . "%");
            }

        //dd($retorno->toSql());
            $retorno->get();

        return $retorno->sum('Valor');
    }

}

