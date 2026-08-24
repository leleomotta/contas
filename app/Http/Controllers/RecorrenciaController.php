<?php

namespace App\Http\Controllers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\{Recorrencia, Despesa, Receita, Fatura, Cartao, Conta, Categoria};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecorrenciaController extends Controller
{
    /**
     * Exibe o formulário para criar uma nova recorrência.
     *
     * @return \Illuminate\View\View
     */
    public function new()
    {
        //$categorias = Categoria::orderBy('Nome')->get();
        $categorias = (new Categoria)->show('D');
        // Garanta que está buscando TODAS as categorias (Receita e Despesa)
        $categorias = (new \App\Models\Categoria)->showAll();
        $contas = Conta::orderBy('Nome')->get();
        $cartoes = Cartao::orderBy('Nome')->get();

        return view('recorrenciaCriar', compact('categorias', 'contas', 'cartoes'));
    }

    // Exibe a lista de recorrências cadastradas
    public function showAll(Request $request)
    {
        $contas = (new Conta)->showAll();
        //$categorias = (new Categoria)->showAll()->where('Tipo','=','D');
        // (use o método que sua Model Categoria usa para pegar todas)
        $categorias = (new \App\Models\Categoria)->showAll();


        $dateFilter = $request->date_filter;
        if (is_null($dateFilter)) {
            $dateFilter = Carbon::now()->format('Y-m');
        }

        $dt = Carbon::parse($dateFilter . '-15');
        $start_date = $dt->copy()->startOfMonth()->toDateString();
        $end_date = $dt->copy()->endOfMonth()->toDateString();


        /*
        $recorrencias = Recorrencia::with('categoria.icone', 'conta', 'cartao')
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereNull('Data_fim')
                    ->orWhereBetween('Data_fim', [$start_date, $end_date]);
            })
            ->orderByDesc('ID_Recorrencia')
            ->get();
        */
        $recorrencias = Recorrencia::with('categoria.icone', 'conta', 'cartao')
            ->orderBy('descricao')
            ->get();

        return view('recorrenciaListar', [
            'recorrencias' => $recorrencias,
            'pendente' => 0,
            'pago' => 0,
            'contas' => $contas,
            'categorias' => $categorias
        ]);
    }

