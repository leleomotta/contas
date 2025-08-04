@extends('adminlte::page')

@section('title', 'Transferência - Listar')

@section('content_header')
    <h1>Transferências</h1>
@stop

@section('content')

    <!-- Bloco principal de tabs: Por Origem / Por Destino -->
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="transferencia-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-origem-tab" data-toggle="pill" href="#tab-origem" role="tab" aria-controls="tab-origem" aria-selected="true">
                        Por Conta de Origem
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-destino-tab" data-toggle="pill" href="#tab-destino" role="tab" aria-controls="tab-destino" aria-selected="false">
                        Por Conta de Destino
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="transferencia-tabs-content">

                <!-- Conteúdo da aba "Por Origem" -->
                <div class="tab-pane fade show active" id="tab-origem" role="tabpanel" aria-labelledby="tab-origem-tab">
                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="contas-origem-tabs" role="tablist">
                                @foreach($contasOrigem as $index => $contaOrigem)
                                    <li class="nav-item">
                                        <a class="nav-link @if($index === 0) active @endif" id="origem-tab-{{$contaOrigem->ID_Conta}}" data-toggle="pill"
                                           href="#origem-{{$contaOrigem->ID_Conta}}" role="tab" aria-controls="origem-{{$contaOrigem->ID_Conta}}"
                                           aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $contaOrigem->Nome . ' - ' . $contaOrigem->Banco }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="contas-origem-tabs-content">
                                @foreach($contasOrigem as $index => $contaOrigem)
                                    <div class="tab-pane fade @if($index === 0) show active @endif" id="origem-{{$contaOrigem->ID_Conta}}" role="tabpanel"
                                         aria-labelledby="origem-tab-{{$contaOrigem->ID_Conta}}">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th>Conta Destino</th>
                                                <th>Data</th>
                                                <th>Valor</th>
                                                <th style="width: 110px">Ações</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($transferencias->where('ID_Conta_Origem', $contaOrigem->ID_Conta) as $transferencia)
                                                <tr>
                                                    <td>{{ $transferencia->contaDestino->Nome . ' - ' . $transferencia->contaDestino->Banco }}</td>
                                                    <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
                                                    <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                                            <form action="{{ route('transferencias.edit', ['ID_Transferencia' => $transferencia->ID_Transferencia]) }}" method="GET">
                                                                <button type="submit" class="btn btn-sm btn-primary" title="Editar">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('transferencias.destroy', ['ID_Transferencia' => $transferencia->ID_Transferencia]) }}" method="POST">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                                                        onclick="return confirm('Deseja realmente excluir este registro?')">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo da aba "Por Destino" -->
                <div class="tab-pane fade" id="tab-destino" role="tabpanel" aria-labelledby="tab-destino-tab">
                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="contas-destino-tabs" role="tablist">
                                @foreach($contasDestino as $index => $contaDestino)
                                    <li class="nav-item">
                                        <a class="nav-link @if($index === 0) active @endif" id="destino-tab-{{$contaDestino->ID_Conta}}" data-toggle="pill"
                                           href="#destino-{{$contaDestino->ID_Conta}}" role="tab" aria-controls="destino-{{$contaDestino->ID_Conta}}"
                                           aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $contaDestino->Nome . ' - ' . $contaDestino->Banco }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="contas-destino-tabs-content">
                                @foreach($contasDestino as $index => $contaDestino)
                                    <div class="tab-pane fade @if($index === 0) show active @endif" id="destino-{{$contaDestino->ID_Conta}}" role="tabpanel"
                                         aria-labelledby="destino-tab-{{$contaDestino->ID_Conta}}">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th>Conta Origem</th>
                                                <th>Data</th>
                                                <th>Valor</th>
                                                <th style="width: 110px">Ações</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($transferencias->where('ID_Conta_Destino', $contaDestino->ID_Conta) as $transferencia)
                                                <tr>
                                                    <td>{{ $transferencia->contaOrigem->Nome . ' - ' . $transferencia->contaOrigem->Banco }}</td>
                                                    <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>
                                                    <td>{{ 'R$ ' . number_format($transferencia->Valor, 2, ',', '.') }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                                            <form action="{{ route('transferencias.edit', ['ID_Transferencia' => $transferencia->ID_Transferencia]) }}" method="GET">
                                                                <button type="submit" class="btn btn-sm btn-primary" title="Editar">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('transferencias.destroy', ['ID_Transferencia' => $transferencia->ID_Transferencia]) }}" method="POST">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                                                        onclick="return confirm('Deseja realmente excluir este registro?')">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card-footer">
        <form id="novaTransferencia" role="form" action="{{ route('transferencias.new') }}" method="GET">
            <div class="float-right">
                <button type="submit" class="btn btn-success">Nova transferência</button>
            </div>
        </form>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <!-- SweetAlert2 -->
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
@stop

@section('js')
    <script>
        // Ativa a primeira sub-aba de cada conjunto ao carregar a página
        $(document).ready(function() {
            // Ativa a primeira aba 'Por Origem' ou 'Por Destino' se houver dados
            $('#transferencia-tabs a[data-toggle="pill"]').first().tab('show');

            // Ativa a primeira sub-aba de cada conjunto
            $('#contas-origem-tabs a[data-toggle="pill"]').first().tab('show');
            $('#contas-destino-tabs a[data-toggle="pill"]').first().tab('show');
        });
    </script>
@stop
