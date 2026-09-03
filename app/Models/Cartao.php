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

    //public function show($Ano_Mes)
    public function show($Ano_Mes = null)
    {
        $cartoes = DB::table('cartao')
            ->select(
                'cartao.ID_Cartao',
                'cartao.Nome',
                'cartao.Bandeira',
                'cartao.Dia_Vencimento',
                'cartao.Dia_Fechamento_Fatura',
                'cartao.ID_Conta',
                'cartao.Cor',

                // ✅ IMPORTANTE: agora o controller consegue filtrar ativos/inativos
                'cartao.Arquivado',

                // Campos “placeholder” que você já usava
                DB::raw("'000' as Valor"),
                DB::raw("'000' as Gasto_Total"),
                DB::raw("'000' as N_Despesas"),
                DB::raw("'00-0000' as Ano_Mes")
            )

            /**
             * Você está usando groupBy só no ID_Cartao.
             * Isso só funciona se o MySQL estiver com ONLY_FULL_GROUP_BY desabilitado.
             * Como seu sistema já está funcionando assim, mantive o padrão,
             * mas incluí Arquivado no groupBy para não dar comportamento estranho.
             */
            ->groupBy('cartao.ID_Cartao', 'cartao.Arquivado')
            ->orderBy('cartao.Nome', 'ASC')
            //->toSql(); dd($cartoes);
            ->get();

        foreach ($cartoes as $cartao) {

            /**
             * Abaixo está a lógica original do seu método
             * (mantive exatamente a ideia: achar o mês atual aberto etc.)
             */

            /*
             * =============================================================
             * DEFINE QUAL FATURA DEVE SER EXIBIDA NO CARD
             * =============================================================
             *
             * Se foi informado explicitamente um Ano_Mes,
             * respeitamos esse valor.
             *
             * Caso contrário, estamos pedindo a "fatura atual",
             * portanto usamos a regra centralizada:
             *
             * - antes do fechamento = mês atual;
             * - no fechamento/depois = próximo mês;
             * - se estiver fechada = próxima aberta.
             */
            if (!empty($Ano_Mes)) {

                $Ano_Mes_local = $Ano_Mes;

            } else {

                $Ano_Mes_local = Fatura::anoMesFaturaAberta(
                    $cartao,
                    Carbon::today()
                );

            }

            $cartao->Ano_Mes = $Ano_Mes_local;

            // Soma do valor da fatura do mês
            $soma = DB::table('fatura')
                ->select(DB::raw('sum(despesa.Valor) as Valor'))
                ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')
                ->where([
                    ['fatura.Ano_Mes', '=', $Ano_Mes_local],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao],
                ])
                //->toSql(); dd($soma);
                ->first();

            $cartao->Valor = $soma->Valor ?? 0;

            // Conta quantas despesas existem naquela fatura/mês
            $count = DB::table('fatura')
                ->select(DB::raw('count(*) as N_Despesas'))
                ->where([
                    ['fatura.Ano_Mes', '=', $Ano_Mes_local],
                    ['fatura.ID_Cartao', '=', $cartao->ID_Cartao],
                ])
                ->first();

            $cartao->N_Despesas = $count->N_Despesas ?? 0;
        }

        return $cartoes;
    }

}
