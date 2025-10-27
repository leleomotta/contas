@extends('adminlte::page')

@section('title', 'Análise Avançada')

@section('content_header')
    <h1>Análise Avançada</h1>
@stop

@section('content')

    {{-- 1. CARD DE FILTROS --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">🔍 Filtros Avançados</h3>
        </div>

        {{-- Formulário de Filtro --}}
        <form id="formFiltros" role="form" action="{{ route('relatorio.analitico') }}" method="GET">
            <div class="card-body">
                <div class="row">

                    {{-- Filtro: Período (Data Início) --}}
                    <div class="col-md-3">
                        <label>Data Início</label>
                        <div class="input-group date" id="DataInicio" data-target-input="nearest">
                            <div class="input-group-append" data-target="#DataInicio" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                            <input type="text" name="data_inicio" class="form-control datetimepicker-input" data-target="#DataInicio"
                                   value="{{ $inputs['data_inicio'] ?? \Carbon\Carbon::now()->startOfYear()->format('d/m/Y') }}"/>
                        </div>
                    </div>

                    {{-- Filtro: Período (Data Fim) --}}
                    <div class="col-md-3">
                        <label>Data Fim</label>
                        <div class="input-group date" id="DataFim" data-target-input="nearest">
                            <div class="input-group-append" data-target="#DataFim" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                            <input type="text" name="data_fim" class="form-control datetimepicker-input" data-target="#DataFim"
                                   value="{{ $inputs['data_fim'] ?? \Carbon\Carbon::now()->endOfYear()->format('d/m/Y') }}"/>
                        </div>
                    </div>

                    {{-- Filtro: Tipo (Receita/Despesa) --}}
                    <div class="col-md-3">
                        <label>Tipo</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="todos" {{ ($inputs['tipo'] ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="D" {{ ($inputs['tipo'] ?? '') == 'D' ? 'selected' : '' }}>Apenas Despesas</option>
                            <option value="R" {{ ($inputs['tipo'] ?? '') == 'R' ? 'selected' : '' }}>Apenas Receitas</option>
                        </select>
                    </div>

                    {{-- Filtro: Agrupar por --}}
                    <div class="col-md-3">
                        <label>Agrupar por</label>
                        <select name="agrupar" id="agrupar" class="form-control">
                            <option value="day" {{ ($inputs['agrupar'] ?? 'month') == 'day' ? 'selected' : '' }}>Dia</option>
                            <option value="month" {{ ($inputs['agrupar'] ?? 'month') == 'month' ? 'selected' : '' }}>Mês</option>
                            <option value="year" {{ ($inputs['agrupar'] ?? 'month') == 'year' ? 'selected' : '' }}>Ano</option>
                        </select>
                    </div>

                </div>

                <div class="row mt-3">

                    {{-- Filtro: Categorias (Multi-select) --}}
                    <div class="col-md-4">
                        <label>Categorias</label>
                        <select name="categorias[]" id="categorias" class="form-control selectpicker"
                                multiple data-live-search="true" title="Todas as Categorias">
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->ID_Categoria }}"
                                    {{ in_array($cat->ID_Categoria, $inputs['categorias'] ?? []) ? 'selected' : '' }}>
                                    {{ $cat->Nome }} ({{ $cat->Tipo == 'R' ? 'Receita' : 'Despesa' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro: Contas (Multi-select) --}}
                    <div class="col-md-4">
                        <label>Contas</label>
                        <select name="contas[]" id="contas" class="form-control selectpicker"
                                multiple data-live-search="true" title="Todas as Contas">
                            @foreach($contas as $conta)
                                <option value="{{ $conta->ID_Conta }}"
                                    {{ in_array($conta->ID_Conta, $inputs['contas'] ?? []) ? 'selected' : '' }}>
                                    {{ $conta->Nome }} - {{ $conta->Banco }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro: Cartões (Multi-select) --}}
                    <div class="col-md-4">
                        <label>Cartões</label>
                        <select name="cartoes[]" id="cartoes" class="form-control selectpicker"
                                multiple data-live-search="true" title="Todos os Cartões">
                            @foreach($cartoes as $cartao)
                                <option value="{{ $cartao->ID_Cartao }}"
                                    {{ in_array($cartao->ID_Cartao, $inputs['cartoes'] ?? []) ? 'selected' : '' }}>
                                    {{ $cartao->Nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    <a href="{{ route('relatorio.analitico') }}" class="btn btn-default">Limpar</a>
                </div>
            </div>
        </form>
    </div>

    {{-- 2. LINHA PARA OS GRÁFICOS E TABELAS --}}
    <div class="row">
        {{-- Coluna para Gráfico de Evolução e Tabela Detalhada --}}
        <div class="col-lg-8">

            {{-- Gráfico 1: Evolução Financeira --}}
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">📊 Evolução Financeira</h3>
                </div>
                <div class="card-body">
                    <canvas id="evolucaoFinanceiraChart"></canvas>
                </div>
            </div>

            {{-- Tabela 2: Todos os Lançamentos (Detalhado) --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">📋 Todos os Lançamentos</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="tabelaDetalhada" class="table table-bordered table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Conta/Cartão</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse ($detalhadoData as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->Data)->format('d/m/Y') }}</td>
                                    <td>{{ $item->Descricao }}</td>
                                    <td>
                                        @if ($item->Tipo == 'R')
                                            <span class="badge badge-success">Receita</span>
                                        @else
                                            <span class="badge badge-danger">Despesa</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->Categoria_Nome }}</td>
                                    <td>
                                        {{-- Mostra a Conta ou o Cartão --}}
                                        {{ $item->Conta_Nome ?? $item->Cartao_Nome ?? '-' }}
                                    </td>
                                    <td class="font-weight-bold {{ $item->Tipo == 'R' ? 'text-success' : 'text-danger' }}">
                                        R$ {{ number_format($item->Valor, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                {{-- Esta linha é mostrada pelo DataTables, mas é bom ter um fallback --}}
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum lançamento encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Coluna para Gráfico Top 10 e Tabela Comparativa --}}
        <div class="col-lg-4">

            {{-- Gráfico 2: Top 10 Despesas --}}
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">💰 Maiores Despesas (Top 10)</h3>
                </div>
                <div class="card-body">
                    <canvas id="topDespesasChart" style="height: 300px;"></canvas>
                </div>
            </div>

            {{-- Tabela 1: Comparativo de Despesas --}}
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        📈 Comparativo de Despesas
                        {{-- NOVO: Ícone de "hint" com tooltip --}}
                        <i class="fa fa-info-circle text-muted ml-2"
                           data-toggle="tooltip"
                           data-placement="top"
                           title="Compara o período selecionado com um período anterior de mesma duração.">
                        </i>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">

                    {{-- INFORMA O PERÍODO ANTERIOR --}}
                    <div class="p-2 text-sm text-muted">
                        Período Anterior: {{ $periodoAnterior ?? 'N/A' }}
                    </div>

                    <table id="tabelaComparativa" class="table table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Período Atual</th>
                            <th>Período Anterior</th>
                            <th>Variação</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- PREENCHE OS DADOS --}}
                        @php $totalAtual = 0; $totalAnterior = 0; @endphp
                        @forelse ($comparativoData as $item)
                            @php $totalAtual += $item['atual']; $totalAnterior += $item['anterior']; @endphp
                            <tr>
                                <td>{{ $item['categoria'] }}</td>
                                <td>R$ {{ number_format($item['atual'], 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($item['anterior'], 2, ',', '.') }}</td>
                                <td>
                                    @if ($item['variacao'] > 0)
                                        <span class="text-danger font-weight-bold">
                                                <i class="fa fa-arrow-up"></i> {{ number_format($item['variacao_perc'], 1, ',', '.') }}%
                                            </span>
                                    @elseif ($item['variacao'] < 0)
                                        <span class="text-success font-weight-bold">
                                                <i class="fa fa-arrow-down"></i> {{ number_format($item['variacao_perc'], 1, ',', '.') }}%
                                            </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Nenhum dado.</td></tr>
                        @endforelse

                        {{-- LINHA DE TOTAL --}}
                        @if ($comparativoData->count() > 0)
                            @php
                                $totalVariacao = $totalAtual - $totalAnterior;
                                $totalVariacaoPerc = ($totalAnterior > 0) ? ($totalVariacao / $totalAnterior) * 100 : ($totalAtual > 0 ? 100 : 0);
                            @endphp
                            <tr class="bg-light font-weight-bold">
                                <td>Total</td>
                                <td>R$ {{ number_format($totalAtual, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($totalAnterior, 2, ',', '.') }}</td>
                                <td>
                                    @if ($totalVariacao > 0)
                                        <span class="text-danger">
                                                <i class="fa fa-arrow-up"></i> {{ number_format($totalVariacaoPerc, 1, ',', '.') }}%
                                            </span>
                                    @elseif ($totalVariacao < 0)
                                        <span class="text-success">
                                                <i class="fa fa-arrow-down"></i> {{ number_format($totalVariacaoPerc, 1, ',', '.') }}%
                                            </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@stop

{{--
    SEÇÃO DE CSS
    Inclui libs para Datepicker e Select (iguais às suas outras telas)
--}}
@section('css')
    {{-- Tempus Dominus (Datepicker) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    {{-- Bootstrap Select --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

{{--
    SEÇÃO DE JS
    Inclui libs para Datepicker, Select, Chart.js e DataTables
--}}
@section('js')
    {{-- Moment.js (dependência do Tempus Dominus) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    {{-- Tempus Dominus (Datepicker) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>

    {{-- Bootstrap Select (VERSÕES UNIFICADAS para 1.13.18) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/i18n/defaults-pt_BR.min.js"></script>

    {{-- !!! A CORREÇÃO ESTÁ AQUI !!! --}}
    {{-- Adicionando o Chart.js v2.9.4, que é compatível com AdminLTE e com o código abaixo --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {

            // 1. Inicializar Datepickers
            $('#DataInicio').datetimepicker({ format: 'DD/MM/YYYY' });
            $('#DataFim').datetimepicker({ format: 'DD/MM/YYYY' });

            // 2. Inicializar Bootstrap Select
            $('.selectpicker').selectpicker();

            // 2b. Inicializar Tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // 3. Inicializar Tabela Detalhada (DataTables)
            $('#tabelaDetalhada').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                "order": [[0, "desc"]]
            });

            // 4. Lógica dos Gráficos (Sintaxe v2.9, que agora irá funcionar)

            // --- GRÁFICO 1: EVOLUÇÃO FINANCEIRA (Sintaxe v2.9) ---
            var ctxEvolucao = document.getElementById('evolucaoFinanceiraChart').getContext('2d');
            var evolucaoData = @json($evolucaoData);

            new Chart(ctxEvolucao, { // Esta linha agora deve funcionar
                type: 'bar',
                data: evolucaoData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        xAxes: [{
                            stacked: false,
                        }],
                        yAxes: [{
                            stacked: false,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                let label = data.datasets[tooltipItem.datasetIndex].label || '';
                                if (label) { label += ': '; }
                                let value = tooltipItem.yLabel;
                                label += 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                return label;
                            }
                        }
                    }
                }
            });

            // --- GRÁFICO 2: TOP 10 DESPESAS (Sintaxe v2.9) ---
            var ctxTopDespesas = document.getElementById('topDespesasChart').getContext('2d');
            var topDespesasData = @json($topDespesasData);

            if (topDespesasData.labels && topDespesasData.labels.length > 0) {
                new Chart(ctxTopDespesas, { // Esta linha agora deve funcionar
                    type: 'pie',
                    data: topDespesasData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 20,
                                padding: 10
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    let label = data.labels[tooltipItem.index] || '';
                                    let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] || 0;

                                    let total = 0;
                                    data.datasets[tooltipItem.datasetIndex].data.forEach(v => total += parseFloat(v));
                                    let percentage = ((value / total) * 100).toFixed(1) + '%';
                                    let valueFormatted = 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

                                    return ` ${label}: ${valueFormatted} (${percentage})`;
                                }
                            }
                        }
                    }
                });
            } else {
                ctxTopDespesas.font = "16px Arial";
                ctxTopDespesas.fillStyle = "#6c757d";
                ctxTopDespesas.textAlign = "center";
                ctxTopDespesas.fillText("Nenhuma despesa no período.", ctxTopDespesas.canvas.width / 2, ctxTopDespesas.canvas.height / 2);
            }

        });
    </script>
@stop
