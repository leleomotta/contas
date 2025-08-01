@extends('adminlte::page')

@section('title', 'Recorrência - Listar')

@section('content_header')
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table id="tabelaRecorrencias" class="table text-nowrap table-hover table-bordered border-light">
                        <thead>
                        <tr>
                            <th style="width:50px">Ativa</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Conta</th>
                            <th>Cartão</th>
                            <th>Categoria</th>
                            <th>Periodicidade</th>
                            <th>Dia vencimento</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th style="width:110px">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recorrencias as $rec)
                            <tr>
                                <td>
                                    @if ($rec->Ativa)
                                        <img src="{{ URL::asset('/storage/V.bmp') }}" alt="Ativa">
                                    @else
                                        <img src="{{ URL::asset('/storage/X.bmp') }}" alt="Inativa">
                                    @endif
                                </td>
                                <td>{{ $rec->Descricao }}</td>
                                <td>{{ 'R$ ' . number_format($rec->Valor, 2, ',', '.') }}</td>
                                <td>{{ $rec->conta->Banco ?? '-' }}</td>
                                <td>{{ $rec->cartao->Nome ?? '-' }}</td>
                                <td>
                                    @if ($rec->categoria)
                                        <span class="icone-circulo" style="background-color: {{ $rec->categoria->Cor }};">
                                            <i class="{{ $rec->categoria->Icone }}"></i>
                                        </span>
                                        {{ $rec->categoria->Nome }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $rec->Periodicidade }}</td>
                                <td>{{ $rec->Dia_vencimento }}</td>
                                <td>{{ \Carbon\Carbon::parse($rec->Data_inicio)->format('d/m/Y') }}</td>
                                <td>
                                    @if ($rec->Data_fim)
                                        {{ \Carbon\Carbon::parse($rec->Data_fim)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                        <form action="{{ route('recorrencias.edit', ['ID_Recorrencia' => $rec->ID_Recorrencia]) }}" method="GET" class="m-0 p-0">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('recorrencias.destroy', ['ID_Recorrencia' => $rec->ID_Recorrencia]) }}" method="POST" class="m-0 p-0">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Excluir"
                                                    onclick="return confirm('Deseja realmente excluir esta recorrência?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Ativa</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Conta</th>
                            <th>Cartão</th>
                            <th>Categoria</th>
                            <th>Periodicidade</th>
                            <th>Dia vencimento</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>&nbsp;</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">
    <style>
        .icone-circulo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            color: black;
            font-size: 16px;
        }
        .icone-circulo i {
            margin: 0;
        }
    </style>
@stop
