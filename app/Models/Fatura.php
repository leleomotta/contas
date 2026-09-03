<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Fatura extends Model
{
    use HasFactory;

    protected $table = 'fatura';
    //protected $primaryKey = ['ID_Despesa', 'ID_Cartao'];
    protected $primaryKey = 'ID_Despesa'; //-> no sistema de questão eu tive que desativar isso
    //protected $primaryKey = ['ID_Cartao', 'Ano_Mes'];

    /**
     * Descobre qual fatura está atualmente aberta para um cartão,
     * considerando a data de referência e o dia de fechamento.
     *
     * IMPORTANTE:
     *
     * No sistema, o Ano_Mes da fatura representa o ciclo que começou
     * no mês anterior ao fechamento.
     *
     * Exemplo para um cartão que fecha dia 15:
     *
     * 01/09/2026 até 14/09/2026 -> fatura 2026-08
     * 15/09/2026 até 14/10/2026 -> fatura 2026-09
     * 15/10/2026 em diante       -> fatura 2026-10
     *
     * Portanto:
     *
     * - ANTES do dia de fechamento:
     *      utiliza o mês anterior.
     *
     * - NO DIA do fechamento ou DEPOIS:
     *      utiliza o próprio mês.
     *
     * Depois disso, verificamos se a fatura calculada já foi
     * fechada manualmente. Se estiver fechada, procuramos a
     * próxima fatura aberta.
     *
     * O parâmetro $cartao é "object" porque este método pode receber:
     *
     * - um Model Eloquent Cartao;
     * - um stdClass retornado pelo Query Builder.
     */
    public static function anoMesFaturaAberta(
        object $cartao,
               $dataReferencia = null
    ): string {

        /*
         * =============================================================
         * DEFINE A DATA DE REFERÊNCIA
         * =============================================================
         *
         * No cadastro da despesa:
         * será a data da compra.
         *
         * Na tela de cartões:
         * será a data atual.
         */
        if ($dataReferencia instanceof Carbon) {

            $data = $dataReferencia->copy();

        } elseif (!empty($dataReferencia)) {

            $data = Carbon::parse($dataReferencia);

        } else {

            $data = Carbon::today();

        }


        /*
         * =============================================================
         * DIA DE FECHAMENTO DO CARTÃO
         * =============================================================
         */
        $diaFechamento = (int) $cartao->Dia_Fechamento_Fatura;


        /*
         * Começamos considerando o próprio mês da data.
         *
         * Exemplo:
         *
         * Data = 03/09/2026
         *
         * inicialmente:
         *
         * $mesFatura = setembro/2026
         */
        $mesFatura = $data->copy()->startOfMonth();


        /*
         * Só aplicamos a regra automática quando o cartão
         * possui um dia de fechamento configurado.
         */
        if ($diaFechamento > 0) {

            /*
             * Proteção para meses que possuem menos dias.
             *
             * Exemplo:
             *
             * cartão fecha dia 31
             * fevereiro possui somente 28 dias
             *
             * nesse caso consideramos o último dia de fevereiro.
             */
            $diaFechamentoEfetivo = min(
                $diaFechamento,
                $data->daysInMonth
            );


            /*
             * =========================================================
             * REGRA PRINCIPAL
             * =========================================================
             *
             * Se AINDA NÃO CHEGOU o dia do fechamento,
             * a fatura aberta pertence ao mês anterior.
             *
             * Exemplo:
             *
             * Hoje:       03/09/2026
             * Fechamento: dia 15
             *
             * 03 < 15
             *
             * Portanto:
             *
             * fatura aberta = 2026-08
             */
            if ($data->day < $diaFechamentoEfetivo) {

                $mesFatura->subMonth();

            }

            /*
             * Se chegou ao dia do fechamento ou passou dele,
             * NÃO fazemos nenhuma alteração.
             *
             * Exemplo:
             *
             * Data:       15/09/2026
             * Fechamento: dia 15
             *
             * resultado:
             *
             * 2026-09
             */
        }


        /*
         * =============================================================
         * GARANTE QUE A FATURA ESTEJA ABERTA
         * =============================================================
         *
         * Exemplo:
         *
         * calculamos 2026-08,
         * mas 2026-08 já foi fechada manualmente.
         *
         * O método abaixo procurará 2026-09.
         */
        return self::proximoAnoMesAberto(
            $cartao,
            $mesFatura
        );
    }


    /**
     * A partir de determinado mês, procura a primeira
     * fatura que ainda esteja aberta.
     *
     * Isso também cobre o caso em que uma fatura tenha
     * sido fechada manualmente antes da data prevista.
     *
     * @param Cartao $cartao
     * @param Carbon $mesReferencia
     * @return string
     */
    public static function proximoAnoMesAberto(
        object $cartao,
        Carbon $mesReferencia
    ): string {

        /*
         * Sempre trabalha no primeiro dia do mês,
         * porque aqui só nos interessa Ano/Mês.
         */
        $mes = $mesReferencia->copy()->startOfMonth();


        /*
         * Continua avançando enquanto encontrar
         * uma fatura marcada como fechada.
         */
        while (true) {

            $anoMes = $mes->format('Y-m');


            /*
             * Como sua tabela "fatura" possui uma linha
             * para cada despesa da fatura, verificamos
             * se existe alguma linha daquele cartão/mês
             * marcada como fechada.
             */
            $fechada = self::where('ID_Cartao', $cartao->ID_Cartao)
                ->where('Ano_Mes', $anoMes)
                ->where('Fechada', 1)
                ->exists();


            /*
             * Se não estiver fechada, encontramos
             * a fatura que deve ser utilizada.
             */
            if (!$fechada) {

                return $anoMes;

            }


            /*
             * Se estiver fechada, tenta o mês seguinte.
             */
            $mes->addMonth();
        }
    }

    public function show($Ano_Mes, $ID_Cartao)
    {
        //Log::info("Fatura Ano_Mes: " . $Ano_Mes);
        //Log::info("Fatura IDCartão: " . $ID_Cartao);
        $retorno = DB::table('fatura')
            ->select(
                'fatura.Ano_Mes', 'fatura.Data_fechamento', 'fatura.Fechada', 'fatura.ID_Cartao', 'despesa.ID_Despesa',
                'despesa.Data', 'despesa.Descricao', 'despesa.Valor', 'icone.Link as Icone',
                DB::raw("COALESCE(CONCAT(categoria_pai.Nome, ' -> ', categoria.Nome), categoria.Nome) AS NomeCategoria"),
                'categoria.Cor as Cor'
            )
            ->join('despesa', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->join('categoria', 'despesa.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->leftJoin('categoria as categoria_pai', 'categoria.ID_Categoria_Pai', '=', 'categoria_pai.ID_Categoria')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
            ->where('fatura.Ano_Mes', $Ano_Mes)
            ->where('fatura.ID_Cartao', $ID_Cartao)
            ->orderBy('despesa.Data', 'DESC')
            ->orderBy('despesa.Descricao', 'ASC')
            ->get();
        return $retorno;
    }


    public function totalFatura($Ano_Mes, $ID_Cartao){

        //$data = Carbon::today()->toDateString();

        $retorno = DB::table('despesa')
            ->select('despesa.valor as Valor')
            ->join('fatura', 'fatura.ID_Despesa', '=', 'despesa.ID_Despesa')
            ->where('fatura.ID_Cartao',$ID_Cartao)
            ->where('fatura.Ano_Mes',$Ano_Mes)
            //->where('despesa.Data', '<', $data) // Verifica se a data da despesa é anterior a hoje
            //->toSql(); dd($retorno);
            ->get();

        return $retorno->sum('Valor');
    }

    public function fatura_fechar($Ano_Mes, $ID_Cartao, $Data, $Conta){

        //$data_fechamento = Carbon::now()->isoFormat('Y-MM-D');

        $retorno = DB::table('fatura')
            ->select('fatura.ID_Despesa', 'fatura.ID_Cartao', 'fatura.Ano_Mes', 'fatura.Data_fechamento',
                'fatura.fechada')
            ->where('fatura.ID_Cartao',$ID_Cartao)
            ->where('fatura.Ano_Mes',$Ano_Mes)
            //->toSql(); dd($retorno);
            ->get();

        foreach($retorno as $despesa) {
            $efetiva = Despesa::find($despesa->ID_Despesa);
            $efetiva->Efetivada = 1;
            $efetiva->ID_Conta = $Conta;
            $efetiva->save();

            Fatura::where(function ($query) use ($ID_Cartao,$Ano_Mes) {
                $query->where('ID_Cartao', '=', $ID_Cartao)
                    ->where('Ano_Mes', '=', $Ano_Mes);
            })->update(['Fechada'=>'1','Data_fechamento'=>$Data,'Conta_fechamento'=>$Conta] );

        }
    }

    public function fatura_reabrir($Ano_Mes, $ID_Cartao){

        $data_fechamento = Carbon::now()->isoFormat('Y-MM-D');

        $retorno = DB::table('fatura')
            ->select('fatura.ID_Despesa', 'fatura.ID_Cartao', 'fatura.Ano_Mes', 'fatura.Data_fechamento',
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
            })->update(['fechada'=>'0','Data_fechamento'=>null,'Conta_fechamento'=>null ] );

        }


    }
}
