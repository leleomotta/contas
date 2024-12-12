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
            ->select('despesa.ID_Despesa', 'despesa.Descricao', 'despesa.Valor', 'despesa.Data',
                'despesa.Efetivada', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('fatura')
                    ->whereRaw('despesa.ID_Despesa = fatura.ID_Despesa');
            })
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
        //dd($filtros->toSql());
        return $filtros->get();
    }

    public function show($start_date, $end_date){
        $despesas = DB::table('despesa')
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
            ->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('fatura')
                    ->whereRaw('despesa.ID_Despesa = fatura.ID_Despesa');
            })
            ->orderBy('Data','DESC')
            //->toSql(); dd($despesas);
            ->get();

        $cartao =DB::table('fatura')
            ->select('despesa.ID_Despesa', DB::raw("'Cartão' as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                //DB::raw("'1900-01-01' as Data"), 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
                'fatura.data_fechamento as Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->join('conta', 'cartao.ID_Conta', '=', 'conta.ID_Conta')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->where('Ano_Mes','=',
                Carbon::createFromDate($start_date)->isoFormat('Y') . '-' .
                        Carbon::createFromDate($start_date)->isoFormat('MM'))
            ->groupBy('cartao.ID_Cartao')
            //->toSql(); dd($cartao);
            ->get();

        $despesas = $despesas->merge($cartao);
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


        $cartao =DB::table('fatura')
            ->select('despesa.ID_Despesa', DB::raw("'Cartão' as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                //DB::raw("'1900-01-01' as Data"), 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
                'fatura.data_fechamento as Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->join('conta', 'cartao.ID_Conta', '=', 'conta.ID_Conta')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->where('Ano_Mes','=',
                Carbon::createFromDate($start_date)->isoFormat('Y') . '-' .
                Carbon::createFromDate($start_date)->isoFormat('MM'))
            ->groupBy('cartao.ID_Cartao')
            //->toSql(); dd($cartao);
            ->get();

        //$despesas = $despesas->merge($cartao);

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

