@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- Paginador de meses -->
                <div class="card-body">
                    <ul class="pagination pagination-month justify-content-center">

                        <li class="page-item">
                            <a class="page-link" href="#">

                            </a>
                        </li>

                        <li id="{{ \Carbon\Carbon::now()->subMonth(5)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(5)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->subMonth(5)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(5)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->subMonth(5)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->subMonth(5)->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->subMonth(4)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(4)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->subMonth(4)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(4)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->subMonth(4)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->subMonth(4)->format('Y') }}
                                </p>

                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->subMonth(3)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(3)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->subMonth(3)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(3)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->subMonth(3)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->subMonth(3)->format('Y') }}
                                </p>

                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->subMonth(2)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(2)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->subMonth(2)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(2)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->subMonth(2)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->subMonth(2)->format('Y') }}
                                </p>

                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->subMonth(1)->isoFormat('Y') . '-' . \Carbon\Carbon::now()->subMonth(1)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->subMonth(1)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->subMonth(1)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->subMonth(1)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->subMonth(1)->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->isoFormat('Y') . '-' . \Carbon\Carbon::now()->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->addMonth(1)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(1)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->addMonth(1)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(1)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->addMonth(1)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->addMonth(1)->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->addMonth(2)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(2)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->addMonth(2)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(2)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->addMonth(2)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->addMonth(2)->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->addMonth(3)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(3)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->addMonth(3)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(3)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->addMonth(3)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->addMonth(3)->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->addMonth(4)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(4)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->addMonth(4)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(4)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->addMonth(4)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->addMonth(4)->format('Y') }}
                                </p>
                            </a>
                        </li>
                        <li id="{{ \Carbon\Carbon::now()->addMonth(5)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(5)->isoFormat('MM') }}" class="page-item">
                            <a class="page-link" href="fatura?Ano_Mes={{ \Carbon\Carbon::now()->addMonth(5)->isoFormat('Y') . '-' .
                                                                \Carbon\Carbon::now()->addMonth(5)->isoFormat('MM') }}&ID_Cartao={{app('request')->input('ID_Cartao')}}" type="submit" >
                                <p class="page-month">
                                    {{ \Carbon\Carbon::now()->addMonth(5)->isoFormat('MMM') }}
                                </p>
                                <p class="page-year">
                                    {{ \Carbon\Carbon::now()->addMonth(5)->format('Y') }}
                                </p>
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">

                            </a>
                        </li>
                    </ul>
                </div>
                <!-- /Paginador de meses -->

                <!-- Listagem-->
                <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
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
                            <td>{{ 'R$ ' .  str_replace("-",'.',
                                            str_replace(".",',',
                                            str_replace(",",'-',
                                            number_format($fatura->Valor, 2
                                            )))) }}</td>
                            <td>{{$fatura->NomeCategoria}}</td>
                            <td>

                                <div class="row">
                                    Botões
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
                <!-- /Listagem-->

            </div>
        </div>
    </div>


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
                    <div class="info-box col-6">
                        <span class="info-box-icon bg-warning"><i class="fa fa-lock"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Pendente {{app('request')->input('ID_Cartao')}}</span>
                            <span class="info-box-number">
                                {{ 'R$ ' .  str_replace("-",'.',
                                            str_replace(".",',',
                                            str_replace(",",'-',
                                            number_format(909090, 2
                                            )))) }}
                            </span>
                        </div>
                    </div>

                <div class="info-box col-6">
                    <span class="info-box-icon bg-success"><i class="fa fa-coins"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Pago</span>
                        <span class="info-box-number">
                            <form id="fatura{{app('request')->input('ID_Cartao')}}" role="form" action="{{ route('cartoes.new_despesa') }}" method="GET">
                                            <input type="hidden" name="ID_Cartao" value="{{app('request')->input('ID_Cartao')}}">
                                            <a href="javascript:{}" onclick="document.getElementById('fatura{{app('request')->input('ID_Cartao')}}').submit();" class="btn btn-app">
                                                <span class="badge bg-info">12</span>
                                                <i class="fas fa-inbox"></i> Fatura
                                            </a>
                            </form>
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.css">
@stop

@section('js')
    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('Ano_Mes');
            if (myParam == null) {
                const dateObj = new Date();
                const month   = dateObj.getUTCMonth() + 1; // months from 1-12
                const year    = dateObj.getUTCFullYear();
                const Ano_Mes = year + '-' + month;
                document.getElementById(Ano_Mes).classList.add(("active"));
            }
            else{
                document.getElementById(myParam).classList.add(("active"));
            }
        };
    </script>
@stop
