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
            ->select('conta.ID_Conta', 'conta.Nome', 'conta.Descricao', 'conta.Banco', 'conta.Imagem', 'conta.Cor', 'conta.Arquivada',
                'conta.Saldo_Inicial',
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
            $receitaMes = (new \App\Models\Receita)->receitas($start_date,$end_date,$conta->ID_Conta)->where('Efetivada', 1)->sum('Valor');

            $receitaAte = (new \App\Models\Receita)->receitas(null,$end_date,$conta->ID_Conta)->where('Efetivada', 1)->sum('Valor');

            $despesaMes = (new \App\Models\Despesa)->despesasSemCartao($start_date,$end_date,$conta->ID_Conta)->where('Efetivada', 1)->sum('Valor');

            $despesaAte  = (new \App\Models\Despesa)->despesasSemCartao(null,$end_date,$conta->ID_Conta)->where('Efetivada', 1)->sum('Valor');

            $cartaoPagoMes = (new \App\Models\Despesa)->cartaoPago($start_date,$end_date,$conta->ID_Conta)->sum('Valor');

            $cartaoPagoAte = (new \App\Models\Despesa)->cartaoPago(null,$end_date,$conta->ID_Conta)->sum('Valor');

            $tranferencias_SaidaMes = (new \App\Models\Transferencia())->tranferenciasSaida($start_date,$end_date,$conta->ID_Conta)->sum('Valor');

            $tranferencias_SaidaAte = (new \App\Models\Transferencia())->tranferenciasSaida(null,$end_date,$conta->ID_Conta)->sum('Valor');

            $tranferencias_EntradaMes = (new \App\Models\Transferencia())->tranferenciasEntrada($start_date,$end_date,$conta->ID_Conta)->sum('Valor');

            $tranferencias_EntradaAte = (new \App\Models\Transferencia())->tranferenciasEntrada(null,$end_date,$conta->ID_Conta)->sum('Valor');

            //coloca o ano mês
            $conta->Ano_Mes = substr($start_date, 0, 7);

            //as receitas e despesas DENTRO DO MÊS ATUAL
            $conta->Despesas = $despesaMes + $cartaoPagoMes;
            $conta->Receitas = $receitaMes;


            //entradas e saídas dentro do mês atual
            $conta->Entradas = $tranferencias_EntradaMes;
            $conta->Saidas = $tranferencias_SaidaMes;

            //Saldo até a data
            $conta->Saldo = $conta->Saldo_Inicial +
                ($receitaAte + $tranferencias_EntradaAte) -
                ($despesaAte + $cartaoPagoAte + $tranferencias_SaidaAte);

            //Saldo do mês corrente
            $conta->SaldoMes = $receitaMes + $tranferencias_EntradaMes
                    - $despesaMes - $cartaoPagoMes - $tranferencias_SaidaMes;

            /*
            //if ($conta->ID_Conta == 5) {
            if(0==0){
                echo($conta->Nome);

                echo('---');
                echo('$conta->Despesas');
                echo(':');
                echo($conta->Despesas);
                echo('---');

                echo('---');
                echo('$despesaMes2');
                echo(':');
                echo($despesaMes );
                echo('---');

                echo('---');
                echo('$cartaoPagoMes[0]->Valor');
                echo(':');
                echo($cartaoPagoMes);
                echo('---');

                echo('---');
                echo('$tranferencias_SaidaMes');
                echo(':');
                echo($tranferencias_SaidaMes);
                echo('---');

                echo('---');
                echo('$receitaMes');
                echo(':');
                echo($receitaMes);
                echo('---');

                echo('---');
                echo('$tranferencias_EntradaMes');
                echo(':');
                echo($tranferencias_EntradaMes);
                echo('---');

                echo('---');
                echo('$conta->Saldo_Inicial');
                echo(':');
                echo($conta->Saldo_Inicial);
                echo('---');

                echo('---');
                echo('$receitaAte');
                echo(':');
                echo($receitaAte);
                echo('---');

                echo('---');
                echo('$tranferencias_EntradaAte');
                echo(':');
                echo($tranferencias_EntradaAte);
                echo('---');

                echo('---');
                echo('$despesaAte');
                echo(':');
                echo($despesaAte);
                echo('---');

                echo('$cartaoPagoAte');
                echo(':');
                echo($cartaoPagoAte);
                echo('---');

                echo('---');
                echo('$tranferencias_SaidaAte');
                echo(':');
                echo($tranferencias_SaidaAte);
                echo('<br>');
            }
            */
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
            $query->orderBy('Nome','ASC');
        })->get();
        return $contas;
    }
}

//precisa criar um show das arquivas?
