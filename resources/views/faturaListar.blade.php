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
                <!-- Seletor de mês/ano -->
                <div class="col-2 mx-auto">
                    <div class="input-group date" id="divData" data-target-input="nearest">

                        <div class="input-group-append" onclick="voltaData()">
                            <div class="input-group-text"><i class="fa fa-angle-left"></i></div>
                        </div>

                        <input type="text" class="form-control datetimepicker-input" data-target="#divData" id="Data" name="Data"
                               data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask
                               placeholder="yyyy-mm"  data-toggle="datetimepicker"
                               style="text-align:center;"
                        />

                        <div class="input-group-append" onclick="avancaData()">
                            <div class="input-group-text"><i class="fa fa-angle-right"></i></div>
                        </div>

                    </div>
                </div>
                <!-- /Seletor de mês/ano -->

                <!-- Listagem-->
                <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="text-align: center">ID</th>
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
                            <td>{{ $fatura->ID_Despesa  }}</td>
                            <td style="text-align: center">{{ date('d/m/Y', strtotime($fatura->Data)) }}</td>
                            <td>{{ $fatura->Descricao  }}</td>
                            <td>{{ 'R$ ' .  str_replace("_",'.',
                                            str_replace(".",',',
                                            str_replace(",",'_',
                                            number_format($fatura->Valor, 2
                                            )))) }}</td>
                            <td>{{$fatura->NomeCategoria}}</td>
                            <td>

                                <div class="row">
                                    <div class="col-3">
                                        @if ($faturas->count() <> 0 )
                                            @if ($fatura->Fechada == 0)
                                                <form id="edita" role="form" action="{{ route('cartoes.edit_despesa', ['ID_Despesa' =>$fatura->ID_Despesa])  }}" method="GET">
                                                    <input type="hidden" name="ID_Despesa" value="{{ $fatura->ID_Despesa }}">
                                                    <input type="hidden" name="ID_Cartao" value="{{ $fatura->ID_Cartao }}">
                                                    <button type="submit" class="btn btn-primary"
                                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                        <span class="fa fa-edit"></span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="col-3">
                                        @if ($faturas->count() <> 0 )
                                            @if ($fatura->Fechada == 0)
                                                <form action="{{ route('cartoes.destroy_despesa', ['ID_Despesa'=> $fatura->ID_Despesa])  }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <input type="hidden" name="ID_Despesa" value="{{ $fatura->ID_Despesa }}">
                                                    <input type="hidden" name="ID_Cartao" value="{{ $fatura->ID_Cartao }}">

                                                    <button type="submit" class="btn btn-danger"
                                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
                                                            onclick="return confirm('Deseja realmente excluir este registro?')">
                                                        <span class="fa fa-trash"></span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: center">ID</th>
                            <th style="text-align: center">Data</th>
                            <th style="text-align: center">Descrição</th>
                            <th style="text-align: center">Valor</th>
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

    <!-- Rodapé -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Operações&nbsp;&nbsp;</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>

        <div class="card-body">
            <div class="row" id="div1">
                <div class="info-box col-3">
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

                <div class="info-box col-3">
                    <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Fechamento da fatura</span>
                        <span class="info-box-number">
                            @if ($faturas->count() <> 0 )
                                @if ($fatura->Fechada == 0)
                                    ---
                                @else
                                    {{ date('d/m/Y', strtotime($fatura->Data_fechamento)) }}
                                @endif
                            @else
                                ---
                            @endif
                        </span>
                    </div>
                </div>

                <div class="info-box col-6">
                    <div class="info-box-content">
                        <form id="fatura{{Session::get('ID_Cartao')}}" role="form" action="{{ route('cartoes.new_despesa') }}" method="GET">
                            <input type="hidden" name="ID_Cartao" value="{{Session::get('ID_Cartao')}}">
                            <button type="submit" class="btn btn-success btn-block" title="Adicionar despesa">
                                <span class="fa fa-plus"></span>
                            </button>
                        </form>
                        @if ($faturas->count() <> 0 )
                            @if ($fatura->Fechada  == 0)
                                <button type="submit" class="btn btn-warning btn-block" title="Fechar fatura"
                                        data-toggle="modal" data-target="#fechaFatura">
                                    <span class="fas fa-money-bill-wave"></span>
                                </button>

                                <!-- Modal de figura -->
                                <form id="fatura" role="form" action="{{ route('cartoes.fatura_fechar') }}" method="GET">
                                    <input type="hidden" name="ID_Cartao" value="{{ Session::get('ID_Cartao') }}">
                                    <input type="hidden" name="Ano_Mes" value="{{app('request')->input('Ano_Mes')}}">
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
                                                                   placeholder="dd/mm/yyyy" value=" "
                                                            />
                                                        </div>
                                                    </div>

                                                    <label>Conta</label>
                                                    <select class="custom-select" id="Conta" name="Conta">
                                                        <option selected data-default>- Selecione uma conta -</option>
                                                        @foreach($contas as $conta)
                                                            <option value="{{$conta->ID_Conta}}"> {{$conta->Banco . ' - ' . $conta->Descricao }}  </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="card-footer">
                                                        <div class="float-right">
                                                            <button type="submit" class="btn btn-success">Pagar fatura</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- Modal de figura -->
                                </form>
                            @else
                                <form id="fatura{{ Session::get('ID_Cartao') }}" role="form" action="{{ route('cartoes.fatura_reabrir') }}" method="GET">
                                    <input type="hidden" name="ID_Cartao" value="{{ Session::get('ID_Cartao') }}">
                                    <input type="hidden" name="Ano_Mes" value="{{app('request')->input('Ano_Mes')}}">
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
    <!-- Rodapé -->

    @stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
@stop

@section('js')
    <script>

        function voltaData() {
            var ano = parseInt( document.getElementById('Data').value.substring(0,4) );
            var mes = parseInt( document.getElementById('Data').value.substring(5,8) );

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
            var ano = parseInt( document.getElementById('Data').value.substring(0,4) );
            var mes = parseInt( document.getElementById('Data').value.substring(5,8) );
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

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('Ano_Mes');
            if (myParam == null) {
                const dateObj = new Date();
                var month   = dateObj.getUTCMonth() + 1; // months from 1-12
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

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>

    <script>
        //Date picker
        $('#divData').datetimepicker({
            //format:'DD/MM/YYYY',
            format:'YYYY-MM',
            viewMode: "months",
            minViewMode: "months",

        })

        $('#Data_Fechamento').datetimepicker({
            format:'DD/MM/YYYY',
        })
    </script>

    <!-- INPUT DATE -->
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
        $('input').inputmask();
    </script>

@stop
