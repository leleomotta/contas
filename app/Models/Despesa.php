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

    public function filter($categoria, $conta, $texto, $start_date, $end_date, $agruparCartao = false)
    {
        /*
         * 1. Despesas normais, fora de cartão.
         */
        $despesasSemCartao = DB::table('despesa')
            ->select(
                'despesa.ID_Despesa',
                'despesa.Descricao',
                'despesa.Valor',
                'despesa.Data',
                'despesa.Efetivada',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'icone.Link as Icone',
                'conta.Banco',
                'categoria.Cor',
                DB::raw("'despesa' as Origem")
            )
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('fatura')
                    ->whereRaw('despesa.ID_Despesa = fatura.ID_Despesa');
            });

        if (!is_null($categoria)) {
            $despesasSemCartao->where('despesa.ID_Categoria', '=', $categoria);
        }

        if (!is_null($conta)) {
            $despesasSemCartao->where('despesa.ID_Conta', '=', $conta);
        }

        if (!is_null($texto)) {
            $despesasSemCartao->where('despesa.Descricao', 'LIKE', '%' . $texto . '%');
        }

        if ($start_date != '0001-01-01') {
            $despesasSemCartao->whereBetween('despesa.Data', [$start_date, $end_date]);
        }

        /*
         * 2. Despesas de cartão.
         *
         * Quando NÃO estiver agrupado, cada despesa aparece individualmente.
         * A descrição recebe o nome do cartão:
         *
         * "Galaxy M62 (Inter Black)"
         */
        $despesasCartao = DB::table('fatura')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone');

        if ($agruparCartao) {
            $despesasCartao->select(
                DB::raw('MIN(despesa.ID_Despesa) as ID_Despesa'),
                DB::raw("CONCAT('Cartão (', cartao.Nome, ')') as Descricao"),
                DB::raw('SUM(despesa.Valor) as Valor'),
                DB::raw("COALESCE(MAX(fatura.Data_fechamento), STR_TO_DATE(CONCAT(fatura.Ano_Mes, '-01'), '%Y-%m-%d')) as Data"),
                'fatura.Fechada as Efetivada',
                'cartao.Nome as NomeCategoria',
                'icone.Link as Icone',
                'conta.Banco',
                DB::raw("'#C8C8C8' as Cor"),
                DB::raw("'cartao_agrupado' as Origem")
            )
                ->groupBy(
                    'cartao.ID_Cartao',
                    'cartao.Nome',
                    'fatura.Ano_Mes',
                    'fatura.Fechada',
                    'icone.Link',
                    'conta.Banco'
                );
        } else {
            $despesasCartao->select(
                'despesa.ID_Despesa',
                DB::raw("CONCAT(despesa.Descricao, ' (', cartao.Nome, ')') as Descricao"),
                'despesa.Valor',
                DB::raw("COALESCE(fatura.Data_fechamento, despesa.Data) as Data"),
                'fatura.Fechada as Efetivada',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'icone.Link as Icone',
                'conta.Banco',
                'categoria.Cor',
                DB::raw("'cartao' as Origem")
            );
        }

        if (!is_null($categoria)) {
            $despesasCartao->where('despesa.ID_Categoria', '=', $categoria);
        }

        if (!is_null($conta)) {
            $despesasCartao->where(function ($query) use ($conta) {
                $query->where('despesa.ID_Conta', '=', $conta)
                    ->orWhere('fatura.Conta_fechamento', '=', $conta);
            });
        }

        if (!is_null($texto)) {
            $despesasCartao->where(function ($query) use ($texto) {
                $query->where('despesa.Descricao', 'LIKE', '%' . $texto . '%')
                    ->orWhere('cartao.Nome', 'LIKE', '%' . $texto . '%');
            });
        }

        if ($start_date != '0001-01-01') {
            $startAnoMes = Carbon::parse($start_date)->format('Y-m');
            $endAnoMes = Carbon::parse($end_date)->format('Y-m');

            $despesasCartao->where(function ($query) use ($start_date, $end_date, $startAnoMes, $endAnoMes) {
                $query->whereBetween('fatura.Data_fechamento', [$start_date, $end_date])
                    ->orWhere(function ($subQuery) use ($startAnoMes, $endAnoMes) {
                        $subQuery->whereNull('fatura.Data_fechamento')
                            ->whereBetween('fatura.Ano_Mes', [$startAnoMes, $endAnoMes]);
                    });
            });
        }

        return $despesasSemCartao
            ->get()
            ->merge($despesasCartao->get())
            ->sortByDesc('Data')
            ->values();
    }

    public function show($start_date, $end_date){
        //dd($this->despesasSemCartao($start_date,$end_date, null));
        $despesas = $this->despesasSemCartao($start_date,$end_date, null);

        //dd($despesas->merge($this->cartaoAberto( Carbon::createFromDate($start_date)->isoFormat('Y') .
            //'-' . Carbon::createFromDate($start_date)->isoFormat('MM') ) ));
        $despesas = $despesas->merge($this->cartaoAberto( Carbon::createFromDate($start_date)->isoFormat('Y') .
            '-' . Carbon::createFromDate($start_date)->isoFormat('MM') ) );

        //dd($despesas->merge($this->cartaoPago($start_date, $end_date, null)));
        $despesas = $despesas->merge($this->cartaoPago($start_date, $end_date, null));

        //dd($despesas);
        //leonardo motta
        return $despesas;
    }

    public function showAgrupado($start_date, $end_date){
        //dd($this->despesasSemCartao($start_date,$end_date, null));
        $despesas = $this->despesasSemCartao($start_date,$end_date, null);

        //dd($despesas->merge($this->cartaoAberto( Carbon::createFromDate($start_date)->isoFormat('Y') .
        //'-' . Carbon::createFromDate($start_date)->isoFormat('MM') ) ));
        $despesas = $despesas->merge($this->cartaoAbertoAgrupado(Carbon::createFromDate($start_date)->isoFormat('Y') .
            '-' . Carbon::createFromDate($start_date)->isoFormat('MM') ) );

        //dd($despesas->merge($this->cartaoPago($start_date, $end_date, null)));
        $despesas = $despesas->merge($this->cartaoPagoAgrupado($start_date, $end_date, null));

        //dd($despesas);
        //leonardo motta
        return $despesas;
    }

    public function despesasSemCartao($start_date, $end_date, $conta){
        //$despesasSemCartao = DB::table('despesa')
        $despesasSemCartao = DB::table(DB::raw('/* FUNÇÃO: despesasSemCartao */ despesa'))
            ->select( 'despesa.ID_Despesa', 'despesa.Descricao', 'despesa.Valor', 'despesa.Data', 'despesa.Efetivada',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'icone.Link as Icone', 'conta.Banco', 'categoria.Cor', DB::raw("'despesa' as Origem"))
            ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone');
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

    public function cartaoPago($start_date, $end_date, $conta)
    {
        $cartaoPago = DB::table(DB::raw('/* FUNÇÃO: cartaoPago */ fatura'))
            ->select(
                'despesa.ID_Despesa',
                DB::raw("CONCAT(despesa.Descricao, ' (', cartao.Nome, ')') as Descricao"),
                'despesa.Valor',
                'fatura.Data_fechamento as Data',
                'fatura.Fechada as Efetivada',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'icone.Link as Icone',
                'conta.Banco',
                'categoria.Cor',
                DB::raw("'cartao' as Origem")
            )
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
            ->where('fatura.Fechada', 1);

        if (!is_null($start_date)) {
            $cartaoPago->where('fatura.Data_fechamento', '>=', $start_date);
        }

        if (!is_null($end_date)) {
            $cartaoPago->where('fatura.Data_fechamento', '<=', $end_date);
        }

        if (!is_null($conta)) {
            $cartaoPago->where('conta.ID_Conta', $conta);
        }

        $cartaoPago->orderBy('fatura.Data_fechamento', 'desc');

        return $cartaoPago->get();
    }

    public function cartaoAberto($Ano_Mes)
    {
        $cartaoAberto = DB::table(DB::raw('/* FUNÇÃO: cartaoAberto */ fatura'))
            ->select(
                'despesa.ID_Despesa',
                DB::raw("CONCAT(despesa.Descricao, ' (', cartao.Nome, ')') as Descricao"),
                'despesa.Valor',
                'fatura.Data_fechamento as Data',
                'fatura.Fechada as Efetivada',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'icone.Link as Icone',
                'conta.Banco',
                'categoria.Cor',
                DB::raw("'cartao' as Origem")
            )
            ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
            ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
            ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
            ->where('Ano_Mes', '=', $Ano_Mes)
            ->whereNull('fatura.Data_fechamento')
            ->orderBy('fatura.Data_fechamento', 'desc');
        // ->toSql(); dd($cartaoAberto);

        return $cartaoAberto->get();
    }

    public function cartaoPagoAgrupado($start_date, $end_date, $conta){
        $cartaoPago =DB::table('fatura')
            ->select('despesa.ID_Despesa', DB::raw("CONCAT('Cartão (', cartao.Nome, ')') as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                //DB::raw("'1900-01-01' as Data"), 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'conta.Banco' )
                'fatura.Data_fechamento as Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'icone.Link as Icone', 'conta.Banco',
                DB::raw("'#C8C8C8' as Cor"), DB::raw("'cartao_agrupado' as Origem") )

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

    public function cartaoAbertoAgrupado($Ano_Mes){
        $cartaoAberto =DB::table('fatura')
            ->select('despesa.ID_Despesa', DB::raw("CONCAT('Cartão (', cartao.Nome, ')') as Descricao"), DB::raw('sum(despesa.Valor) as Valor'),
                'fatura.Data_fechamento as Data', 'fatura.Fechada as Efetivada', 'cartao.Nome as NomeCategoria', 'icone.Link as Icone', 'conta.Banco',
                DB::raw("'#C8C8C8' as Cor"), DB::raw("'cartao_agrupado' as Origem"))
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

