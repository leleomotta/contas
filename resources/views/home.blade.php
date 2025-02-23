@extends('adminlte::page')

@section('title', 'Dashboard')

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
                    <div class="card-body">
                        <div class="row" id="div1">
                            <div class="info-box col-4">
                                <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Receitas</span>
                                    <span class="info-box-number">
                        {{ 'R$ ' .  str_replace("_",'.',
                                    str_replace(".",',',
                                    str_replace(",",'_',
                                    number_format($receitas, 2
                                    )))) }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-box col-4">
                                <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Despesas</span>
                                    <span class="info-box-number">
                        {{ 'R$ ' .  str_replace("_",'.',
                                    str_replace(".",',',
                                    str_replace(",",'_',
                                    number_format($despesas, 2
                                    )))) }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-box col-4">
                                <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Saldo</span>
                                    <span class="info-box-number">
                        {{ 'R$ ' .  str_replace("_",'.',
                                    str_replace(".",',',
                                    str_replace(",",'_',
                                    number_format($receitas - $despesas, 2
                                    )))) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- THE CALENDAR -->
                    <!--<div id="calendar"></div> -->
                </div>
                <!-- /Listagem-->

            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
@stop

@section('js')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth'
            });
            calendar.render();
        });
    </script>





    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>
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

            url = '{{ route("home.showAll",array("date_filter" => 'DATA' ) ) }}';
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

            url = '{{ route("home.showAll",array("date_filter" => 'DATA' ) ) }}';
            url = url.replace('DATA', data);

            window.location.href = url;

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
