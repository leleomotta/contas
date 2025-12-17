@php
    use Illuminate\Support\Carbon;

    /**
     * ======================================================================
     * FILTRO DE DATA (APENAS 1 VEZ PARA A VIEW TODA)
     * ======================================================================
     * - Evita usar $_GET diretamente (padrão Laravel: request())
     * - Se não vier date_filter, usa o mês atual
     * - Gera start_date e end_date do mês selecionado
     */
    $dateFilter = request('date_filter', now()->format('Y-m'));

    // Monta uma data "segura" dentro do mês (dia 15) para calcular início/fim do mês
    $dt = Carbon::createFromFormat('Y-m-d', $dateFilter . '-15');

    $start_date = $dt->copy()->startOfMonth()->toDateString();
    $end_date   = $dt->copy()->endOfMonth()->toDateString();
@endphp

@extends('adminlte::page')

@section('title', 'Conta - Listar')

@section('content_header')
@stop

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex p-0">
                    <!-- Seletor de mês/ano -->
                    <div class="col-md-auto mx-auto">
                        <div class="input-group date" id="divData" data-target-input="nearest">

                            <div class="input-group-append" onclick="voltaData()">
                                <div class="input-group-text"><i class="fa fa-angle-left"></i></div>
                            </div>

                            <input type="text"
                                   class="form-control datetimepicker-input"
                                   id="Data"
                                   name="Data"
                                   data-target="#divData"
                                   data-toggle="datetimepicker"
                                   placeholder="aaaa-mm"
                                   style="text-align:center;"
                                   {{-- Mantém o valor já no server-side (menos JS e mais confiável) --}}
                                   value="{{ $dateFilter }}"
                            />

                            <div class="input-group-append" onclick="avancaData()">
                                <div class="input-group-text"><i class="fa fa-angle-right"></i></div>
                            </div>

                        </div>
                    </div>
                    <!-- /Seletor de mês/ano -->

                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Ativas</a></li>
                        <li class="nav-item"><a class="nav-link " href="#tab_2" data-toggle="tab">Inativas</a></li>
                        <li class="nav-item"><a class="nav-link " href="#tab_3" data-toggle="tab">Tabela</a></li>
                    </ul>
                </div><!-- /.card-header -->

                <div class="card-body">
                    <div class="tab-content">
                        <!-- ====================================================================== -->
                        <!-- TAB 1 - CONTAS ATIVAS -->
                        <!-- ====================================================================== -->
                        <div class="tab-pane active" id="tab_1">
                            <div class="card-body">
                                @foreach($contasAtivas->chunk(3) as $ativas)
                                    <div class="row">
                                        @foreach($ativas as $conta)
                                            <div class="col-md-4">
                                                <!-- Widget: user widget style 1 -->
                                                <div class="card card-widget widget-user">
                                                    @php
                                                        /**
                                                         * ==================================================================
                                                         * CÁLCULOS POR CONTA (ATIVAS)
                                                         * ==================================================================
                                                         * Usa $start_date e $end_date calculados UMA VEZ no topo da view.
                                                         */

                                                        $receitaMes = (new \App\Models\Receita)->receitas($start_date, $end_date, $conta->ID_Conta);

                                                        $despesaMes = (new \App\Models\Despesa)->despesasSemCartao($start_date, $end_date, $conta->ID_Conta);
                                                        $cartaoPagoMes = (new \App\Models\Despesa)->despesasDeCartao($start_date, $end_date, $conta->ID_Conta);
                                                        $despesaMes = $despesaMes->merge($cartaoPagoMes);

                                                        $tranferencias_EntradaMes = (new \App\Models\Transferencia())->tranferenciasEntrada($start_date, $end_date, $conta->ID_Conta);
                                                        $tranferencias_SaidaMes   = (new \App\Models\Transferencia())->tranferenciasSaida($start_date, $end_date, $conta->ID_Conta);
                                                    @endphp

                                                    <div class="widget-user-header text-white"
                                                         style="background:{{ $conta->Cor }}">
                                                        <h3 class="widget-user-username">{{ $conta->ID_Conta . ' - ' . $conta->Nome }}</h3>
                                                        <h5 class="widget-user-desc">{{ $conta->Banco }}</h5>
                                                    </div>

                                                    <a onclick="window.location='{{ route('contas.edit', ['ID_Conta' => $conta->ID_Conta]) }}'">
                                                        <div class="widget-user-image">
                                                            @if (! $conta->Imagem == null)
                                                                <img class="img-circle elevation-2"
                                                                     src="data:image/jpeg;base64,{{ base64_encode($conta->Imagem) }}"
                                                                     alt="Imagem">
                                                            @else
                                                                <img class="img-circle elevation-2"
                                                                     border="0"
                                                                     align="middle"
                                                                     src="{{ URL::asset('/storage/banco.png') }}"
                                                                     alt="Banco">
                                                            @endif
                                                        </div>
                                                    </a>

                                                    <div class="card-footer">
                                                        <div class="row">
                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Saldo, 2, ',', '.') }}
                                                                    </h5>
                                                                    <span class="description-text">SALDO ATUAL</span>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Receitas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#receitas{{$conta->ID_Conta}}" class="description-text">
                                                                        RECEITAS
                                                                    </span>

                                                                    <!-- Modal de detalhe -->
                                                                    <div class="modal fade" id="receitas{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Receitas</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Receitas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($receitaMes as $valores)
                                                                                            <tr>
                                                                                                <td>{{ $valores->Efetivada }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($valores->Data)) }}</td>
                                                                                                <td>{{ $valores->Descricao }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($valores->Valor, 2, ',', '.') }}</td>
                                                                                                <td>{{ $valores->NomeCategoria }}</td>
                                                                                                <td>{{ $valores->Banco }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /Modal -->
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Despesas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#despesas{{$conta->ID_Conta}}" class="description-text">
                                                                        DESPESAS
                                                                    </span>

                                                                    <!-- Modal de detalhe -->
                                                                    <div class="modal fade" id="despesas{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Despesas</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Despesas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($despesaMes as $valores)
                                                                                            <tr>
                                                                                                <td>{{ $valores->Efetivada }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($valores->Data)) }}</td>
                                                                                                <td>{{ $valores->Descricao }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($valores->Valor, 2, ',', '.') }}</td>
                                                                                                <td>{{ $valores->NomeCategoria }}</td>
                                                                                                <td>{{ $valores->Banco }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /Modal -->
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Entradas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#entra{{$conta->ID_Conta}}" class="description-text">
                                                                        Transf. Entrada
                                                                    </span>

                                                                    <!-- Modal -->
                                                                    <div class="modal fade" id="entra{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Transferências de Entrada</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Entradas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($tranferencias_EntradaMes as $transferencia)
                                                                                            <tr>
                                                                                                <td>{{ $transferencia->Nome .' - ' . $transferencia->Banco  }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /Modal -->
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Saidas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#sai{{$conta->ID_Conta}}" class="description-text">
                                                                        Transf. Saída
                                                                    </span>

                                                                    <!-- Modal -->
                                                                    <div class="modal fade" id="sai{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Transferências de Saída</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Saidas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($tranferencias_SaidaMes as $transferencia)
                                                                                            <tr>
                                                                                                <td>{{ $transferencia->Nome .' - ' . $transferencia->Banco  }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /Modal -->
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->SaldoMes, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#saldo{{$conta->ID_Conta}}" class="description-text">
                                                                        SALDO MÊS
                                                                    </span>

                                                                    @php
                                                                        /**
                                                                         * Monta um "extrato do mês" combinando receitas, despesas, transf. entrada e saída,
                                                                         * e ordena por Data.
                                                                         */
                                                                        $despesas = $despesaMes;
                                                                        $despesas->each(function ($despesa) {
                                                                            $despesa->Valor = -$despesa->Valor;
                                                                        });

                                                                        $saldo = $despesas->merge($receitaMes);

                                                                        $entradas = $tranferencias_EntradaMes->map(function ($item) {
                                                                            $item->Efetivada = 'X';
                                                                            $item->Categoria = 'Transf. Ent.';
                                                                            $item->Descricao = 'Transf. Ent.';
                                                                            $item->NomeCategoria = '-';
                                                                            return $item;
                                                                        });

                                                                        $saldo = $saldo->merge($entradas);

                                                                        $saidas = $tranferencias_SaidaMes->map(function ($item) {
                                                                            $item->Efetivada = 'X';
                                                                            $item->Categoria = 'Transf. Saida';
                                                                            $item->Descricao = 'Transf. Saida';
                                                                            $item->NomeCategoria = '-';
                                                                            return $item;
                                                                        });

                                                                        $saidas->each(function ($saida) {
                                                                            $saida->Valor = -$saida->Valor;
                                                                        });

                                                                        $saldo = $saldo->merge($saidas);

                                                                        $sorted = $saldo->sortBy(function($item) {
                                                                            return $item->Data;
                                                                        });
                                                                    @endphp

                                                                        <!-- Modal -->
                                                                    <div class="modal fade" id="saldo{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Saldo do mês</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Receitas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($sorted as $valores)
                                                                                            <tr>
                                                                                                <td>{{ $valores->Efetivada }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($valores->Data)) }}</td>
                                                                                                <td>{{ $valores->Descricao }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($valores->Valor, 2, ',', '.') }}</td>
                                                                                                <td>{{ $valores->NomeCategoria }}</td>
                                                                                                <td>{{ $valores->Banco }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /Modal -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.widget-user -->
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- /TAB 1 -->

                        <!-- ====================================================================== -->
                        <!-- TAB 2 - CONTAS INATIVAS / ARQUIVADAS -->
                        <!-- ====================================================================== -->
                        <div class="tab-pane" id="tab_2">
                            <div class="card-body">
                                @foreach($contasArquivadas->chunk(3) as $inativas)
                                    <div class="row">
                                        @foreach($inativas as $conta)
                                            <div class="col-md-4">
                                                <div class="card card-widget widget-user">
                                                    @php
                                                        /**
                                                         * ==================================================================
                                                         * CÁLCULOS POR CONTA (ARQUIVADAS)
                                                         * ==================================================================
                                                         * Usa o mesmo período calculado no topo da view.
                                                         */
                                                        $despesaMes = (new \App\Models\Despesa)->despesasSemCartao($start_date, $end_date, $conta->ID_Conta);
                                                        $cartaoPagoMes = (new \App\Models\Despesa)->despesasDeCartao($start_date, $end_date, $conta->ID_Conta);
                                                        $despesaMes = $despesaMes->merge($cartaoPagoMes);

                                                        $receitaMes = (new \App\Models\Receita)->receitas($start_date, $end_date, $conta->ID_Conta);

                                                        $tranferencias_EntradaMes = (new \App\Models\Transferencia())->tranferenciasEntrada($start_date, $end_date, $conta->ID_Conta);
                                                        $tranferencias_SaidaMes   = (new \App\Models\Transferencia())->tranferenciasSaida($start_date, $end_date, $conta->ID_Conta);
                                                    @endphp

                                                    <div class="widget-user-header text-white"
                                                         style="background:{{ $conta->Cor }}">
                                                        <h3 class="widget-user-username">{{ $conta->ID_Conta . ' - ' . $conta->Nome }}</h3>
                                                        <h5 class="widget-user-desc">{{ $conta->Banco }}</h5>
                                                    </div>

                                                    <a onclick="window.location='{{ route('contas.edit', ['ID_Conta' => $conta->ID_Conta]) }}'">
                                                        <div class="widget-user-image">
                                                            @if (! $conta->Imagem == null)
                                                                <img class="img-circle elevation-2"
                                                                     src="data:image/jpeg;base64,{{ base64_encode($conta->Imagem) }}"
                                                                     alt="Imagem">
                                                            @else
                                                                <img class="img-circle elevation-2"
                                                                     border="0"
                                                                     align="middle"
                                                                     src="{{ URL::asset('/storage/banco.png') }}"
                                                                     alt="Banco">
                                                            @endif
                                                        </div>
                                                    </a>

                                                    {{-- A partir daqui, mantém o seu layout original (mesmo conteúdo da TAB 1) --}}
                                                    <div class="card-footer">
                                                        <div class="row">
                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Saldo, 2, ',', '.') }}
                                                                    </h5>
                                                                    <span class="description-text">SALDO ATUAL</span>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Receitas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#receitas{{$conta->ID_Conta}}" class="description-text">
                                                                        RECEITAS
                                                                    </span>

                                                                    <div class="modal fade" id="receitas{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Receitas</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Receitas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($receitaMes as $valores)
                                                                                            <tr>
                                                                                                <td>{{ $valores->Efetivada }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($valores->Data)) }}</td>
                                                                                                <td>{{ $valores->Descricao }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($valores->Valor, 2, ',', '.') }}</td>
                                                                                                <td>{{ $valores->NomeCategoria }}</td>
                                                                                                <td>{{ $valores->Banco }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Despesas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#despesas{{$conta->ID_Conta}}" class="description-text">
                                                                        DESPESAS
                                                                    </span>

                                                                    <div class="modal fade" id="despesas{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Despesas</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Despesas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($despesaMes as $valores)
                                                                                            <tr>
                                                                                                <td>{{ $valores->Efetivada }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($valores->Data)) }}</td>
                                                                                                <td>{{ $valores->Descricao }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($valores->Valor, 2, ',', '.') }}</td>
                                                                                                <td>{{ $valores->NomeCategoria }}</td>
                                                                                                <td>{{ $valores->Banco }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Entradas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#entra{{$conta->ID_Conta}}" class="description-text">
                                                                        Transf. Entrada
                                                                    </span>

                                                                    <div class="modal fade" id="entra{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Transferências de Entrada</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Entradas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($tranferencias_EntradaMes as $transferencia)
                                                                                            <tr>
                                                                                                <td>{{ $transferencia->Nome .' - ' . $transferencia->Banco  }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->Saidas, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#sai{{$conta->ID_Conta}}" class="description-text">
                                                                        Transf. Saída
                                                                    </span>

                                                                    <div class="modal fade" id="sai{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Transferências de Saída</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Saidas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($tranferencias_SaidaMes as $transferencia)
                                                                                            <tr>
                                                                                                <td>{{ $transferencia->Nome .' - ' . $transferencia->Banco  }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Conta Origem</th>
                                                                                            <th>Data</th>
                                                                                            <th>Valor</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-4">
                                                                <div class="description-block">
                                                                    <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                        {{ 'R$ ' . number_format($conta->SaldoMes, 2, ',', '.') }}
                                                                    </h5>

                                                                    <span data-toggle="modal" data-target="#saldo{{$conta->ID_Conta}}" class="description-text">
                                                                        SALDO MÊS
                                                                    </span>

                                                                    @php
                                                                        $despesas = $despesaMes;
                                                                        $despesas->each(function ($despesa) {
                                                                            $despesa->Valor = -$despesa->Valor;
                                                                        });

                                                                        $saldo = $despesas->merge($receitaMes);

                                                                        $entradas = $tranferencias_EntradaMes->map(function ($item) {
                                                                            $item->Efetivada = 'X';
                                                                            $item->Categoria = 'Transf. Ent.';
                                                                            $item->Descricao = 'Transf. Ent.';
                                                                            $item->NomeCategoria = '-';
                                                                            return $item;
                                                                        });

                                                                        $saldo = $saldo->merge($entradas);

                                                                        $saidas = $tranferencias_SaidaMes->map(function ($item) {
                                                                            $item->Efetivada = 'X';
                                                                            $item->Categoria = 'Transf. Saida';
                                                                            $item->Descricao = 'Transf. Saida';
                                                                            $item->NomeCategoria = '-';
                                                                            return $item;
                                                                        });

                                                                        $saidas->each(function ($saida) {
                                                                            $saida->Valor = -$saida->Valor;
                                                                        });

                                                                        $saldo = $saldo->merge($saidas);

                                                                        $sorted = $saldo->sortBy(function($item) {
                                                                            return $item->Data;
                                                                        });
                                                                    @endphp

                                                                    <div class="modal fade" id="saldo{{$conta->ID_Conta}}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title">Saldo do mês</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <table id="Receitas" class="table table-bordered table-hover">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        @foreach($sorted as $valores)
                                                                                            <tr>
                                                                                                <td>{{ $valores->Efetivada }}</td>
                                                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($valores->Data)) }}</td>
                                                                                                <td>{{ $valores->Descricao }}</td>
                                                                                                <td>{{ 'R$ ' . number_format($valores->Valor, 2, ',', '.') }}</td>
                                                                                                <td>{{ $valores->NomeCategoria }}</td>
                                                                                                <td>{{ $valores->Banco }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                        </tbody>
                                                                                        <tfoot>
                                                                                        <tr>
                                                                                            <th>Efetivada</th>
                                                                                            <th>Data</th>
                                                                                            <th>Descrição</th>
                                                                                            <th>Valor</th>
                                                                                            <th>Categoria</th>
                                                                                            <th>Banco</th>
                                                                                        </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- /.widget-user -->
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <!-- /TAB 2 -->

                        <!-- ====================================================================== -->
                        <!-- TAB 3 - TABELA (COM SWITCH MOSTRAR/OCULTAR ARQUIVADAS) -->
                        <!-- ====================================================================== -->
                        <div class="tab-pane " id="tab_3">
                            <div class="card-body">

                                <div class="mb-2 d-flex justify-content-end align-items-center">
                                    {{-- Switch para mostrar/ocultar contas arquivadas na tabela --}}
                                    <div class="custom-control custom-switch mr-3">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="toggleArquivadas">
                                        <label class="custom-control-label" for="toggleArquivadas">
                                            Mostrar contas inativas/arquivadas
                                        </label>
                                    </div>

                                    <button type="button"
                                            class="btn btn-success btn-sm"
                                            onclick="copiarTabelaExcel()">
                                        <i class="fas fa-file-excel"></i> Copiar para Excel
                                    </button>
                                </div>

                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center">Conta</th>
                                        <th style="text-align: center">Ano/Mês</th>
                                        <th style="text-align: center">Saldo Atual</th>
                                        <th style="text-align: center">Receitas</th>
                                        <th style="text-align: center">Despesas</th>
                                        <th style="text-align: center">Transf. Entrada</th>
                                        <th style="text-align: center">Transf. Saida</th>
                                        <th style="text-align: center">Saldo Mes</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @php
                                        /**
                                         * ==================================================================
                                         * IMPORTANTE:
                                         * - NÃO alteramos $contasAtivas aqui (evita efeitos colaterais).
                                         * - Criamos uma lista "contasTabela" com ativas + arquivadas.
                                         * - Criamos um "mapa" de IDs arquivadas para identificar linhas.
                                         */
                                        $idsArquivadas = $contasArquivadas->pluck('ID_Conta')->flip();

                                        $contasTabela = $contasAtivas
                                            ->merge($contasArquivadas)
                                            ->sortBy('ID_Conta');
                                    @endphp

                                    @foreach($contasTabela as $conta)
                                        @php
                                            // true se esta conta faz parte de contasArquivadas
                                            $isArquivada = isset($idsArquivadas[$conta->ID_Conta]);

                                            // seu destaque existente
                                            $isConta99 = ((int)$conta->ID_Conta === 99);
                                        @endphp

                                        <tr
                                            class="{{ $isArquivada ? 'conta-arquivada table-secondary' : '' }}"
                                            data-arquivada="{{ $isArquivada ? 1 : 0 }}"
                                            style="{{ $isConta99 ? 'background-color: #fff3cd;' : '' }}"
                                        >
                                            <td>
                                                {{ $conta->ID_Conta . ' - ' . $conta->Nome . ' - ' . $conta->Banco }}

                                                {{-- Badge opcional pra ficar claro visualmente --}}
                                                @if($isArquivada)
                                                    <span class="badge badge-secondary ml-2">Arquivada</span>
                                                @endif
                                            </td>
                                            <td>{{ $conta->Ano_Mes }}</td>
                                            <td>{{ 'R$ ' . number_format($conta->Saldo, 2, ',', '.') }}</td>
                                            <td>{{ 'R$ ' . number_format($conta->Receitas, 2, ',', '.') }}</td>
                                            <td>{{ 'R$ ' . number_format($conta->Despesas, 2, ',', '.') }}</td>
                                            <td>{{ 'R$ ' . number_format($conta->Entradas, 2, ',', '.') }}</td>
                                            <td>{{ 'R$ ' . number_format($conta->Saidas, 2, ',', '.') }}</td>
                                            <td>{{ 'R$ ' . number_format($conta->SaldoMes, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th style="text-align: center">Conta</th>
                                        <th style="text-align: center">Ano/Mês</th>
                                        <th style="text-align: center">Saldo Atual</th>
                                        <th style="text-align: center">Receitas</th>
                                        <th style="text-align: center">Despesas</th>
                                        <th style="text-align: center">Transf. Entrada</th>
                                        <th style="text-align: center">Transf. Saida</th>
                                        <th style="text-align: center">Saldo Mes</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /TAB 3 -->

                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- OBS: você está carregando Bootstrap 5 aqui; AdminLTE 3 usa Bootstrap 4. --}}
    {{-- Se aparecer alguma "quebra" de layout, esse é o primeiro ponto pra revisar. --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css"
          integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

    {{-- Data --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

    {{-- daterange picker --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">
@stop

@section('js')
    <script>
        /**
         * ======================================================================
         * NAVEGAÇÃO DE MÊS (VOLTA/AVANÇA)
         * ======================================================================
         * Monta a URL com date_filter e recarrega.
         */
        function voltaData() {
            const [anoStr, mesStr] = document.getElementById('Data').value.split('-');
            let ano = parseInt(anoStr);
            let mes = parseInt(mesStr);

            mes = mes - 1;
            if (mes === 0) {
                mes = 12;
                ano = ano - 1;
            }

            if (mes >= 1 && mes <= 9) {
                mes = "0" + mes;
            }

            const data = ano + '-' + mes;
            document.getElementById('Data').value = data;

            let url = '{{ route("contas.showAll", ["date_filter" => "DATA"]) }}';
            url = url.replace('DATA', data);

            window.location.href = url;
        }

        function avancaData() {
            const [anoStr, mesStr] = document.getElementById('Data').value.split('-');
            let ano = parseInt(anoStr);
            let mes = parseInt(mesStr);

            mes = mes + 1;
            if (mes === 13) {
                mes = 1;
                ano = ano + 1;
            }

            if (mes >= 1 && mes <= 9) {
                mes = "0" + mes;
            }

            const data = ano + '-' + mes;
            document.getElementById('Data').value = data;

            let url = '{{ route("contas.showAll", ["date_filter" => "DATA"]) }}';
            url = url.replace('DATA', data);

            window.location.href = url;
        }

        function redirecionaParaDataSelecionada() {
            const data = document.getElementById('Data').value;
            let url = '{{ route("contas.showAll", ["date_filter" => "DATA"]) }}';
            url = url.replace('DATA', data);
            window.location.href = url;
        }
    </script>

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>

    <!-- INPUT DATE -->
    <script>
        /**
         * ======================================================================
         * DATEPICKER (TEMPUSDOMINUS)
         * ======================================================================
         * - Inicia com o valor já definido no input (server-side)
         * - Ao fechar o picker, se mudou, redireciona
         */
        let ultimaData = $('#Data').val();

        $('#divData').datetimepicker({
            format: 'YYYY-MM',
            viewMode: 'months',
            minViewMode: 'months',
            locale: 'pt-br',

            // Garante que o picker abra exatamente no mês do input
            defaultDate: moment($('#Data').val() + '-01', 'YYYY-MM-DD'),
        });

        $('#divData').on('hide.datetimepicker', function () {
            const novaData = $('#Data').val();

            if (novaData !== ultimaData) {
                ultimaData = novaData;
                redirecionaParaDataSelecionada();
            }
        });

        $('[data-mask]').inputmask();
    </script>

    <script>
        /**
         * ======================================================================
         * COPIAR TABELA PARA EXCEL
         * ======================================================================
         * - Copia a tabela "example1" como HTML/seleção
         * - Linhas ocultas (display:none) não serão copiadas
         */
        function copiarTabelaExcel() {
            const tabela = document.getElementById('example1');

            if (!tabela) {
                alert('Tabela não encontrada.');
                return;
            }

            const range = document.createRange();
            range.selectNode(tabela);

            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);

            try {
                document.execCommand('copy');
            } catch (err) {
                alert('Erro ao copiar a tabela.');
            }

            selection.removeAllRanges();
        }
    </script>

    <script>
        /**
         * ======================================================================
         * MOSTRAR/OCULTAR ARQUIVADAS NA ABA TABELA
         * ======================================================================
         * - Linhas arquivadas possuem data-arquivada="1"
         * - Switch controla display
         * - Preferência salva no localStorage
         */
        function aplicarFiltroArquivadasTabela(mostrar) {
            const linhasArquivadas = document.querySelectorAll('#example1 tbody tr[data-arquivada="1"]');

            linhasArquivadas.forEach((tr) => {
                tr.style.display = mostrar ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('toggleArquivadas');
            if (!toggle) return;

            // Recupera preferência (default: ocultar)
            const salvo = localStorage.getItem('mostrarArquivadasTabela');
            const mostrarInicial = (salvo === '1');

            toggle.checked = mostrarInicial;
            aplicarFiltroArquivadasTabela(mostrarInicial);

            toggle.addEventListener('change', function () {
                const mostrarAgora = toggle.checked;
                localStorage.setItem('mostrarArquivadasTabela', mostrarAgora ? '1' : '0');
                aplicarFiltroArquivadasTabela(mostrarAgora);
            });
        });
    </script>
@stop
