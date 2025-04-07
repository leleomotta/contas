<?php

namespace App\Models;

use Hamcrest\BaseDescription;
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
        $despesas = $this->despesasSemCartao($start_date,$end_date, null);

        $despesas = $despesas->merge($this->cartaoAberto( Carbon::createFromDate($start_date)->isoFormat('Y') .
            '-' . Carbon::createFromDate($start_date)->isoFormat('MM') ) );

        $despesas = $despesas->merge($this->cartaoPago($start_date, $end_date, null));

        return $despesas;
    }

    public function despesasSemCartao($start_date, $end_date, $conta){
        $despesasSemCartao = DB::table('despesa')
            ->select('despesa.ID_Despesa', 'despesa.Descricao', 'despesa.Valor', 'despesa.Data',
                'despesa.Efetivada', 'categoria.Nome as NomeCategoria', 'icone.Link as Icone', 'conta.Banco',
                'categoria.Cor')
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftjoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone');
            //->where('Efetivada', 1);
            if (! is_null($start_date) ) {
                $despesasSemCartao->where('Data', '>=', $start_date);
            }
            if (! is_null($end_date) ) {
                $despesasSemCartao->where('Data', '<=', $end_date);
            }

            if (! is_null($conta) ){
                $despesasSemCartao->where('conta.ID_Conta', $conta);
            }
            $despesasSemCartao->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('fatura')
                    ->whereRaw('despesa.ID_Despesa = fatura.ID_Despesa');
            })
            ->orderBy('Data','DESC');
            //dd($despesasSemCartao->toSql());

            return $despesasSemCartao->get();
    }

    public function despesasDeCartao($start_date, $end_date, $conta){
        $despesasDeCartao =DB::table('fatura')
            ->select('despesa.ID_Despesa', 'despesa.Descricao', 'despesa.Valor',
                'despesa.Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            //->join('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')

            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa');

        if (! is_null($start_date) ) {
            $despesasDeCartao->where('fatura.Data_fechamento', '>=', $start_date);
        }
        if (! is_null($end_date) ) {
            $despesasDeCartao->where('fatura.Data_fechamento', '<=', $end_date);
        }
        if (! is_null($conta) ){
            $despesasDeCartao->where('conta.ID_Conta', $conta);
        }
        //dd($cartaoPago->toSql());
        return $despesasDeCartao->get();
        /*
        $despesasDeCartao = DB::table('despesa')
            ->select('despesa.ID_Despesa', 'despesa.Descricao', 'despesa.Valor', 'despesa.Data',
                'despesa.Efetivada', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria');
        if (! is_null($start_date) ) {
            $despesasDeCartao->where('Data', '>=', $start_date);
        }
        if (! is_null($end_date) ) {
            $despesasDeCartao->where('Data', '<=', $end_date);
        }

        if (! is_null($conta) ){
            $despesasDeCartao->where('conta.ID_Conta', $conta);
        }
        $despesasDeCartao->whereExists(function($query)
        {
            $query->select(DB::raw(1))
                ->from('fatura')
                ->whereRaw('despesa.ID_Despesa = fatura.ID_Despesa');
        })
            ->orderBy('Data','DESC');
        //dd($despesasDeCartao->toSql());

        return $despesasDeCartao->get();
        */
    }

    public function cartaoPago($start_date, $end_date, $conta){
        $cartaoPago =DB::table('fatura')
            ->select('despesa.ID_Despesa', DB::raw("'Cartão' as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                //DB::raw("'1900-01-01' as Data"), 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
                'fatura.Data_fechamento as Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'icone.Link as Icone', 'conta.Banco',
                DB::raw("'#C8C8C8' as Cor"))

            ->leftJoin('icone', 'icone.ID_Icone', '=', DB::raw('0'))
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            //->join('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')

            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->where('fatura.Fechada', 1)
            ->groupBy('cartao.ID_Cartao','fatura.Ano_Mes');

            if (! is_null($start_date) ) {
                $cartaoPago->where('fatura.Data_fechamento', '>=', $start_date);
            }
            if (! is_null($end_date) ) {
                $cartaoPago->where('fatura.Data_fechamento', '<=', $end_date);
            }
            if (! is_null($conta) ){
                $cartaoPago->where('conta.ID_Conta', $conta);
            }
        //dd($cartaoPago->toSql());
        return $cartaoPago->get();
    }

    public function cartaoAberto($Ano_Mes){
        $cartaoAberto =DB::table('fatura')
            ->select('despesa.ID_Despesa', DB::raw("'Cartão' as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                'fatura.Data_fechamento as Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'icone.Link as Icone', 'conta.Banco',
                DB::raw("'#C8C8C8' as Cor"))
            ->leftJoin('icone', 'icone.ID_Icone', '=', DB::raw('0'))
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            //->join('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->where('Ano_Mes','=',  $Ano_Mes)
            ->whereNull('fatura.Data_fechamento')
            ->groupBy('cartao.ID_Cartao');
            //->toSql(); dd($cartaoAberto);

        return $cartaoAberto->get();
    }



}

