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
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Receitas</span>
                                <span class="info-box-number">
                                        {{ 'R$ ' .  str_replace("_",'.',
                                                    str_replace(".",',',
                                                    str_replace(",",'_',
                                                    number_format($receitas->sum('Valor'), 2
                                                    )))) }}
                                    </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Despesas</span>
                                <span class="info-box-number">
                                        {{ 'R$ ' .  str_replace("_",'.',
                                                    str_replace(".",',',
                                                    str_replace(",",'_',
                                                    number_format($despesas->sum('Valor'), 2
                                                    )))) }}
                                    </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Saldo</span>
                                <span class="info-box-number">
                                        {{ 'R$ ' .  str_replace("_",'.',
                                                    str_replace(".",',',
                                                    str_replace(",",'_',
                                                    number_format($receitas->sum('Valor') - $despesas->sum('Valor'), 2
                                                    )))) }}
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <canvas id="Receitas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <canvas id="Despesas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <canvas id="Cartao" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                </div>

                <!-- THE CALENDAR -->
                <!--<div id="calendar"></div> -->
            </div>
            <!-- /Listagem-->
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

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>
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

            url = '{{ route("home.showAll",array("date_filter" => 'DATA' ) ) }}';
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

            url = '{{ route("home.showAll",array("date_filter" => 'DATA' ) ) }}';
            url = url.replace('DATA', data);

            window.location.href = url;

        }

        function redirecionaParaDataSelecionada() {
            const data = document.getElementById('Data').value;
            let url = '{{ route("home.showAll", ["date_filter" => "DATA"]) }}';
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
        $('[data-mask]').inputmask();

    </script>
    <!-- INPUT DATE -->


    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
        function gerar_cor_hexadecimal()
        {
            return '#' + parseInt((Math.random() * 0xFFFFFF))
                .toString(16)
                .padStart(6, '0');
        }
        //$questoesAno = DB::table('questao')->selectRaw('Ano, count(Ano) as Quantidade')->groupBy('Ano')->get();
        //agrupar o receitas por tipo e depois ir colocando nos lugares do gráfico, somando os valores
        $(function () {
            //RECEITAS
            // Get context with jQuery - using jQuery's .get() method.
            @php
                $ordenado = $receitas->sortBy(function($categoria){
                    return $categoria->NomeCategoria;
                });
                $receitasSoma = $ordenado->groupBy('NomeCategoria')->map(function ($categoria) { return $categoria->sum('Valor'); });
                $chaves = $receitasSoma->keys();
                $valores = $receitasSoma->values();
            @endphp

            var ReceitasData        = {
                labels: [
                    //dd($chaves[0]); // "Outros"
                    @for ($i = 0; $i < $chaves->count() ; $i++)
                        "{{ $chaves[$i] }}" ,
                    @endfor

                ],
                datasets: [
                    {
                        data: [
                            @for ($i = 0; $i < $chaves->count() ; $i++)
                                {{ $valores[$i] }} ,
                            @endfor
                        ],
                        backgroundColor : [
                            @for ($i = 0; $i < $chaves->count() ; $i++)
                            gerar_cor_hexadecimal(),
                            @endfor
                        ],
                    }
                ]
            }
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#Receitas').get(0).getContext('2d')
            var pieData        = ReceitasData;
            var pieOptions     = {
                maintainAspectRatio : false,
                responsive : true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var Receitas = new Chart(pieChartCanvas, {
                type: 'pie',
                data: pieData,
                options: pieOptions
            })
            //RECEITAS

            //DESPESAS
            // Get context with jQuery - using jQuery's .get() method.
            @php
                $despesasSoma = $despesas->groupBy('NomeCategoria')->map(function ($categoria) { return $categoria->sum('Valor'); });
                $chaves = $despesasSoma->keys();
                $valores = $despesasSoma->values();
            @endphp

            var DespesasData        = {
                labels: [
                    //dd($chaves[0]); // "Outros"
                    @for ($i = 0; $i < $chaves->count() ; $i++)
                        "{{ $chaves[$i] }}" ,
                    @endfor

                ],
                datasets: [
                    {
                        data: [
                            @for ($i = 0; $i < $chaves->count() ; $i++)
                                {{ $valores[$i] }} ,
                            @endfor
                        ],
                        backgroundColor : [
                            @for ($i = 0; $i < $chaves->count() ; $i++)
                            gerar_cor_hexadecimal(),
                            @endfor
                        ],
                    }
                ]
            }
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#Despesas').get(0).getContext('2d')
            var pieData        = DespesasData;
            var pieOptions     = {
                maintainAspectRatio : false,
                responsive : true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var Despesas = new Chart(pieChartCanvas, {
                type: 'pie',
                data: pieData,
                options: pieOptions
            })
            //DESPESAS

            //CARTÃO
            // Get context with jQuery - using jQuery's .get() method.
            @php
                $despesasCartaoSoma = $despesasCartao->groupBy('NomeCategoria')->map(function ($categoria) { return $categoria->sum('Valor'); });
                $chaves = $despesasCartaoSoma->keys();
                $valores = $despesasCartaoSoma->values();
            @endphp

            var CartaoData        = {
                labels: [
                    //dd($chaves[0]); // "Outros"
                    @for ($i = 0; $i < $chaves->count() ; $i++)
                        "{{ $chaves[$i] }}" ,
                    @endfor

                ],
                datasets: [
                    {
                        data: [
                            @for ($i = 0; $i < $chaves->count() ; $i++)
                                {{ $valores[$i] }} ,
                            @endfor
                        ],
                        backgroundColor : [
                            @for ($i = 0; $i < $chaves->count() ; $i++)
                            gerar_cor_hexadecimal(),
                            @endfor
                        ],
                    }
                ]
            }
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#Cartao').get(0).getContext('2d')
            var pieData        = CartaoData;
            var pieOptions     = {
                maintainAspectRatio : false,
                responsive : true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var Cartao = new Chart(pieChartCanvas, {
                type: 'pie',
                data: pieData,
                options: pieOptions
            })
            //CARTÃO
        })
    </script>
@stop
