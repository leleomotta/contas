@extends('adminlte::page')

@section('title', 'Receita - Listar')

@section('content_header')

@stop

@section('content')


    <!--Filtro -->
    <form id="filtro" role="form" action="{{ route('receitas.filter') }}" method="GET">
        <div class="card card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtro&nbsp;&nbsp;</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group" id="filtroPrincipal">

                    <div class="row" id="div1">
                        <!--Contas e categorias-->
                        <div class="form-group">
                            <div class="row">
                                <div class="form-row align-items-center">
                                    <div class="col-auto">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" name="chkCategoria" id="chkCategoria" onclick="habilitaCampos()">
                                            <label for="chkCategoria"> </label>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <select class="form-control" id="categoria" name="categoria" disabled>
                                            <option selected data-default>- Selecione uma categoria -</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->ID_Categoria }}"> {{ $categoria->Nome  }} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-auto">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" name="chkConta" id="chkConta" onclick="habilitaCampos()">
                                            <label for="chkConta"> </label>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <select class="form-control" id="conta" name="conta" disabled>
                                            <option selected data-default>- Selecione uma conta -</option>
                                            @foreach($contas as $conta)
                                                <option value="{{ $conta->ID_Conta  }}"> {{ $conta->Banco  }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Contas e categorias-->

                         <!--Texto e Datas-->
                        <div class="form-group">
                            <div class="row">
                                <div class="form-row align-items-center">
                                    <div class="col-auto">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" name="chkTexto" id="chkTexto" onclick="habilitaCampos()">
                                            <label for="chkTexto"> </label>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control" id="texto" name="texto" placeholder="Digite a texto a ser buscado" disabled/>
                                    </div>

                                    <div class="col-auto">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" name="chkDatas" id="chkDatas" onclick="habilitaCampos()">
                                            <label for="chkDatas"> </label>
                                        </div>
                                    </div>
                                    <div class="col-5">

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right" id="datas" name="datas" disabled />
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Datas-->

                    </div>
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-success btn-sm">Aplicar Filtro</button>
                </div>
            </div>
        </div>
    </form>
    <!--/.Filtro -->

    <!--<form id="registros" role="form" action="{{ route('receitas.showAll') }}" method="GET">-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card">
                </div>
                <!-- Seletor de mês/ano -->
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
                <!-- /Seletor de mês/ano -->



                <!-- Listagem-->
                <div class="card-body table-responsive p-0">
                    <table id="example1" class="table text-nowrap table-hover table-bordered border-light">
                    <thead>
                        <tr>
                            <th style="width: 50px">Efetivada</th>
                            <th style="text-align: center">Data</th>
                            <th style="text-align: center">Descrição</th>
                            <th style="text-align: center">Valor</th>
                            <th style="text-align: center">Conta</th>
                            <th style="text-align: center">Categoria</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($receitas as $receita)
                        <tr>
                            <td>
                                @if ($receita->Efetivada == 1)
                                    <img src="{{URL::asset('/storage/V.bmp')}}" >
                                @else
                                    <img src="{{URL::asset('/storage/X.bmp')}}" >
                                @endif
                            </td>
                            <td style="text-align: center">{{ date('d/m/Y', strtotime($receita->Data)) }}</td>
                            <td>{{ $receita->Descricao  }}</td>
                            <td>{{ 'R$ ' . number_format($receita->Valor, 2, ',', '.') }}</td>
                            <td>{{$receita->Banco}}</td>
                            <td>
                                <span class="icone-circulo" style="background-color: {{ $receita->Cor }};">
                                    <i class="{{ $receita->Icone }}"></i>
                                </span>
                                {{ $receita->NomeCategoria }}
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                    <form id="efetiva_{{ $receita->ID_Receita }}" role="form" action="{{ route('receitas.efetiva', ['ID_Receita' =>$receita->ID_Receita])  }}" method="GET" class="m-0 p-0">
                                        @if ($receita->Efetivada == 1)
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Desefetivar"
                                                    onclick="return confirm('Deseja realmente desefetivar esta receita?')">
                                                <i class="fa fa-window-close"></i>
                                            </button>
                                        @else
                                            <input type="hidden" name="date_filter" value="{{ app('request')->input('date_filter') }}">
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    title="Efetivar"
                                                    onclick="return confirm('Deseja realmente efetivar esta receita?')">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        @endif
                                    </form>

                                    <form id="edita" role="form" action="{{ route('receitas.edit', ['ID_Receita' => $receita->ID_Receita]) }}" method="GET" class="m-0 p-0">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('receitas.destroy', ['ID_Receita'=> $receita->ID_Receita]) }}" method="POST" class="m-0 p-0">
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
                    <tfoot>
                        <tr>
                            <th style="width: 50px">Efetivada</th>
                            <th style="text-align: center">Data</th>
                            <th style="text-align: center">Descrição</th>
                            <th style="text-align: center">Valor</th>
                            <th style="text-align: center">Conta</th>
                            <th style="text-align: center">Categoria</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
                </div>
                <!-- /Listagem-->
            </div>
        </div>
    </div>
    <!--</form> -->

    <!-- Rodapé -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Saldos&nbsp;&nbsp;</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>

        <div class="card-body">
            <div class="row" id="div1">
                <div class="info-box col-md-6 col-sm-6 col-12">
                    <span class="info-box-icon bg-warning"><i class="fa fa-lock"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Pendente</span>
                        <span class="info-box-number">
                            {{ 'R$ ' . number_format($pendente, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="info-box col-md-6 col-sm-6 col-12">
                    <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Recebido</span>
                        <span class="info-box-number">
                            {{ 'R$ ' . number_format($recebido, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Rodapé -->

    @stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Data --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

    {{-- daterange picker --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
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

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>

    <script>
        $('#datas').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            }
        });

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

            url = '{{ route("receitas.showAll",array("date_filter" => 'DATA' ) ) }}';
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

            url = '{{ route("receitas.showAll",array("date_filter" => 'DATA' ) ) }}';
            url = url.replace('DATA', data);

            window.location.href = url;

        }

        function redirecionaParaDataSelecionada() {
            const data = document.getElementById('Data').value;
            let url = '{{ route("receitas.showAll", ["date_filter" => "DATA"]) }}';
            url = url.replace('DATA', data);
            window.location.href = url;
        }

        function habilitaCampos() {
            document.getElementById("categoria").disabled = !document.getElementById("chkCategoria").checked;
            document.getElementById("conta").disabled = !document.getElementById("chkConta").checked;
            document.getElementById("datas").disabled = !document.getElementById("chkDatas").checked;
            document.getElementById("texto").disabled = !document.getElementById("chkTexto").checked;
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('date_filter');
            if (myParam == null) {
                const dateObj = new Date();
                var month   = dateObj.getUTCMonth() + 1; // months from 1-12
                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                const year    = dateObj.getUTCFullYear();
                const date_filter = year + '-' + month;
                //document.getElementById(date_filter).classList.add(("active"));
                document.getElementById('Data').value = date_filter;

            }
            else{
                document.getElementById('Data').value = myParam;
            }

        };
    </script>

    <!-- INPUT DATE -->
    <script>
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

    </script>
    <!-- INPUT DATE -->
@stop
