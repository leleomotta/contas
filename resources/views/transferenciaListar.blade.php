@extends('adminlte::page')

@section('title', 'Transferência - Listar')

@section('content_header')

@stop

@section('content')


    <!--Filtro -->
    <form id="filtro" role="form" action="#" method="GET">

    </form>
    <!--/.Filtro -->

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="card-header d-flex p-0">

                        <h3 class="card-title p-3">Transferências</h3>

                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Por origem</a></li>
                            <li class="nav-item"><a class="nav-link " href="#tab_2" data-toggle="tab">Por destino</a></li>

                        </ul>
                    </div><!-- /.card-header -->

                        <div class="tab-pane active" id="tab_1">
                            <div class="card-body">
                            <div class="card card-primary card-tabs">
                                <div class="card-header p-0 pt-1">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">

                                        @foreach($contasOrigem as $contaOrigem)
                                            <li class="nav-item">
                                                <a class="nav-link" id="custom-tab-ID_Conta-{{$contaOrigem->ID_Conta_Origem}}-tab" data-toggle="pill" href="#custom-tab-ID_Conta-{{$contaOrigem->ID_Conta_Origem}}"
                                                   role="tab" aria-controls="custom-tab-ID_Conta-{{$contaOrigem->ID_Conta_Origem}}" aria-selected="false">{{ $contaOrigem->Nome . ' - ' . $contaOrigem->Banco  }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-one-tabContent">
                                        @foreach($contasOrigem as $contaOrigem)
                                            <div class="tab-pane fade" id="custom-tab-ID_Conta-{{$contaOrigem->ID_Conta_Origem}}" role="tabpanel" aria-labelledby="custom-tab-ID_Conta-{{$contaOrigem->ID_Conta_Origem}}-tab">
                                                <table id="example1" class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>Conta Destino</th>
                                                        <th>Data</th>
                                                        <th>Valor</th>
                                                        <th style="width: 110px">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($transferencias->where('ID_Conta_Origem','=',$contaOrigem->ID_Conta_Origem) as $transferencia)
                                                            <tr>
                                                                <td>{{ $transferencia->Nome .' - ' . $transferencia->Banco  }}</td>
                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>

                                                                <td>{{ 'R$ ' .  str_replace("_",'.',
                                                                    str_replace(".",',',
                                                                    str_replace(",",'_',
                                                                    number_format($transferencia->Valor, 2
                                                                    )))) }}
                                                                </td>
                                                                <td>
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <form id="edita" role="form" action="{{ route('transferencias.edit', ['ID_Transferencia' =>$transferencia->ID_Transferencia])  }}" method="GET">
                                                                                <button type="submit" class="btn btn-primary"
                                                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                                                    <span class="fa fa-edit"></span>
                                                                                </button>
                                                                            </form>
                                                                        </div>

                                                                        <div class="col-3">
                                                                            <form action="{{ route('transferencias.destroy', ['ID_Transferencia'=> $transferencia->ID_Transferencia])  }}" method="POST">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <input type="hidden" name="ID_Transferencia" value="{{ $transferencia->ID_Transferencia }}">
                                                                                <button type="submit" class="btn btn-danger"
                                                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
                                                                                        onclick="return confirm('Deseja realmente excluir este registro?')">
                                                                                    <span class="fa fa-trash"></span>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </div>


                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <th>Conta Destino</th>
                                                        <th>Data</th>
                                                        <th>Valor</th>
                                                        <th style="width: 110px">Ações</th>
                                                    </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                        </div>

                        <div class="tab-pane" id="tab_2">
                            <div class="card-body">
                                <div class="card card-primary card-tabs">
                                    <div class="card-header p-0 pt-1">
                                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">

                                            @foreach($contasDestino as $contaDestino)
                                                <li class="nav-item">
                                                    <a class="nav-link" id="custom-tab-ID_Conta-{{$contaDestino->ID_Conta_Destino}}-tab" data-toggle="pill" href="#custom-tab-ID_Conta-{{$contaDestino->ID_Conta_Destino}}"
                                                       role="tab" aria-controls="custom-tab-ID_Conta-{{$contaDestino->ID_Conta_Destino}}" aria-selected="false">{{ $contaDestino->Nome . ' - ' . $contaDestino->Banco  }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-one-tabContent">
                                            @foreach($contasDestino as $contaDestino)
                                                <div class="tab-pane fade" id="custom-tab-ID_Conta-{{$contaDestino->ID_Conta_Destino}}" role="tabpanel" aria-labelledby="custom-tab-ID_Conta-{{$contaDestino->ID_Conta_Destino}}-tab">
                                                    <table id="example1" class="table table-bordered table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>Conta Origem</th>
                                                            <th>Data</th>
                                                            <th>Valor</th>
                                                            <th style="width: 110px">Ações</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($transferencias->where('ID_Conta_Destino','=',$contaDestino->ID_Conta_Destino) as $transferencia)
                                                            <tr>
                                                                <td>{{ $transferencia->Nome .' - ' . $transferencia->Banco  }}</td>
                                                                <td style="text-align: center">{{ date('d/m/Y', strtotime($transferencia->Data)) }}</td>

                                                                <td>{{ 'R$ ' .  str_replace("_",'.',
                                                                    str_replace(".",',',
                                                                    str_replace(",",'_',
                                                                    number_format($transferencia->Valor, 2
                                                                    )))) }}
                                                                </td>
                                                                <td>
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <form id="edita" role="form" action="{{ route('transferencias.edit', ['ID_Transferencia' =>$transferencia->ID_Transferencia])  }}" method="GET">
                                                                                <button type="submit" class="btn btn-primary"
                                                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                                                    <span class="fa fa-edit"></span>
                                                                                </button>
                                                                            </form>
                                                                        </div>

                                                                        <div class="col-3">
                                                                            <form action="{{ route('transferencias.destroy', ['ID_Transferencia'=> $transferencia->ID_Transferencia])  }}" method="POST">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <input type="hidden" name="ID_Transferencia" value="{{ $transferencia->ID_Transferencia }}">
                                                                                <button type="submit" class="btn btn-danger"
                                                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
                                                                                        onclick="return confirm('Deseja realmente excluir este registro?')">
                                                                                    <span class="fa fa-trash"></span>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </div>


                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <th>Conta Origem</th>
                                                            <th>Data</th>
                                                            <th>Valor</th>
                                                            <th style="width: 110px">Ações</th>
                                                        </tr>
                                                        </tfoot>
                                                    </table>

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- /.card -->
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="card-footer">
                        <form id="novaTransferencia" role="form" action="{{ route('transferencias.new') }}" method="GET">

                            <div class="float-right">
                                <button type="submit" class="btn btn-success">Nova transferencia</button>
                            </div>
                        </form>

                    </div>
                </div><!-- /.card-body -->
            </div>

        </div>
    </div>
    @stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
@stop

@section('js')


@stop
