@extends('adminlte::page')

@section('title', 'Fatura - Listar')

@section('content_header')

@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card">
                </div>
                <div class="col-md-auto mx-auto">
                    <div class="input-group date" id="divData" data-target-input="nearest">

                        <div class="input-group-append" onclick="voltaData()">
                            <div class="input-group-text"><i class="fa fa-angle-left"></i></div>
                        </div>

                        <input type="text" class="form-control datetimepicker-input"
                               id="Data" name="Data"
                               data-target="#divData"
                               data-toggle="datetimepicker"
                               placeholder="aaaa-mm"
                               style="text-align:center;" />

                        <div class="input-group-append" onclick="avancaData()">
                            <div class="input-group-text"><i class="fa fa-angle-right"></i></div>
                        </div>

                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table id="example1" class="table text-nowrap table-hover table-bordered border-light">
                        <thead>
                        <tr>
                            <th style="text-align: center">Data</th>
                            <th style="text-align: center">Descrição</th>
                            <th style="text-align: center">Valor</th>
                            <th style="text-align: center">Categoria</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($faturas as $fatura)
                            <tr>
                                <td style="text-align: center">{{ date('d/m/Y', strtotime($fatura->Data)) }}</td>
                                <td>{{ $fatura->Descricao  }}</td>
                                <td>{{ 'R$ ' .  str_replace("_",'.',
                                                str_replace(".",',',
                                                str_replace(",",'_',
                                                number_format($fatura->Valor, 2
                                                )))) }}</td>
                                <td>
                                    <span class="icone-circulo" style="background-color: {{ $fatura->Cor }};">
                                        <i class="{{ $fatura->Icone }}"></i>
                                    </span>
                                    {{ $fatura->NomeCategoria }}
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                        @if ($faturas->count() != 0 && $fatura->Fechada == 0)
                                            <form id="edita" role="form" action="{{ route('cartoes.edit_despesa', ['ID_Despesa' => $fatura->ID_Despesa]) }}" method="GET" class="m-0 p-0">
                                                <input type="hidden" name="ID_Despesa" value="{{ $fatura->ID_Despesa }}">
                                                <input type="hidden" name="ID_Cartao" value="{{ $fatura->ID_Cartao }}">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('cartoes.destroy_despesa', ['ID_Despesa'=> $fatura->ID_Despesa]) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="ID_Despesa" value="{{ $fatura->ID_Despesa }}">
                                                <input type="hidden" name="ID_Cartao" value="{{ $fatura->ID_Cartao }}">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                                        onclick="return confirm('Deseja realmente excluir este registro?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>


                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th style="text-align: center">Data</th>
                            <th style="text-align: center">Descrição</th>
                            <th style="text-align: center">Valor</th>
                            <th style="text-align: center">Categoria</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Operações</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6 col-12 h-150" id="Total">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total da fatura</span>
                        <span class="info-box-number">
                        {{ 'R$ ' .  str_replace("_",'.',
                                    str_replace(".",',',
                                    str_replace(",",'_',
                                    number_format($totalFatura, 2
                                    )))) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12" id="Fechamento">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Fechamento da fatura</span>
                        <span class="info-box-number">
                        @if ($faturas->count() != 0)
                                @if (isset($fatura) && $fatura->Fechada == 0)
                                    ---
                                @else
                                    {{ isset($fatura) ? date('d/m/Y', strtotime($fatura->Data_fechamento)) : '---' }}
                                @endif
                            @else
                                ---
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-12" id="Botoes">
                <div class="info-box">
                    <div class="card-body row">
                        <div class="col-12">
                            @if ($faturas->count() != 0)
                                @if (isset($fatura) && $fatura->Fechada == 0)
                                    <form id="fatura{{Session::get('ID_Cartao')}}" role="form" action="{{ route('cartoes.new_despesa') }}" method="GET">
                                        <input type="hidden" name="ID_Cartao" value="{{Session::get('ID_Cartao')}}">
                                        <button type="submit" class="btn btn-success btn-block" title="Adicionar despesa">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form id="fatura{{Session::get('ID_Cartao')}}" role="form" action="{{ route('cartoes.new_despesa') }}" method="GET">
                                    <input type="hidden" name="ID_Cartao" value="{{Session::get('ID_Cartao')}}">
                                    <button type="submit" class="btn btn-success btn-block" title="Adicionar despesa">
                                        <span class="fa fa-plus"></span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="col-12">
                            @if ($faturas->count() != 0)
                                @if (isset($fatura) && $fatura->Fechada  == 0)
                                    <button type="submit" class="btn btn-warning btn-block" title="Fechar fatura"
                                            data-toggle="modal" data-target="#fechaFatura">
                                        <span class="fas fa-money-bill-wave"></span>
                                    </button>
                                    <form id="fatura" role="form" action="{{ route('cartoes.fatura_fechar') }}" method="GET">
                                        <input type="hidden" name="ID_Cartao" value="{{ Session::get('ID_Cartao') }}">
                                        <input type="hidden" name="Ano_Mes" value="{{$Ano_Mes}}">
                                        <div class="modal fade" id="fechaFatura">
                                            <div class="modal-dialog  modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title"> Fechar fatura </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning alert-dismissible">
                                                            <label>Total da fatura: </label>
                                                            {{ 'R$ ' .  str_replace("_",'.',
                                                                        str_replace(".",',',
                                                                        str_replace(",",'_',
                                                                        number_format($totalFatura, 2
                                                                        )))) }}
                                                        </div>

                                                        <label>Data</label>
                                                        <div class="form-group">
                                                            <div class="input-group date" id="Data_Fechamento" data-target-input="nearest">
                                                                <div class="input-group-append" data-target="#Data_Fechamento" data-toggle="datetimepicker">
                                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                </div>
                                                                <input type="text" class="form-control datetimepicker-input" data-target="#Data_Fechamento" name="Data_Fechamento"
                                                                       data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                                                                       placeholder="dd/mm/yyyy" value="{{ old('Data', \Carbon\Carbon::now()->format('d/m/Y')) }}"
                                                                />
                                                            </div>
                                                        </div>

                                                        <label>Conta</label>
                                                        <select class="custom-select" id="Conta" name="Conta">
                                                            <option selected data-default>- Selecione uma conta -</option>
                                                            @foreach($contas as $conta)
                                                                <option value="{{$conta->ID_Conta}}"> {{$conta->Banco . ' - ' . $conta->Nome }}  </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="card-footer">
                                                            <div class="float-right">
                                                                <button type="submit" class="btn btn-success">Pagar fatura</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <form id="fatura{{ Session::get('ID_Cartao') }}" role="form" action="{{ route('cartoes.fatura_reabrir') }}" method="GET">
                                        <input type="hidden" name="ID_Cartao" value="{{ Session::get('ID_Cartao') }}">
                                        <input type="hidden" name="Ano_Mes" value="{{$Ano_Mes}}">
                                        <button type="submit" class="btn btn-danger btn-block" title="Reabrir fatura"
                                                onclick="return confirm('Deseja realmente reabrir a fatura?')">
                                            <span class="fas fa-money-bill-wave"></span>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" xintegrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
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

            document.getElementById('Data').value = ano + '-' + mes;

            var data = ano + '-' + mes;


            url = '{{ route("cartoes.fatura",array("Ano_Mes" => 'DATA') ) }}';
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
            document.getElementById('Data').value = ano + '-' + mes;

            var data = ano + '-' + mes;

            url = '{{ route("cartoes.fatura",array("Ano_Mes" => 'DATA') ) }}';
            url = url.replace('DATA', data);

            window.location.href = url;

        }

        function redirecionaParaDataSelecionada() {
            const data = document.getElementById('Data').value;
            let url = '{{ route("cartoes.fatura", ["Ano_Mes" => "DATA"]) }}';
            url = url.replace('DATA', data);
            window.location.href = url;
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('Ano_Mes');
            if (myParam == null) {
                const dateObj = new Date();
                const faturaFechada = {{ $fechada ? 'true' : 'false' }};
                if (faturaFechada) {
                    var month   = dateObj.getUTCMonth() + 2; // months from 1-12
                }else{
                    var month   = dateObj.getUTCMonth() + 1; // months from 1-12
                }

                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                const year    = dateObj.getUTCFullYear();
                const Ano_Mes = year + '-' + month;
                //document.getElementById(Ano_Mes).classList.add(("active"));
                document.getElementById('Data').value = Ano_Mes;

            }
            else{
                document.getElementById('Data').value = myParam;
            }
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>

    <script>
        //Date picker
        let ultimaData = $('#Data').val();
        //Date picker
        $('#divData').datetimepicker({
            format: 'YYYY-MM',
            viewMode: 'months',
            minViewMode: 'months',
            locale: 'pt-br',
            defaultDate: moment(), // já inicia com data atual
        });
        $('#divData').on('hide.datetimepicker', function () {
            const novaData = $('#Data').val();

            if (novaData !== ultimaData) {
                ultimaData = novaData;
                redirecionaParaDataSelecionada();
            }
        });
        $('[data-mask]').inputmask(); // mantenha para os outros
        $('#Data').inputmask('remove'); // remove do campo do seletor de mês

        $('#Data_Fechamento').datetimepicker({
            format:'DD/MM/YYYY',
        })
    </script>
@stop
