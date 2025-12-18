<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FaturaController extends Controller
{
    /**
     * Caso sua rota aponte para index(), delegamos para listar()
     * para manter compatibilidade com o que você já tem no projeto.
     */
    public function index(Request $request)
    {
        return $this->listar($request);
    }

    /**
     * Tela "Faturas (Histórico)" - lista agrupada por Ano_Mes (YYYY-MM)
     *
     * Filtros:
     * - Cartão
     * - Mês/Ano de (YYYY-MM)
     * - Mês/Ano até (YYYY-MM)
     * - Status (abertas/fechadas/todas)
     * - Conta de fechamento (ID_Conta)
     * - Ordenar por
     * - Somente com lançamentos (checkbox)
     */
    public function listar(Request $request)
    {
        /**
         * =========================================================
         * 1) Validação server-side (URL pode ser manipulada)
         * =========================================================
         */
        $request->validate([
            // Aceita vazio ou YYYY-MM
            'ano_mes_de'  => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'ano_mes_ate' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],

            // Se vier preenchido, tem que ser um dos valores permitidos
            'status'  => ['nullable', 'in:abertas,fechadas'],
            'ordenar' => ['nullable', 'in:mes_desc,mes_asc,total_desc,total_asc'],
        ]);

        /**
         * =========================================================
         * 2) Leitura/normalização de filtros
         * =========================================================
         *
         * Aceita:
         * - cartao_id (novo)
         * - ID_Cartao (legado)
         */
        $cartaoId = $request->input('cartao_id', $request->input('ID_Cartao'));

        // Se veio cartão na URL, salva em sessão para “lembrar” último cartão
        if (!empty($cartaoId)) {
            $cartaoId = (int)$cartaoId;
            $request->session()->put('ID_Cartao', $cartaoId);
        } else {
            // Se não veio, tenta sessão
            $cartaoId = (int)($request->session()->get('ID_Cartao') ?? 0);
            $cartaoId = $cartaoId > 0 ? $cartaoId : null;
        }

        $anoMesDe  = (string)($request->input('ano_mes_de') ?? '');
        $anoMesAte = (string)($request->input('ano_mes_ate') ?? '');

        // status: '', 'abertas', 'fechadas'
        $status = (string)$request->input('status', '');

        // conta_fechamento: '' ou ID_Conta
        $contaFechamento = $request->input('conta_fechamento');
        $contaFechamento = ($contaFechamento === '' || $contaFechamento === null) ? null : (int)$contaFechamento;

        // ordenar: mes_desc | mes_asc | total_desc | total_asc
        $ordenar = (string)$request->input('ordenar', 'mes_desc');

        // checkbox
        $somenteComLancamentos = (bool)$request->boolean('somente_com_lancamentos');

        // Para o Blade preencher corretamente os campos
        $filtros = [
            'cartao_id' => $cartaoId,
            'ano_mes_de' => $anoMesDe ?: '',
            'ano_mes_ate' => $anoMesAte ?: '',
            'status' => $status,
            'conta_fechamento' => (string)($contaFechamento ?? ''),
            'ordenar' => $ordenar,
            'somente_com_lancamentos' => $somenteComLancamentos ? 1 : 0,
        ];

        /**
         * =========================================================
         * 3) Dados auxiliares (combos)
         * =========================================================
         */
        $cartoes = Cartao::query()
            ->orderBy('Nome', 'asc')
            ->get();

        // Cartão selecionado (para Dia_Fechamento_Fatura)
        $cartao = null;
        if (!empty($cartaoId)) {
            $cartao = Cartao::query()
                ->where('ID_Cartao', $cartaoId)
                ->first();
        }

        /**
         * ✅ FILTRO "Conta de fechamento":
         * - Agora vem da tabela conta => aparecem todas as contas disponíveis.
         * - Se existir a coluna "Arquivada", filtramos apenas as ativas.
         */
        $contasQuery = DB::table('conta')
            ->select([
                'ID_Conta',
                DB::raw("CONCAT(COALESCE(Banco,''), ' - ', COALESCE(Nome,'')) as Label"),
            ])
            ->orderBy('Banco', 'asc')
            ->orderBy('Nome', 'asc');

        if (Schema::hasColumn('conta', 'Arquivada')) {
            $contasQuery->where('Arquivada', '=', 0);
        }

        $contasFechamento = $contasQuery->get();

        /**
         * =========================================================
         * 4) Query do histórico de faturas (agrupado por Ano_Mes)
         * =========================================================
         */
        $faturas = collect();

        if (!empty($cartaoId)) {
            $query = DB::table('fatura')
                ->join('despesa', 'despesa.ID_Despesa', '=', 'fatura.ID_Despesa')

                // Join para pegarmos a descrição da conta (Banco - Nome)
                ->leftJoin('conta as conta_fech', 'conta_fech.ID_Conta', '=', 'fatura.Conta_fechamento')

                ->where('fatura.ID_Cartao', '=', $cartaoId)
                ->selectRaw("
                    fatura.Ano_Mes as Ano_Mes,
                    MIN(COALESCE(fatura.Fechada, 0)) as Fechada,
                    MAX(fatura.Data_fechamento) as Data_fechamento,
                    MAX(fatura.Conta_fechamento) as Conta_fechamento,
                    MAX(CONCAT(COALESCE(conta_fech.Banco,''), ' - ', COALESCE(conta_fech.Nome,''))) as ContaLabel,
                    SUM(COALESCE(despesa.Valor, 0)) as Total,
                    COUNT(*) as Itens
                ")
                ->groupBy('fatura.Ano_Mes');

            // Período (Ano_Mes é YYYY-MM, comparação string funciona)
            if (!empty($anoMesDe)) {
                $query->where('fatura.Ano_Mes', '>=', $anoMesDe);
            }
            if (!empty($anoMesAte)) {
                $query->where('fatura.Ano_Mes', '<=', $anoMesAte);
            }

            // Conta de fechamento
            if (!empty($contaFechamento)) {
                $query->where('fatura.Conta_fechamento', '=', $contaFechamento);
            }

            // Status (HAVING porque Fechada é agregado)
            if ($status === 'abertas') {
                $query->havingRaw('MIN(COALESCE(fatura.Fechada, 0)) = 0');
            } elseif ($status === 'fechadas') {
                $query->havingRaw('MIN(COALESCE(fatura.Fechada, 0)) = 1');
            }

            // Ordenação no SQL (útil principalmente quando NÃO vamos preencher meses zerados)
            switch ($ordenar) {
                case 'mes_asc':
                    $query->orderBy('fatura.Ano_Mes', 'asc');
                    break;
                case 'total_desc':
                    $query->orderByRaw('SUM(COALESCE(despesa.Valor, 0)) desc');
                    break;
                case 'total_asc':
                    $query->orderByRaw('SUM(COALESCE(despesa.Valor, 0)) asc');
                    break;
                case 'mes_desc':
                default:
                    $query->orderBy('fatura.Ano_Mes', 'desc');
                    break;
            }

            $faturas = collect($query->get());

            /**
             * =========================================================
             * 4.1) Preencher meses faltantes (Total=0)
             * =========================================================
             *
             * ATENÇÃO:
             * - Esse passo é o que estava “quebrando” a ordenação antes,
             *   porque o preencherMesesZerados sempre gerava em ordem crescente.
             *
             * SOLUÇÃO:
             * - Preenche, e depois aplicamos a mesma ordenação escolhida (Collection).
             */
            if (!$somenteComLancamentos && !empty($anoMesDe) && !empty($anoMesAte)) {
                $diaFechamentoCartao = ($cartao && !empty($cartao->Dia_Fechamento_Fatura))
                    ? str_pad((string)$cartao->Dia_Fechamento_Fatura, 2, '0', STR_PAD_LEFT)
                    : null;

                $faturas = $this->preencherMesesZerados($faturas, $anoMesDe, $anoMesAte, $diaFechamentoCartao);

                // ✅ Reaplica ordenação após “mexer” na coleção
                $faturas = $this->aplicarOrdenacaoCollection($faturas, $ordenar);
            } else {
                // Mesmo sem preencher, reaplica na collection por segurança (não custa e evita surpresa)
                $faturas = $this->aplicarOrdenacaoCollection($faturas, $ordenar);
            }

            /**
             * =========================================================
             * 4.2) Enriquecimento para o Blade
             * =========================================================
             */
            if ($cartao) {
                $diaFechamento = !empty($cartao->Dia_Fechamento_Fatura)
                    ? str_pad((string)$cartao->Dia_Fechamento_Fatura, 2, '0', STR_PAD_LEFT)
                    : null;

                $faturas = $faturas->map(function ($f) use ($cartao, $diaFechamento) {
                    $dataFechamento = null;

                    // Se a fatura tiver data registrada, usa ela
                    if (!empty($f->Data_fechamento)) {
                        $dataFechamento = Carbon::parse($f->Data_fechamento);
                    } else {
                        // Senão, calcula uma data estimada com base no dia do cartão
                        if (!empty($cartao->Dia_Fechamento_Fatura)) {
                            $dataFechamento = $this->dateFromAnoMesDay(
                                (string)$f->Ano_Mes,
                                (int)$cartao->Dia_Fechamento_Fatura
                            );
                        }
                    }

                    $f->DataFechamentoFmt = $dataFechamento ? $dataFechamento->format('d/m/Y') : null;
                    $f->DiaFechamento = $diaFechamento;

                    return $f;
                });

                // ⚠️ Depois do map(), mantém a ordenação (map preserva índice, mas por garantia reordena)
                $faturas = $this->aplicarOrdenacaoCollection($faturas, $ordenar);
            }
        }

        /**
         * =========================================================
         * 5) Resumo (cards)
         * =========================================================
         */
        $totalPeriodo = $faturas->sum(fn($f) => (float)($f->Total ?? 0));

        $totalAberto = $faturas->sum(function ($f) {
            $fechada = (int)($f->Fechada ?? 0);
            return $fechada === 1 ? 0 : (float)($f->Total ?? 0);
        });

        $resumo = [
            'total_periodo' => $totalPeriodo,
            'total_aberto' => $totalAberto,
            'qtd_meses' => $faturas->count(),
        ];

        /**
         * =========================================================
         * 6) View
         * =========================================================
         */
        return view('faturas_Listar', [
            'cartoes' => $cartoes,
            'cartao' => $cartao,
            'contasFechamento' => $contasFechamento,
            'filtros' => $filtros,
            'faturas' => $faturas,
            'resumo' => $resumo,
        ]);
    }

    /**
     * (Mantido para compatibilidade; hoje você está abrindo fatura pela route cartoes.fatura)
     * Se você não usa mais esse show, pode remover depois.
     */
    public function show(Request $request, $cartao, $anoMes)
    {
        $cartaoId = (int)$cartao;
        $anoMes = (string)$anoMes;

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $anoMes)) {
            abort(404);
        }

        $cartaoObj = Cartao::query()
            ->where('ID_Cartao', $cartaoId)
            ->first();

        // Ajuste a view se você usar esse show; caso contrário, remova a rota
        return view('faturas_Detalhar', [
            'cartao' => $cartaoObj,
            'anoMes' => $anoMes,
        ]);
    }

    /**
     * ✅ Ordena uma Collection de faturas de acordo com o filtro "ordenar"
     * (IMPORTANTE para quando o preencherMesesZerados for usado)
     */
    private function aplicarOrdenacaoCollection(Collection $faturas, string $ordenar): Collection
    {
        // Ordenações possíveis:
        // - mes_desc (Ano_Mes desc)
        // - mes_asc  (Ano_Mes asc)
        // - total_desc (Total desc)
        // - total_asc  (Total asc)

        switch ($ordenar) {
            case 'mes_asc':
                return $faturas->sortBy(fn($f) => (string)($f->Ano_Mes ?? ''))
                    ->values();

            case 'total_desc':
                return $faturas->sortByDesc(fn($f) => (float)($f->Total ?? 0))
                    ->values();

            case 'total_asc':
                return $faturas->sortBy(fn($f) => (float)($f->Total ?? 0))
                    ->values();

            case 'mes_desc':
            default:
                return $faturas->sortByDesc(fn($f) => (string)($f->Ano_Mes ?? ''))
                    ->values();
        }
    }

    /**
     * Helper: cria uma data a partir de Ano_Mes (YYYY-MM) + dia.
     * Se o dia extrapolar o último dia do mês, ajusta para o último dia.
     */
    private function dateFromAnoMesDay(string $anoMes, int $dia): Carbon
    {
        $base = Carbon::createFromFormat('Y-m', $anoMes)->startOfMonth();

        $ultimoDia = $base->copy()->endOfMonth()->day;
        $diaSeguro = max(1, min($dia, $ultimoDia));

        return $base->copy()->day($diaSeguro);
    }

    /**
     * Helper: completa meses faltantes no período com Total=0.
     * Útil quando o usuário desmarca "Somente com lançamentos".
     *
     * IMPORTANTE:
     * - Essa função NÃO decide ordenação.
     * - Ela só garante que todos os meses existam.
     * - A ordenação é aplicada depois por aplicarOrdenacaoCollection().
     */
    private function preencherMesesZerados(Collection $faturas, string $anoMesDe, string $anoMesAte, ?string $diaFechamentoCartao = null): Collection
    {
        $map = $faturas->keyBy('Ano_Mes');

        $ini = Carbon::createFromFormat('Y-m', $anoMesDe)->startOfMonth();
        $fim = Carbon::createFromFormat('Y-m', $anoMesAte)->startOfMonth();

        $lista = collect();

        for ($d = $ini->copy(); $d->lte($fim); $d->addMonth()) {
            $ym = $d->format('Y-m');

            if ($map->has($ym)) {
                $lista->push($map->get($ym));
                continue;
            }

            // Cria um objeto “vazio” com Total=0, mas compatível com o Blade
            $lista->push((object)[
                'Ano_Mes' => $ym,
                'Fechada' => 0,
                'Data_fechamento' => null,
                'Conta_fechamento' => null,
                'ContaLabel' => null,
                'Total' => 0,
                'Itens' => 0,
                'DataFechamentoFmt' => null,
                'DiaFechamento' => $diaFechamentoCartao,
            ]);
        }

        return $lista;
    }
}
