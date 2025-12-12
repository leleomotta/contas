<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Conta;
use App\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReceitaController extends Controller
{
    /**
     * Gera o date_filter (YYYY-MM) a partir de uma data (YYYY-MM-DD).
     * Se a data estiver vazia/inválida, usa um fallback (ex: request('date_filter'))
     * e, se ainda assim não existir, usa o mês atual.
     */
    private function dateFilterFromDate(?string $date, ?string $fallbackDateFilter = null): string
    {
        // 1) Tenta usar a data do registro (a regra que você pediu)
        if (!empty($date)) {
            try {
                return Carbon::parse($date)->format('Y-m'); // ex: 2025-12
            } catch (\Exception $e) {
                // Se a data estiver em formato inesperado, cai para fallback
            }
        }

        // 2) Se vier um date_filter já pronto (ex: vindo da listagem), usa ele
        if (!empty($fallbackDateFilter)) {
            return $fallbackDateFilter;
        }

        // 3) Último fallback: mês atual
        return Carbon::now()->format('Y-m');
    }

    /**
     * Redireciona para a listagem de receitas no mês da própria receita.
     * - Prioridade: mês da Data da receita
     * - Fallback: date_filter que veio na request
     * - Último fallback: mês atual (dentro do dateFilterFromDate)
     */
    private function redirectToReceitasMonth(Receita $receita, Request $request)
    {
        $dateFilter = $this->dateFilterFromDate($receita->Data, $request->input('date_filter'));

        // Usar route é melhor do que montar URL na mão (mantém padrão do Laravel)
        return redirect()->route('receitas.showAll', [
            'date_filter' => $dateFilter,
        ]);
    }

    /**
     * Monta uma URL de retorno para a listagem de receitas preservando (quando existir)
     * o filtro mensal (date_filter=YYYY-MM).
     *
     * OBS: este sistema usa a listagem /receitas com querystring date_filter,
     * então esta função ajuda a manter a navegação consistente após redirects.
     *
     * @param string|null $dateFilter Formato esperado: YYYY-MM
     * @return string
     */
    private function receitasListUrl(?string $dateFilter = null): string
    {
        // Se não vier filtro, volta para o mês atual
        $dateFilter = $dateFilter ?: Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');

        return '/receitas?date_filter=' . $dateFilter;
    }


    /**
     * Remove a receita especificada do armazenamento.
     *
     * @param int $ID_Receita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $ID_Receita)
    {
        //$receita = Receita::find($ID_Receita);
        $receita = Receita::findOrFail($ID_Receita);

        // Guarda o mês ANTES de excluir
        $dateFilter = $this->dateFilterFromDate($receita->Data, $request->input('date_filter'));


        // REGRA DE NEGÓCIO:
        // Receitas EFETIVADAS (Efetivada = 1) NÃO podem ser alteradas/excluídas.
        if ((int) $receita->Efetivada === 1) {
            return $this->redirectToReceitasMonth($receita, $request)
                ->with('error', 'Esta receita está EFETIVADA e não pode ser excluída.');
        }


        try {
            DB::beginTransaction();

            $receita->delete();

            DB::commit();
            return $this->redirectToReceitasMonth($receita, $request)
                ->with('success', 'Receita excluída com sucesso.');

        } catch (\Exception $e) {
            DB::rollback();
            return back();
        }
        /*
            $url ='/receitas?date_filter=' . \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                \Carbon\Carbon::now()->isoFormat('MM');
            return Redirect::to($url);

        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
        */
    }

    /**
     * Edita a receita especificada.
     *
     * @param int $ID_Receita
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, int $ID_Receita)
    {
        $receita = Receita::find($ID_Receita);

        // REGRA DE NEGÓCIO:
        // Receitas EFETIVADAS (Efetivada = 1) NÃO podem ser alteradas.
        if ((int) $receita->Efetivada === 1) {
            return $this->redirectToReceitasMonth($receita, $request)
                ->with('error', 'Esta receita está EFETIVADA e não pode ser editada.');

        }

        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->show('R');

        return view('receitaEditar', [
            'receita' => $receita,
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    /**
     * Alterna o status de efetivada de uma receita.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function efetiva(Request $request)
    {
        $receita = Receita::find($request->ID_Receita);
        $receita->Efetivada = !$receita->Efetivada;
        $receita->save();
        $dateFilter = $request->date_filter;
        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }
        //$url ='/receitas?date_filter=' . $dateFilter;
        //return redirect::to($url);
        $dateFilter = $this->dateFilterFromDate($receita->Data, $request->input('date_filter'));

        return redirect()->route('receitas.showAll', [
            'date_filter' => $dateFilter,
        ]);

    }

    /**
     * Filtra a listagem de receitas com base nos parâmetros da requisição.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function filter(Request $request)
    {
        $start_date = implode("-", array_reverse(explode("/", substr($request->datas, 0, 10))));
        $end_date = implode("-", array_reverse(explode("/", substr($request->datas, 13, 10))));

        $request["chkCategoria"] = (isset($request["chkCategoria"])) ? 1 : 0;
        $request["chkConta"] = (isset($request["chkConta"])) ? 1 : 0;
        $request["chkTexto"] = (isset($request["chkTexto"])) ? 1 : 0;
        $request["chkDatas"] = (isset($request["chkDatas"])) ? 1 : 0;

        $categoria = $request["chkCategoria"] ? $request->categoria : null;
        $conta = $request["chkConta"] ? $request->conta : null;
        $texto = $request["chkTexto"] ? $request->texto : null;

        if (!$request["chkDatas"]) {
            $start_date = '0001-01-01';
            $end_date = '9999-12-31';
        }

        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','R');
        $receitas = new Receita();

        return view('receitaListar',
            [
                'receitas' => $receitas->filter($categoria, $conta, $texto, $start_date, $end_date),
                'pendente' => $receitas->receitasPendente($categoria, $conta, $texto, $start_date, $end_date),
                'recebido' => $receitas->receitasRecebido($categoria, $conta, $texto, $start_date, $end_date),
                'categorias' => $categorias,
                'contas' => $contas,
            ]);
    }

    /**
     * Exibe a listagem do recurso.
     *
     * @return void
     */
    public function index()
    {
        // Esta função está vazia
    }

    /**
     * Exibe o formulário para criar uma nova receita.
     *
     * @return \Illuminate\View\View
     */
    public function new()
    {
        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->show('R');
        return view('receitaCriar', [
            'categorias' => $categorias,
            'contas' => $contas,
        ]);
    }

    /**
     * Exibe a listagem completa de receitas.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showAll(Request $request)
    {
        $contas = (new \App\Models\Conta)->showAll();
        $categorias = (new \App\Models\Categoria)->showAll()->where('Tipo','=','R');

        $dateFilter = $request->date_filter;
        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        }

        $dt = Carbon::now();
        $dt->setDateFrom($dateFilter . '-15');
        $start_date = Carbon::createFromDate($dt->firstOfMonth())->toDateString();
        $end_date = Carbon::createFromDate($dt->lastOfMonth())->toDateString();
        $categoria = null;
        $conta = null;
        $texto = null;

        $receitas = new Receita();

        return view('receitaListar', [
            'receitas' => $receitas->show($start_date, $end_date),
            'pendente' => $receitas->receitasPendente($categoria, $conta, $texto, $start_date, $end_date),
            'recebido' => $receitas->receitasRecebido($categoria, $conta, $texto, $start_date, $end_date),
            'contas' => $contas,
            'categorias' => $categorias
        ]);
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param Receita $receita
     * @return void
     */
    public function show(Receita $receita)
    {
        // Esta função está vazia
    }

    /**
     * Salva uma nova receita no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $receita = new Receita();

        $receita->Descricao = $request->Descricao;
        $receita->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $receita->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
        $receita->save();

        //$url ='/receitas?date_filter=' . Carbon::now()->isoFormat('Y') . '-' . Carbon::now()->isoFormat('MM');
        //return Redirect::to($url);

        $dateFilter = $this->dateFilterFromDate($receita->Data);

        return redirect()->route('receitas.showAll', [
            'date_filter' => $dateFilter,
        ]);
    }

    /**
     * Atualiza a receita especificada no armazenamento.
     *
     * @param Request $request
     * @param int $ID_Receita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $ID_Receita)
    {
        // findOrFail é melhor que find: se vier ID errado, você descobre na hora (404),
        // ao invés de seguir com dados inconsistentes.
        $receita = Receita::findOrFail($ID_Receita);

        // REGRA DE NEGÓCIO:
        // Receitas EFETIVADAS (Efetivada = 1) NÃO podem ser alteradas.
        if ((int) $receita->Efetivada === 1) {

            // Mensagem “diagnóstica” para você bater o olho e confirmar
            // se o ID que chegou aqui é o mesmo que você achou que estava editando.
            $msg = sprintf(
                "A receita #%d (%s em %s) está EFETIVADA e não pode ser alterada.",
                $receita->ID_Receita,
                $receita->Descricao,
                Carbon::parse($receita->Data)->format('d/m/Y')
            );

            // (Opcional) Log para ver o que chegou no request/rota
            \Log::warning('Tentativa de alterar receita efetivada', [
                'route_ID_Receita' => $ID_Receita,
                'db_ID_Receita' => $receita->ID_Receita,
                'Efetivada' => $receita->Efetivada,
            ]);

            return $this->redirectToReceitasMonth($receita, $request)
                ->with('error', $msg);
        }

        // ... mantém o resto do seu update igual
        $receita->Descricao = $request->Descricao;
        $receita->Valor = str_replace(",",'.',str_replace(".","", str_replace("R$ ","",$request->Valor)));
        $receita->Data = implode("-",array_reverse(explode("/",$request->Data)));
        $receita->ID_Conta = $request->Conta;
        $receita->ID_Categoria = $request->Categoria;

        $receita->Efetivada = (isset($request->Efetivada)) ? 1 : 0;
        $receita->save();

        $dateFilter = $this->dateFilterFromDate($receita->Data);

        return redirect()->route('receitas.showAll', [
            'date_filter' => $dateFilter,
        ]);
    }

}
