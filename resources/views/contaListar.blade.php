@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex p-0">

                    <h3 class="card-title p-3">Contas</h3>

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

                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Ativas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Inativas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">Tabela</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- .tab-pane -->
                        <div class="tab-pane active" id="tab_1">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <tbody>
                                        @foreach($contasAtivas->chunk(3) as $ativas)
                                            <div class="row">
                                                @foreach($ativas as $conta)
                                                    <div class="col-md-4">
                                                        <!-- Widget: user widget style 1 -->
                                                        <div class="card card-widget widget-user" >
                                                            <!-- Add the bg color to the header using any of the bg-* classes -->
                                                            <!-- <div class="widget-user-header bg-info"> -->

                                                            <div class="widget-user-header text-white"
                                                                     style="background:{{ $conta->Cor }}">
                                                                <h3 class="widget-user-username">{{ $conta->ID_Conta . '-' . $conta->Banco }}</h3>
                                                                <h5 class="widget-user-desc">{{ $conta->Descricao }}</h5>
                                                            </div>

                                                            <a onclick="window.location='{{ route('contas.edit', ['ID_Conta' =>$conta->ID_Conta]) }}'" >
                                                                <div class="widget-user-image">

                                                                    @if (! $conta->Imagem == null)
                                                                        <img class="img-circle elevation-2" src='data:image/jpeg;base64,{{base64_encode( $conta->Imagem ) }} ' alt="Imagem">
                                                                    @else
                                                                        <img class="img-circle elevation-2" border=0 ALIGN=MIDDLE src="{{URL::asset('/storage/banco.png')}}" alt="Banco">
                                                                    @endif
                                                                </div>
                                                            </a>

                                                            <div class="card-footer">
                                                                <div class="row">
                                                                    <div class="col-sm-4 border-right">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Saldo, 2
                                                                                            )))) }} </h5>
                                                                            <span class="description-text">SALDO ATUAL</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>
                                                                    <!-- /.col -->
                                                                    <div class="col-sm-4 border-right">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Receitas, 2
                                                                                            )))) }}</h5>
                                                                            <span class="description-text">RECEITAS</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>
                                                                    <!-- /.col -->
                                                                    <div class="col-sm-4">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Despesas, 2
                                                                                            )))) }}</h5>
                                                                            <span class="description-text">DESPESAS</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>

                                                                </div>
                                                                <!-- /.row -->
                                                                <div class="row">
                                                                    <div class="col-sm-4 border-right">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Entradas, 2
                                                                                            )))) }} </h5>
                                                                            <span class="description-text">Transf. Entrada</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>
                                                                    <!-- /.col -->
                                                                    <div class="col-sm-4 border-right">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Saidas, 2
                                                                                            )))) }}</h5>
                                                                            <span class="description-text">Transf. Saída</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>
                                                                    <!-- /.col -->
                                                                    <div class="col-sm-4">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->SaldoMes, 2
                                                                                            )))) }}</h5>
                                                                            <span class="description-text">SALDO MÊS</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /.widget-user -->
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <!-- .tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <tbody>
                                       @foreach($contasArquivadas->chunk(3) as $inativas)
                                            <div class="row">
                                                @foreach($inativas as $conta)
                                                    <div class="col-md-4">
                                                            <!-- Widget: user widget style 1 -->
                                                            <div class="card card-widget widget-user" >
                                                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                                                <!-- <div class="widget-user-header bg-info"> -->

                                                                <div class="widget-user-header text-white"
                                                                     style="background:{{ $conta->Cor }}">
                                                                    <h3 class="widget-user-username">{{ $conta->ID_Conta . '-' . $conta->Banco }}</h3>
                                                                    <h5 class="widget-user-desc">{{ $conta->Descricao }}</h5>
                                                                </div>

                                                                <a onclick="window.location='{{ route('contas.edit', ['ID_Conta' =>$conta->ID_Conta]) }}'" >
                                                                    <div class="widget-user-image">

                                                                        @if (! $conta->Imagem == null)
                                                                            <img class="img-circle elevation-2" src='data:image/jpeg;base64,{{base64_encode( $conta->Imagem ) }} ' alt="Imagem">
                                                                        @else
                                                                            <img class="img-circle elevation-2" border=0 ALIGN=MIDDLE src="{{URL::asset('/storage/banco.png')}}" alt="Banco">
                                                                        @endif
                                                                    </div>
                                                                </a>

                                                                <div class="card-footer">
                                                                    <div class="row">
                                                                        <div class="col-sm-4 border-right">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Saldo, 2
                                                                                                )))) }} </h5>
                                                                                <span class="description-text">SALDO ATUAL</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>
                                                                        <!-- /.col -->
                                                                        <div class="col-sm-4 border-right">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Receitas, 2
                                                                                                )))) }}</h5>
                                                                                <span class="description-text">RECEITAS</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>
                                                                        <!-- /.col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Despesas, 2
                                                                                                )))) }}</h5>
                                                                                <span class="description-text">DESPESAS</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>

                                                                    </div>
                                                                    <!-- /.row -->
                                                                    <div class="row">
                                                                        <div class="col-sm-4 border-right">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Entradas, 2
                                                                                                )))) }} </h5>
                                                                                <span class="description-text">Transf. Entrada</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>
                                                                        <!-- /.col -->
                                                                        <div class="col-sm-4 border-right">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Saidas, 2
                                                                                                )))) }}</h5>
                                                                                <span class="description-text">Transf. Saída</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>
                                                                        <!-- /.col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->SaldoMes, 2
                                                                                                )))) }}</h5>
                                                                                <span class="description-text">SALDO MÊS</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- /.widget-user -->
                                                        </div>
                                                @endforeach
                                            </div>
                                      @endforeach

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <!-- .tab-pane -->
                        <div class="tab-pane" id="tab_3">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center">Conta</th>
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
                                        $contasAtivas = $contasAtivas->merge($contasArquivadas);
                                        $sorted = $contasAtivas->sortBy(function($conta)
                                        {
                                            return $conta->ID_Conta;
                                        });
                                    @endphp
                                        @foreach($sorted as $conta)
                                            <tr>
                                                <td>
                                                    {{ $conta->ID_Conta . '-' . $conta->Banco }}
                                                </td>
                                                <td>
                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                str_replace(".",',',
                                                                str_replace(",",'_',
                                                                number_format($conta->Saldo, 2
                                                                )))) }}
                                                </td>
                                                <td>
                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                str_replace(".",',',
                                                                str_replace(",",'_',
                                                                number_format($conta->Receitas, 2
                                                                )))) }}
                                                </td>
                                                <td>
                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                               str_replace(".",',',
                                                               str_replace(",",'_',
                                                               number_format($conta->Despesas, 2
                                                               )))) }}
                                                </td>
                                                <td>
                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                str_replace(".",',',
                                                                str_replace(",",'_',
                                                                number_format($conta->Entradas, 2
                                                                )))) }}
                                                </td>
                                                <td>
                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                str_replace(".",',',
                                                                str_replace(",",'_',
                                                                number_format($conta->Saidas, 2
                                                                )))) }}
                                                </td>
                                                <td>
                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                str_replace(".",',',
                                                                str_replace(",",'_',
                                                                number_format($conta->SaldoMes, 2
                                                                )))) }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th style="text-align: center">Conta</th>
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
                        <!-- /.tab-pane -->

                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
    </div>



    @stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Data --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

    {{-- daterange picker --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">
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
            document.getElementById('Data').value = ano + '/' + mes;

            var data = ano + '-' + mes;

            url = '{{ route("contas.showAll",array("date_filter" => 'DATA' ) ) }}';
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
            document.getElementById('Data').value = ano + '/' + mes;


            var data = ano + '-' + mes;

            url = '{{ route("contas.showAll",array("date_filter" => 'DATA' ) ) }}';
            url = url.replace('DATA', data);

            window.location.href = url;

        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('date_filter');
            console.log('leo');
            console.log(urlParams);
            console.log('motta');
            console.log(myParam);
            if (myParam == null) {
                const dateObj = new Date();
                var month   = dateObj.getUTCMonth() + 1; // months from 1-12
                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                const year    = dateObj.getUTCFullYear();
                const date_filter = year + '-' + month;
                document.getElementById('Data').value = date_filter;
            }
            else{
                document.getElementById('Data').value = myParam;
            }
        };
    </script>

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>

    <!-- INPUT DATE -->
    <script>
        //Date picker
        $('#divData').datetimepicker({
            //format:'DD/MM/YYYY',
            format:'YYYY/MM',
            viewMode: "months",
            minViewMode: "months",

        })
        $('[data-mask]').inputmask();
    </script>
    <!-- INPUT DATE -->
@stop