    // Armazena uma nova recorrência no banco de dados
// Em RecorrenciaController.php
    public function store(Request $request)
    {
        $recorrencia = new Recorrencia();

        // Define os campos básicos da recorrência
        $recorrencia->Descricao = $request->Descricao;
        $recorrencia->Tipo = $request->Tipo; // <-- NOVO: Salva o tipo
        $recorrencia->Valor = str_replace(",", ".", str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $recorrencia->ID_Categoria = $request->ID_Categoria;

        // --- LÓGICA DE PAGAMENTO ATUALIZADA ---
        if ($request->Tipo === 'R') {
            // Se for Receita, só pode ser Conta
            $recorrencia->ID_Conta = $request->ID_Conta;
            $recorrencia->ID_Cartao = null;
        } else {
            // Se for Despesa, usa a lógica do TipoPagamento
            if ($request->TipoPagamento === 'conta') {
                $recorrencia->ID_Conta = $request->ID_Conta;
                $recorrencia->ID_Cartao = null;
            } elseif ($request->TipoPagamento === 'cartao') {
                $recorrencia->ID_Conta = null;
                $recorrencia->ID_Cartao = $request->ID_Cartao;
            } else {
                // Caso não seja nenhum (ex: despesa "genérica" se permitido)
                $recorrencia->ID_Conta = null;
                $recorrencia->ID_Cartao = null;
            }
        }
        // --- FIM DA LÓGICA ATUALIZADA ---

        // Define a periodicidade e datas
        $recorrencia->Periodicidade = $request->Periodicidade;
        $recorrencia->Dia_vencimento = $request->DiaVencimento;
        $recorrencia->Data_inicio = Carbon::createFromFormat('d/m/Y', $request->DataInicio)->format('Y-m-d');

        if (!empty($request->DataFim)) {
            $recorrencia->Data_fim = Carbon::createFromFormat('d/m/Y', $request->DataFim)->format('Y-m-d');
        }

        // Define o status da recorrência (ativa ou não)
        $recorrencia->Ativa = isset($request->Ativa) ? 1 : 0;
        $recorrencia->save();

        return Redirect::to('/recorrencias');
    }

    /**
     * Salva uma nova recorrência no banco de dados.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recorrencias_store(Request $request)
    {
        $recorrencia = new Recorrencia();
        dd('recorrencias_storestore');
        $recorrencia->Descricao = $request->Descricao;
        $recorrencia->Valor = str_replace(",", '.', str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $recorrencia->ID_Categoria = $request->ID_Categoria;
        $recorrencia->ID_Conta = ($request->TipoPagamento === 'conta') ? $request->ID_Conta : null;
        $recorrencia->ID_Cartao = ($request->TipoPagamento === 'cartao') ? $request->ID_Cartao : null;
        $recorrencia->Dia_vencimento = $request->DiaVencimento;
        $recorrencia->Periodicidade = $request->Periodicidade;
        $recorrencia->Data_inicio = implode("-", array_reverse(explode("/", $request->DataInicio)));

        if (!empty($request->DataFim)) {
            $recorrencia->Data_fim = implode("-", array_reverse(explode("/", $request->DataFim)));
        } else {
            $recorrencia->Data_fim = null;
        }

        $recorrencia->Ativa = isset($request->Ativa) ? 1 : 0;

        $recorrencia->save();

        $url = '/recorrencias';
        return redirect($url);
    }

    // Geração das despesas recorrentes para um determinado mês e ano
    // Geração das despesas E RECEITAS recorrentes para um determinado mês e ano
    public function gerarRecorrencias($mes, $ano)
    {
        try {
            DB::beginTransaction();
            $recorrencias = Recorrencia::where('Ativa', 1)->get();

            // Novos contadores
            $contadorDespesas = 0;
            $contadorReceitas = 0;

            foreach ($recorrencias as $recorrencia) {
                $dataInicio = Carbon::parse($recorrencia->Data_inicio);
                $dataFim = $recorrencia->Data_fim ? Carbon::parse($recorrencia->Data_fim) : null;
                $diasNoMes = Carbon::create($ano, $mes, 1)->daysInMonth;

                for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                    $dataAtual = Carbon::create($ano, $mes, $dia);

                    // Verifica se a data atual está dentro do intervalo da recorrência
                    if ($dataAtual->lt($dataInicio)) {
                        continue; // pula se estiver antes da data inicial
                    }
                    if ($dataFim && $dataAtual->gt($dataFim)) {
                        continue; // pula se estiver após a data final
                    }

                    $gerar = false;

                    switch ($recorrencia->Periodicidade) {
                        case 'Mensal':
                            // Limpa e valida o dia
                            $diaVenc = (int) trim($recorrencia->Dia_vencimento);
                            if ($diaVenc <= 0 || $diaVenc > 31) {
                                // Se o dia for inválido (ex: 31 em Fev), mas comum (29, 30, 31)
                                // tenta usar o último dia do mês atual.
                                if (in_array($diaVenc, [29, 30, 31]) && $diaVenc > $diasNoMes) {
                                    $gerar = $dataAtual->day == $diasNoMes;
                                } else {
                                    continue 2; // Dia inválido, pula recorrência
                                }
                            } else {
                                $gerar = $dataAtual->day == $diaVenc;
                            }
                            break;

                        case 'Anual':
                            // Verifica formato correto (dd/mm)
                            if (!preg_match('/^\d{2}\/\d{2}$/', $recorrencia->Dia_vencimento)) {
                                continue 2; // pula para a próxima recorrência
                            }
                            [$diaRec, $mesRec] = explode('/', $recorrencia->Dia_vencimento);
                            if (!checkdate((int)$mesRec, (int)$diaRec, $dataAtual->year)) {
                                continue 2;
                            }
                            $gerar = $dataAtual->day == (int)$diaRec && $dataAtual->month == (int)$mesRec;
                            break;

                        case 'Semanal':
                            // Lista dos dias da semana com a primeira letra maiúscula
                            $diasSemana = ['Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                            $diaSemana = $diasSemana[$dataAtual->dayOfWeek];
                            $gerar = $diaSemana === $recorrencia->Dia_vencimento;
                            break;
                    }

                    // Se a data não for válida para gerar, pula
                    if (!$gerar) continue;

                    // --- DIVISÃO DA LÓGICA: RECEITA OU DESPESA ---

                    if ($recorrencia->Tipo == 'R') {
                        // --- LÓGICA PARA GERAR RECEITA ---

                        // Evita gerar duplicatas
                        $jaExiste = Receita::where('Descricao', $recorrencia->Descricao)
                            ->whereDate('Data', $dataAtual->toDateString())
                            ->where('Valor', $recorrencia->Valor)
                            ->exists();

                        if ($jaExiste) continue;

                        // Criação da receita
                        $receita = new Receita();
                        $receita->Descricao = $recorrencia->Descricao;
                        $receita->Valor = $recorrencia->Valor;
                        $receita->Data = $dataAtual->toDateString();
                        $receita->ID_Categoria = $recorrencia->ID_Categoria;
                        $receita->Efetivada = 0; // Padrão para recorrências
                        $receita->Recorrente = 1; // Indica que veio de recorrência

                        // Receita sempre cai em uma conta
                        if (!is_null($recorrencia->ID_Conta)) {
                            $receita->ID_Conta = $recorrencia->ID_Conta;
                            $receita->save();
                            $contadorReceitas++;
                        }

                    } else {
                        // --- LÓGICA PARA GERAR DESPESA (CÓDIGO ORIGINAL) ---

                        // Evita gerar duplicatas
                        $jaExiste = Despesa::where('Descricao', $recorrencia->Descricao)
                            ->whereDate('Data', $dataAtual->toDateString())
                            ->where('Valor', $recorrencia->Valor)
                            ->exists();

                        if ($jaExiste) continue;

                        // Criação da despesa
                        $despesa = new Despesa();
                        $despesa->Descricao = $recorrencia->Descricao;
                        $despesa->Valor = $recorrencia->Valor;
                        $despesa->Data = $dataAtual->toDateString();
                        $despesa->ID_Categoria = $recorrencia->ID_Categoria;
                        $despesa->Efetivada = 0;
                        $despesa->Recorrente = 1;

                        // Define se a despesa é por conta
                        if (!is_null($recorrencia->ID_Conta)) {
                            $despesa->ID_Conta = $recorrencia->ID_Conta;
                            $despesa->save();
                        }
                        // Define se é por cartão
                        elseif (!is_null($recorrencia->ID_Cartao)) {
                            $despesa->save();
                            $cartao = Cartao::find($recorrencia->ID_Cartao);
                            if (!$cartao) continue;

                            $diaFechamento = $cartao->Dia_Fechamento_Fatura ?? 1;
                            $referencia = $dataAtual->copy();
                            if ($dataAtual->day > $diaFechamento) {
                                $referencia->addMonth();
                            }
                            $anoMes = $referencia->format('Y-m');

                            // Cria o vínculo com a fatura
                            $fatura = new Fatura();
                            $fatura->ID_Cartao = $recorrencia->ID_Cartao;
                            $fatura->ID_Despesa = $despesa->ID_Despesa;
                            $fatura->Ano_Mes = $anoMes;
                            $fatura->Fechada = 0;
                            $fatura->save();
                        }
                        $contadorDespesas++;
                    }
                }
            }
            DB::commit();

            // --- MENSAGEM DE SUCESSO ATUALIZADA ---
            $mesNome = \Carbon\Carbon::createFromFormat('m', $mes)->translatedFormat('F');

            // Mensagem em linha única
            $mensagem = "Recorrências geradas para {$mesNome}/{$ano}: {$contadorDespesas} despesa(s) e {$contadorReceitas} receita(s) criadas com sucesso.";

            return redirect()->route('recorrencias.showAll')->with('success', $mensagem);

        } catch (\Exception $e) {
            DB::rollBack();
            // Retorno com mensagem de erro
            Log::error("Erro ao gerar recorrências: " . $e->getMessage());
            return redirect()->route('recorrencias.showAll')->with('error', 'Erro ao gerar recorrências. Por favor, tente novamente.');
        }
    }

    public function edit(int $ID_Recorrencia)
    {
        // Busca a recorrência pelo ID
        $recorrencia = Recorrencia::find($ID_Recorrencia);

        // Busca todas as contas e categorias do tipo 'Despesa'
        $contas = (new \App\Models\Conta)->showAll();
        $cartoes = Cartao::orderBy('Nome')->get();
        //$categorias = (new \App\Models\Categoria)->show('D');
        // CORREÇÃO: Carregar TODAS as categorias
        $categorias = (new \App\Models\Categoria)->showAll();


        return view('recorrenciaEditar', [
            'recorrencia' => $recorrencia,
            'categorias' => $categorias,
            'contas' => $contas,
            'cartoes' => $cartoes
        ]);
    }

    // Em RecorrenciaController.php
    // Em RecorrenciaController.php
    public function update(Request $request, int $ID_Recorrencia)
    {
        $recorrencia = Recorrencia::find($ID_Recorrencia);

        // Atualiza campos básicos
        $recorrencia->Descricao = $request->Descricao;
        $recorrencia->Valor = str_replace(",", ".", str_replace(".", "", str_replace("R$ ", "", $request->Valor)));
        $recorrencia->ID_Categoria = $request->ID_Categoria;

        // Não permitimos alterar o Tipo (R/D) de uma recorrência existente.
        // Portanto, usamos o $recorrencia->Tipo (que veio do DB) para decidir a lógica.

        // --- LÓGICA DE PAGAMENTO ATUALIZADA ---
        if ($recorrencia->Tipo === 'R') {
            // Se for Receita, só pode ser Conta
            $recorrencia->ID_Conta = $request->ID_Conta;
            $recorrencia->ID_Cartao = null;
        } else {
            // Se for Despesa, usa a lógica do TipoPagamento
            if ($request->TipoPagamento === 'conta') {
                $recorrencia->ID_Conta = $request->ID_Conta;
                $recorrencia->ID_Cartao = null;
            } elseif ($request->TipoPagamento === 'cartao') {
                $recorrencia->ID_Conta = null;
                $recorrencia->ID_Cartao = $request->ID_Cartao;
            } else {
                $recorrencia->ID_Conta = null;
                $recorrencia->ID_Cartao = null;
            }
        }
        // --- FIM DA LÓGICA ATUALIZADA ---

        // Datas e periodicidade
        $recorrencia->Periodicidade = $request->Periodicidade;
        $recorrencia->Dia_vencimento = $request->Dia_vencimento;
        $recorrencia->Data_inicio = \Carbon\Carbon::createFromFormat('d/m/Y', $request->DataInicio)->format('Y-m-d');
        $recorrencia->Data_fim = !empty($request->DataFim)
            ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->DataFim)->format('Y-m-d')
            : null;

        // Checkbox
        $recorrencia->Ativa = isset($request->Ativa) ? 1 : 0;

        $recorrencia->save();

        return redirect()->route('recorrencias.showAll');
    }

    public function destroy(int $ID_Recorrencia)
    {
        $recorrencia = Recorrencia::find($ID_Recorrencia);

        try {
            DB::beginTransaction();

            $recorrencia->delete();

            DB::commit();

            $url = '/recorrencias?date_filter=' . \Carbon\Carbon::now()->format('Y-m');
            return redirect($url);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir recorrência.');
        }
    }

    // Novo método para ativar/desativar a recorrência
    public function toggleAtiva(int $ID_Recorrencia)
    {
        $recorrencia = Recorrencia::find($ID_Recorrencia);

        if (!$recorrencia) {
            return response()->json(['success' => false, 'message' => 'Recorrência não encontrada.'], 404);
        }

        $recorrencia->Ativa = !$recorrencia->Ativa; // Inverte o valor booleano
        $recorrencia->save();

        return response()->json([
            'success' => true,
            'message' => 'Status da recorrência atualizado com sucesso!',
            'ativa' => $recorrencia->Ativa
        ]);
    }
}
