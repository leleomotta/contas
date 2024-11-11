@extends('adminlte::page')

@section('title', 'Dashboard')

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

    <form id="registros" role="form" action="{{ route('receitas.showAll') }}" method="GET">
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->subMonth(5)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->subMonth(5)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->subMonth(4)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->subMonth(4)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->subMonth(3)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->subMonth(3)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->subMonth(2)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->subMonth(2)->isoFormat('MM') }}" type="submit" >
                                    <p class="page-month">
                                        {{ \Carbon\Carbon::now()->subMonth(2)->isoFormat('MMM') }}
                                    </p>
                                    <p class="page-year">
                                        {{ \Carbon\Carbon::now()->subMonth(2)->format('Y') }}
                                    </p>

                                </a>
                            </li>
                            <li id="{{ \Carbon\Carbon::now()->subMonth(1)->isoFormat('Y') . '-' . \Carbon\Carbon::now()->subMonth(1)->isoFormat('MM') }}" class="page-item">
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->subMonth(1)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->subMonth(1)->isoFormat('MM') }}" type="submit" >
                                    <p class="page-month">
                                        {{ \Carbon\Carbon::now()->subMonth(1)->isoFormat('MMM') }}
                                    </p>
                                    <p class="page-year">
                                        {{ \Carbon\Carbon::now()->subMonth(1)->format('Y') }}
                                    </p>
                                </a>
                            </li>
                            <li id="{{ \Carbon\Carbon::now()->isoFormat('Y') . '-' . \Carbon\Carbon::now()->isoFormat('MM') }}" class="page-item">
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->addMonth(1)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->addMonth(1)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->addMonth(2)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->addMonth(2)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->addMonth(3)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->addMonth(3)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->addMonth(4)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->addMonth(4)->isoFormat('MM') }}" type="submit" >
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
                                <a class="page-link" href="receitas?date_filter={{ \Carbon\Carbon::now()->addMonth(5)->isoFormat('Y') . '-' .
                                                                    \Carbon\Carbon::now()->addMonth(5)->isoFormat('MM') }}" type="submit" >
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
                            <th style="width: 50px">Efetivada</th>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Conta</th>
                            <th style="text-align: center">Categoria</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($receitas as $receita)
                            <tr>
                                <td>
                                    @if ($receita->Efetivada == 1)
                                        <img src='/questoes/resources/img/V.bmp'>
                                    @else
                                        <img src='/questoes/resources/img/X.bmp'>
                                    @endif
                                </td>
                                <td style="text-align: center">{{ date('d/m/Y', strtotime($receita->Data)) }}</td>
                                <td>{{ $receita->Descricao  }}</td>
                                <td>{{ 'R$ ' .  str_replace("-",'.',
                                                str_replace(".",',',
                                                str_replace(",",'-',
                                                number_format($receita->Valor, 2
                                                )))) }}</td>
                                <td>{{$receita->Banco}}</td>
                                <td>{{$receita->NomeCategoria}}</td>
                                <td>

                                    <div class="row">
                                        <div class="col-3">
                                            <form id="bundão" role="form" action="{{ route('receitas.edit', ['ID_Receita' =>$receita->ID_Receita])  }}" method="GET">
                                                <button type="submit" class="btn btn-primary"
                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <span class="fa fa-edit"></span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-3">
                                            <form id="edita" role="form" action="{{ route('receitas.edit', ['ID_Receita' =>$receita->ID_Receita])  }}" method="GET">
                                                <button type="submit" class="btn btn-primary"
                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <span class="fa fa-edit"></span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-3">
                                            <form action="{{ route('receitas.destroy', ['ID_Receita'=> $receita->ID_Receita])  }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="ID_Receita" value="{{ $receita->ID_Receita }}">
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
                            <th style="width: 50px">Efetivada</th>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Conta</th>
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
    </form>

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

        function habilitaCampos() {
            document.getElementById("categoria").disabled = !document.getElementById("chkCategoria").checked;
            document.getElementById("conta").disabled = !document.getElementById("chkConta").checked;
            document.getElementById("datas").disabled = !document.getElementById("chkDatas").checked;
            document.getElementById("texto").disabled = !document.getElementById("chkTexto").checked;
        }


        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('date_filter');
            //alert(myParam);
            document.getElementById(myParam).classList.add(("active"));
        };

    </script>
@stop
