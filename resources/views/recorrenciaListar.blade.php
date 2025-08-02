@extends('adminlte::page')

@section('title', 'Recorrência - Listar')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Recorrências</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <!-- Botão para abrir o modal de geração de recorrências -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalGerarRecorrencias">
                    <i class="fas fa-plus"></i> Gerar Recorrências
                </button>
            </ol>
        </div>
    </div>
@stop

@section('content')

    <!-- Bloco para exibir as mensagens de sucesso/erro -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- O restante do seu conteúdo da tabela -->
    <div class="row">
        ...
    </div>

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

    <!-- Modal para Gerar Recorrências -->
    <div class="modal fade" id="modalGerarRecorrencias" tabindex="-1" role="dialog" aria-labelledby="modalGerarRecorrenciasLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGerarRecorrenciasLabel">Gerar Recorrências Mensais</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Selecione o mês e o ano para gerar as despesas recorrentes.</p>
                    <form id="formGerarRecorrencias">
                        <div class="form-group">
                            <label for="mes">Mês</label>
                            <select class="form-control" id="mes" name="mes">
                                <option value="01">Janeiro</option>
                                <option value="02">Fevereiro</option>
                                <option value="03">Março</option>
                                <option value="04">Abril</option>
                                <option value="05">Maio</option>
                                <option value="06">Junho</option>
                                <option value="07">Julho</option>
                                <option value="08">Agosto</option>
                                <option value="09">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ano">Ano</label>
                            <select class="form-control" id="ano" name="ano">
                                @php
                                    $anoAtual = now()->year;
                                @endphp
                                @for ($i = $anoAtual - 2; $i <= $anoAtual + 5; $i++)
                                    <option value="{{ $i }}" {{ $i == $anoAtual ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnGerarRecorrencias">Gerar</button>
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

@section('js')
    <script>
        // Lógica para o modal de geração de recorrências
        $('#btnGerarRecorrencias').on('click', function() {
            let mes = $('#mes').val();
            let ano = $('#ano').val();

            // Monta a URL com os valores selecionados
            let url = "{{ route('recorrencias.gerar', ['mes' => 'MES_PLACEHOLDER', 'ano' => 'ANO_PLACEHOLDER']) }}";
            url = url.replace('MES_PLACEHOLDER', mes).replace('ANO_PLACEHOLDER', ano);

            // Redireciona para a rota
            window.location.href = url;
        });

        // Opcional: preenche o modal com o mês e ano atuais ao abrir
        $('#modalGerarRecorrencias').on('show.bs.modal', function() {
            let dataAtual = new Date();
            let mesAtual = dataAtual.getMonth() + 1;
            let anoAtual = dataAtual.getFullYear();
            $('#mes').val(('0' + mesAtual).slice(-2));
            $('#ano').val(anoAtual);
        });
    </script>
@stop
