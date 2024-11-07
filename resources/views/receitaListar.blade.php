@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')


    <!--Filtro -->
    <form id="filtro" role="form" action="#" method="GET">
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
                        <div class="form-group" id="filtroPrincipal">
                            <div class="row" id="div1">
                                <div class="col-6">
                                    <select class="form-control" id="categoria" name="categoria">
                                        <option selected data-default>- Selecione uma categoria -</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->ID_Categoria }}"> {{ $categoria->Nome  }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-control" id="conta" name="conta"  >
                                        <option selected data-default>- Selecione uma conta -</option>
                                        @foreach($contas as $conta)
                                            <option value="{{ $conta->ID_Conta  }}"> {{ $conta->Banco  }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--Contas e categorias-->

                        <!--Datas-->
                        <div class="form-group">

                            <div class="row" id="div1">
                                <div class="col-6">
                                    <input type="text" class="form-control" id="texto1" name="texto1" placeholder="Digite a texto a ser buscado">
                                </div>
                                <div class="col-1 text-end ">
                                    <label> Data inicial: </label>
                                </div>

                                <div class="col-2">
                                    <input type="date" name="start_date" class="form-control">

                                </div>

                                <div class="col-1">
                                    <label> Data final: </label>
                                </div>

                                <div class="col-2">
                                    <input type="date" name="end_date"  class="form-control">
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

                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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
                            <li class="page-item">
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

                                    <div class="row" id="div1">
                                        <div class="col-3">

                                            <form id="efetiva" role="form" action="{{ route('home', ['ID_Receita' =>$receita->ID_Receita])  }}" method="GET">
                                                <input type="hidden" name="page" value="{{-- $receita->currentPage() --}}">
                                                <button type="submit" class="btn btn-success"
                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <span class="fa fa-check"></span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-3">
                                            <form id="edita" role="form" action="{{ route('home', ['ID_Receita' =>$receita->ID_Receita])  }}" method="GET">
                                                <input type="hidden" name="page" value="{{-- $receita->currentPage() --}}">
                                                <button type="submit" class="btn btn-primary"
                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <span class="fa fa-edit"></span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-3">
                                            <form action="{{ route('home', ['ID_Receita'=> $receita->ID_Receita])  }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="ID_Receita" value="{{ $receita->ID_Receita }}">
                                                <input type="hidden" name="page" value="{{-- $receita->currentPage() --}}">
                                                <button type="submit" class="btn btn-danger"
                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
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
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


@stop

@section('js')

    <script src="/contas/resources/js/boot/jquery/jquery.min.js"></script>
    <!--<script src="../../plugins/jquery/jquery.min.js"></script> -->
    <!-- Bootstrap 4 -->

    <!-- DataTables  & Plugins -->
    <script src="/contas/resources/js/boot/datatables/jquery.dataTables.min.js"></script>
    <script src="/contas/resources/js/boot/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script src="/contas/resources/js/boot/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/contas/resources/js/boot/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="/contas/resources/js/boot/jszip/jszip.min.js"></script>
    <script src="/contas/resources/js/boot/pdfmake/pdfmake.min.js"></script>
    <script src="/contas/resources/js/boot/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="/contas/resources/js/boot/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="/contas/resources/js/boot/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script>
        $(function () {
            //para habilitar os filtros da tabela altere o nome para example1
            $("#example").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>

@stop
