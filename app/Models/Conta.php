<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // ✅ IMPORTANTE: Builder correto do Eloquent
use Illuminate\Support\Facades\DB;

class Conta extends Model
{
    use HasFactory;

    /**
     * Tabela no banco.
     */
    protected $table = 'conta';

    /**
     * Chave primária (no seu banco é ID_Conta).
     */
    protected $primaryKey = 'ID_Conta';

    /**
     * =====================================================================
     * GLOBAL SCOPE: por padrão, NÃO traz contas arquivadas
     * =====================================================================
     * Objetivo:
     * - Qualquer lugar que faça Conta::query(), Conta::all(), Conta::pluck(), etc,
     *   automaticamente verá somente Arquivada = 0.
     *
     * IMPORTANTE:
     * - Esse filtro vale para consultas Eloquent.
     * - NÃO vale para DB::table('conta') (Query Builder “cru”), como no seu show().
     */
    protected static function booted(): void
    {
        static::addGlobalScope('nao_arquivadas', function (Builder $builder) {
            // Campo "Arquivada": 0 = ativa / 1 = arquivada
            $builder->where('Arquivada', 0);
        });
    }

    /**
     * =====================================================================
     * ESCAPE HATCH: quando você REALMENTE precisar enxergar arquivadas
     * =====================================================================
     * Ex.: tela "Inativas/Arquivadas", relatórios, manutenção etc.
     */
    public function scopeComArquivadas(Builder $query): Builder
    {
        // Remove apenas o global scope "nao_arquivadas"
        return $query->withoutGlobalScope('nao_arquivadas');
    }

    /**
     * =====================================================================
     * Atalho: somente arquivadas
     * =====================================================================
     * Como o global scope esconderia as arquivadas, primeiro removemos ele e
     * depois filtramos Arquivada = 1.
     */
    public function scopeApenasArquivadas(Builder $query): Builder
    {
        return $query->comArquivadas()->where('Arquivada', 1);
    }

    /**
     * =====================================================================
     * Seu método original "show"
     * =====================================================================
     * Observação importante:
     * - Aqui você usa DB::table('conta'), então o global scope NÃO interfere.
     * - Você já filtra pelo parâmetro $arquivada, então está ok.
     */
    public function show($start_date, $end_date, $arquivada)
    {
        $contas = DB::table('conta')
            ->select(
                'conta.ID_Conta',
                'conta.Nome',
                'conta.Descricao',
                'conta.Banco',
                'conta.Imagem',
                'conta.Cor',
                'conta.Arquivada',
                'conta.Saldo_Inicial',
                DB::raw("000 as Despesas"),
                DB::raw('000 as Receitas'),
                DB::raw("'Entra' as Entradas"),
                DB::raw("'Sai' as Saidas"),
                DB::raw("'MOTTA' as SaldoMes"),
                DB::raw("'---' as Ano_Mes"),
                DB::raw("'MOTTA' as Saldo")
            )
            ->where('conta.Arquivada', $arquivada)
            ->groupBy('conta.ID_Conta')
            ->get();

        foreach ($contas as $conta) {

            // Receitas
            $receitaMes = (new \App\Models\Receita)
                ->receitas($start_date, $end_date, $conta->ID_Conta)
                ->where('Efetivada', 1)
                ->sum('Valor');

            $receitaAte = (new \App\Models\Receita)
                ->receitas(null, $end_date, $conta->ID_Conta)
                ->where('Efetivada', 1)
                ->sum('Valor');

            // Despesas
            $despesaMes = (new \App\Models\Despesa)
                ->despesasSemCartao($start_date, $end_date, $conta->ID_Conta)
                ->where('Efetivada', 1)
                ->sum('Valor');

            $despesaAte = (new \App\Models\Despesa)
                ->despesasSemCartao(null, $end_date, $conta->ID_Conta)
                ->where('Efetivada', 1)
                ->sum('Valor');

            // Cartão pago
            $cartaoPagoMes = (new \App\Models\Despesa)
                ->cartaoPago($start_date, $end_date, $conta->ID_Conta)
                ->sum('Valor');

            $cartaoPagoAte = (new \App\Models\Despesa)
                ->cartaoPago(null, $end_date, $conta->ID_Conta)
                ->sum('Valor');

            // Transferências
            $tranferencias_SaidaMes = (new \App\Models\Transferencia())
                ->tranferenciasSaida($start_date, $end_date, $conta->ID_Conta)
                ->sum('Valor');

            $tranferencias_SaidaAte = (new \App\Models\Transferencia())
                ->tranferenciasSaida(null, $end_date, $conta->ID_Conta)
                ->sum('Valor');

            $tranferencias_EntradaMes = (new \App\Models\Transferencia())
                ->tranferenciasEntrada($start_date, $end_date, $conta->ID_Conta)
                ->sum('Valor');

            $tranferencias_EntradaAte = (new \App\Models\Transferencia())
                ->tranferenciasEntrada(null, $end_date, $conta->ID_Conta)
                ->sum('Valor');

            // Ano/Mês
            $conta->Ano_Mes = substr($start_date, 0, 7);

            // Dentro do mês
            $conta->Despesas = $despesaMes + $cartaoPagoMes;
            $conta->Receitas = $receitaMes;
            $conta->Entradas = $tranferencias_EntradaMes;
            $conta->Saidas   = $tranferencias_SaidaMes;

            // Saldo até a data
            $conta->Saldo = $conta->Saldo_Inicial
                + ($receitaAte + $tranferencias_EntradaAte)
                - ($despesaAte + $cartaoPagoAte + $tranferencias_SaidaAte);

            // Saldo do mês corrente
            $conta->SaldoMes = $receitaMes + $tranferencias_EntradaMes
                - $despesaMes - $cartaoPagoMes - $tranferencias_SaidaMes;
        }

        return $contas;
    }

    /**
     * =====================================================================
     * Seu método original showAll()
     * =====================================================================
     * Ajuste aplicado:
     * - Use self::query() em vez de Conta:: (evita confusão dentro do próprio model)
     * - Com o Global Scope, aqui já virão somente NÃO arquivadas.
     */
    public function showAll()
    {
        $contas = self::query()
            ->orderBy('Banco', 'asc')
            ->orderBy('Nome', 'asc')
            ->get();

        return $contas;
    }
}
