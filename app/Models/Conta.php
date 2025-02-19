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

    public function show($start_date, $end_date, $arquivada){

        $contas = DB::table('conta')
            ->select('conta.ID_Conta', 'conta.Descricao', 'conta.Banco', 'conta.Imagem', 'conta.Cor', 'conta.Arquivada',
                DB::raw("000 as Despesas"), DB::raw('000 as Receitas'),
                DB::raw("'Entra' as Entradas"), DB::raw("'Sai' as Saidas"),
                DB::raw("'MOTTA' as SaldoMes"),
                DB::raw("'---' as Ano_Mes"),
                DB::raw("'MOTTA' as Saldo") )
            ->where('conta.Arquivada', $arquivada)
            ->groupBy('conta.ID_Conta')
            //->toSql(); dd($contas);
            ->get();

        foreach($contas as $conta) {

            $receitaMes = DB::table('conta')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(receita.Valor) as Receitas'))
                ->leftJoin('receita', function ($join) use ($start_date, $end_date) {
                    $join->on('receita.ID_Conta', '=', 'conta.ID_Conta')
                        ->where('receita.Efetivada', '=', 1)
                        ->whereBetween('receita.Data', [$start_date, $end_date]); //linha para filtrar a data
                })
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->groupBy('conta.ID_Conta')

                //dd($receitaMes->toSql(), $receitaMes->getBindings());
                //->toSql(); dd($receitaMes);
                //select `conta`.`ID_Conta`, `conta`.`Saldo_inicial`, sum(receita.Valor) as Receitas from `conta` left join `receita` on `receita`.`ID_Conta` = `conta`.`ID_Conta` and `receita`.`Efetivada` = ? and `receita`.`Data` between ? and ? where `conta`.`ID_Conta` = ? group by `conta`.`ID_Conta`
                ->get();

            $receitaAte = DB::table('conta')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(receita.Valor) as Receitas') )
                ->leftJoin('receita', function($join) use ($start_date, $end_date) {
                    $join->on('receita.ID_Conta', '=', 'conta.ID_Conta')
                        ->where('receita.Efetivada', '=', 1)
                        //->whereBetween('receita.Data',[$start_date,$end_date]); //linha para filtrar a data
                        ->where('receita.Data', '<=', $end_date);
                })
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->groupBy('conta.ID_Conta')
                //->toSql(); dd($receitaAte);
                //select `conta`.`ID_Conta`, `conta`.`Saldo_inicial`, sum(receita.Valor) as Receitas from `conta` left join `receita` on `receita`.`ID_Conta` = `conta`.`ID_Conta` and `receita`.`Efetivada` = ? and `receita`.`Data` <= ? where `conta`.`ID_Conta` = ? group by `conta`.`ID_Conta`
                ->get();


            $despesaMes = DB::table('despesa')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(despesa.Valor) as Despesas') )
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
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->orderBy('Data','DESC')
                //->toSql(); dd($despesaMes);
                //select `conta`.`ID_Conta`, `conta`.`Saldo_inicial`, sum(despesa.Valor) as Despesas from `despesa` inner join `conta` on `despesa`.`ID_Conta` = `conta`.`ID_Conta` inner join `categoria` on `despesa`.`ID_Categoria` = `categoria`.`ID_Categoria` where `Data` between ? and ? and not exists (select 1 from `fatura` where despesa.ID_Despesa = fatura.ID_Despesa) and `conta`.`ID_Conta` = ? order by `Data` desc
                ->get();

            $despesaAte = DB::table('despesa')
                ->select('conta.ID_Conta', 'conta.Saldo_inicial',
                    DB::raw('sum(despesa.Valor) as Despesas') )
                ->join('conta', 'despesa.ID_Conta', '=', 'conta.ID_Conta')
                ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
                ->where('Data', '<=', $end_date)
                ->whereNotExists(function($query)
                {
                    $query->select(DB::raw(1))
                        ->from('fatura')
                        ->whereRaw('despesa.ID_Despesa = fatura.ID_Despesa');
                })
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                ->orderBy('Data','DESC')
                //->toSql(); dd($despesaAte);
                //select `conta`.`ID_Conta`, `conta`.`Saldo_inicial`, sum(despesa.Valor) as Despesas from `despesa` inner join `conta` on `despesa`.`ID_Conta` = `conta`.`ID_Conta` inner join `categoria` on `despesa`.`ID_Categoria` = `categoria`.`ID_Categoria` where `Data` <= ? and not exists (select 1 from `fatura` where despesa.ID_Despesa = fatura.ID_Despesa) and `conta`.`ID_Conta` = ? order by `Data` desc
                ->get();

            $cartaoPagoMes =DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
                //->join('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
                ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')

                ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
                ->whereBetween('fatura.Data_fechamento',
                    [
                        $start_date,
                        $end_date
                    ]
                )
                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                //->toSql(); dd($cartaoPagoMes);
                //select sum(despesa.Valor) as Valor from `fatura` inner join `cartao` on `fatura`.`ID_Cartao` = `cartao`.`ID_Cartao` left join `conta` on `fatura`.`Conta_fechamento` = `conta`.`ID_Conta` inner join `despesa` on `despesa`.`ID_Despesa` = `fatura`.`ID_Despesa` where `fatura`.`Data_fechamento` between ? and ? and `conta`.`ID_Conta` = ?
                ->get();

            $cartaoPagoAte =DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('cartao', 'fatura.ID_Cartao', '=', 'cartao.ID_Cartao')
                //->join('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')
                ->leftJoin('conta', 'fatura.Conta_fechamento', '=', 'conta.ID_Conta')

                ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
                ->where('fatura.Data_fechamento', '<=', $end_date)

                ->where('conta.ID_Conta', '=', $conta->ID_Conta)
                //->toSql(); dd($cartaoPagoAte);
                //select sum(despesa.Valor) as Valor from `fatura` inner join `cartao` on `fatura`.`ID_Cartao` = `cartao`.`ID_Cartao` left join `conta` on `fatura`.`Conta_fechamento` = `conta`.`ID_Conta` inner join `despesa` on `despesa`.`ID_Despesa` = `fatura`.`ID_Despesa` where `fatura`.`Data_fechamento` <= ? and `conta`.`ID_Conta` = ?
                ->get();


            $tranferencias_SaidaMes = DB::table('transferencia')
                ->select('transferencia.ID_Transferencia',
                    DB::raw('sum(transferencia.Valor) as Saida') )
                ->where('transferencia.ID_Conta_Origem', '=', $conta->ID_Conta)
                ->whereBetween('transferencia.Data',[$start_date,$end_date]) //linha para filtrar a data
                //->toSql(); dd($tranferencias_SaidaMes);
                //select `transferencia`.`ID_Transferencia`, sum(transferencia.Valor) as Saida from `transferencia` where `transferencia`.`ID_Conta_Origem` = ? and `transferencia`.`Data` between ? and ?
                ->get();

            $tranferencias_SaidaAte = DB::table('transferencia')
                ->select('transferencia.ID_Transferencia',
                    DB::raw('sum(transferencia.Valor) as Saida') )
                ->where('transferencia.ID_Conta_Origem', '=', $conta->ID_Conta)
                ->where('transferencia.Data','<=',$end_date) //linha para filtrar a data
                //->toSql(); dd($tranferencias_SaidaAte);
                //select `transferencia`.`ID_Transferencia`, sum(transferencia.Valor) as Saida from `transferencia` where `transferencia`.`ID_Conta_Origem` = ? and `transferencia`.`Data` <= ?
                ->get();

            $tranferencias_EntradaMes = DB::table('transferencia')
                ->select('transferencia.ID_Transferencia',
                    DB::raw('sum(transferencia.Valor) as Saida') )
                ->where('transferencia.ID_Conta_Destino', '=', $conta->ID_Conta)
                ->whereBetween('transferencia.Data',[$start_date,$end_date]) //linha para filtrar a data
                //->toSql(); dd($tranferencias_EntradaMes);
                //select `transferencia`.`ID_Transferencia`, sum(transferencia.Valor) as Saida from `transferencia` where `transferencia`.`ID_Conta_Destino` = ? and `transferencia`.`Data` between ? and ?
                ->get();

            $tranferencias_EntradaAte = DB::table('transferencia')
                ->select('transferencia.ID_Transferencia',
                    DB::raw('sum(transferencia.Valor) as Saida') )
                ->where('transferencia.ID_Conta_Destino', '=', $conta->ID_Conta)
                ->where('transferencia.Data','<=',$end_date) //linha para filtrar a data
                //->toSql(); dd($tranferencias_EntradaAte);
                //select `transferencia`.`ID_Transferencia`, sum(transferencia.Valor) as Saida from `transferencia` where `transferencia`.`ID_Conta_Destino` = ? and `transferencia`.`Data` <= ?
                ->get();

            //coloca o ano mês
            $conta->Ano_Mes = substr($start_date,0,7);

            //as receitas e despesas DENTRO DO MÊS ATUAL
            if ($cartaoPagoMes->count() > 0){
                //$conta->Despesas = $despesaMes[0]->Despesas + $cartaoPagoMes[0]->Valor + $tranferencias_SaidaMes[0]->Saida;
                $conta->Despesas = $despesaMes[0]->Despesas + $cartaoPagoMes[0]->Valor;
            }
            else{
                //$conta->Despesas = $despesaMes[0]->Despesas + $tranferencias_SaidaMes[0]->Saida;
                $conta->Despesas = $despesaMes[0]->Despesas;
            }

            //$conta->Receitas = $receitaMes[0]->Receitas + $tranferencias_EntradaMes[0]->Saida;
            $conta->Receitas = $receitaMes[0]->Receitas;


            //entradas e saídas dentro do mês atual
            $conta->Entradas = $tranferencias_EntradaMes[0]->Saida;
            $conta->Saidas = $tranferencias_SaidaMes[0]->Saida;

        //$conta->Saldo = $receita[0]->Saldo_inicial + $receita[0]->Receitas + $tranferencias_Entrada[0]->Saida - $conta->Despesas - $tranferencias_Saida[0]->Saida;
            //Saldo até a data
            $conta->Saldo = $receitaAte[0]->Saldo_inicial +
                ($receitaAte[0]->Receitas + $tranferencias_EntradaAte[0]->Saida) -
                ($despesaAte[0]->Despesas + $cartaoPagoAte[0]->Valor + $tranferencias_SaidaAte[0]->Saida);

/*
            if ($conta->ID_Conta == 5) {
                echo( $conta->Descricao );

                echo('---');
                echo( '$conta->Despesas' );
                echo(':');
                echo( $conta->Despesas );
                echo('---');

                if ($cartaoPagoMes->count() > 0){
                    echo('---');
                    echo( '$cartaoPagoMes[0]->Valor' );
                    echo(':');
                    echo( $cartaoPagoMes[0]->Valor );
                    echo('---');
                }

                echo('---');
                echo( '$tranferencias_SaidaMes[0]->Saida' );
                echo(':');
                echo( $tranferencias_SaidaMes[0]->Saida );
                echo('---');

                echo('---');
                echo( '$conta->Receitas' );
                echo(':');
                echo( $conta->Receitas );
                echo('---');

                echo('---');
                echo( '$tranferencias_EntradaMes[0]->Saida' );
                echo(':');
                echo( $tranferencias_EntradaMes[0]->Saida );
                echo('---');

                echo('---');
                echo( '$receitaAte[0]->Saldo_inicial' );
                echo(':');
                echo( $receitaAte[0]->Saldo_inicial );
                echo('---');

                echo('---');
                echo( '$receitaAte[0]->Receitas' );
                echo(':');
                echo( $receitaAte[0]->Receitas );
                echo('---');

                echo('---');
                echo( '$tranferencias_EntradaAte[0]->Saida' );
                echo(':');
                echo( $tranferencias_EntradaAte[0]->Saida );
                echo('---');

                echo('---');
                echo( '$despesaAte[0]->Despesas' );
                echo(':');
                echo( $despesaAte[0]->Despesas );
                echo('---');

                echo('---');
                echo( '$cartaoPagoAte[0]->Valor' );
                echo(':');
                echo( $cartaoPagoAte[0]->Valor );
                echo('---');

                echo('---');
                echo( '$tranferencias_SaidaAte[0]->Saida' );
                echo(':');
                echo( $tranferencias_SaidaAte[0]->Saida );
                echo('---');
            }
*/
            //Saldo do mês corrente
            $conta->SaldoMes = $receitaMes[0]->Receitas + $tranferencias_EntradaMes[0]->Saida
                            - $despesaMes[0]->Despesas - $cartaoPagoMes[0]->Valor - $tranferencias_SaidaMes[0]->Saida;
        }


        //assim ordena o array collection depois de pronto
        /*
        $sorted = $contas->sortBy(function($conta)
        {
            return $conta->Descricao;
        });
        dd($sorted);
        */
        return $contas;
    }

    public function showAll(){

        //$contas = DB::table('conta')->paginate(99999);
        $contas = Conta::where(function ($query) {
            $query->select('*');
            //$query->where('Arquivada',0);
            $query->orderBy('Banco','ASC');
        })->get();
        return $contas;
    }
}

//precisa criar um show das arquivas?
