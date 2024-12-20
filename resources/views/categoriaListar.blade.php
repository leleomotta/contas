@extends('adminlte::page')

@section('title', 'Categorias')

@section('content_header')

@stop

@section('content')


    <!--Filtro -->
    <form id="filtro" role="form" action="#" method="GET">

    </form>
    <!--/.Filtro -->

    <form id="registros" role="form" action="{{ route('receitas.showAll') }}" method="GET">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <!-- Listagem das despesas-->
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i>
                            Despesas
                        </h3>
                    </div>
                    <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($despesas as $despesa)
                            <tr bgcolor="{{ $despesa->Cor  }}">
                                <td>{{ $despesa->ID_Categoria }}</td>
                                <td>{{ $despesa->Nome }}</td>
                                <td>
                                    <div class="row">

                                        <div class="col-3">
                                            <form id="edita" role="form" action="{{ route('categorias.edit', ['ID_Categoria' =>$despesa->ID_Categoria])  }}" method="GET">
                                                <button type="submit" class="btn btn-primary"
                                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <span class="fa fa-edit"></span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-3">
                                            <form action="{{ route('categorias.destroy', ['ID_Categoria'=> $despesa->ID_Categoria])  }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="ID_Categoria" value="{{ $despesa->ID_Categoria }}">
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
                            <th>ID</th>
                            <th>Nome</th>
                            <th style="width: 110px">&nbsp;</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                    <!-- Listagem das despesas-->

                    <hr>
                    <!-- Listagem das receitas-->
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i>
                                Receitas
                        </h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th style="width: 110px">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($receitas as $receita)
                                <tr bgcolor="{{ $receita->Cor  }}">
                                    <td>{{ $receita->ID_Categoria }}</td>
                                    <td>{{ $receita->Nome }}</td>
                                    <td>
                                        <div class="row">

                                            <div class="col-3">
                                                <form id="edita" role="form" action="{{ route('categorias.edit', ['ID_Categoria' =>$receita->ID_Categoria])  }}" method="GET">
                                                    <button type="submit" class="btn btn-primary"
                                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                        <span class="fa fa-edit"></span>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="col-3">
                                                <form action="{{ route('categorias.destroy', ['ID_Categoria'=> $receita->ID_Categoria])  }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <input type="hidden" name="ID_Categoria" value="{{ $receita->ID_Categoria }}">
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
                                <th>ID</th>
                                <th>Nome</th>
                                <th style="width: 110px">&nbsp;</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Listagem das receitas-->

                </div>
            </div>
        </div>

        <!-- -->

        <!-- -->
    </form>



    @stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
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

    <!-- SweetAlert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
    <script type="text/javascript">
        //quando carrega a página ele carrega as validações
        $(document).ready(function () {
            @if (Session::has('naoapagado'))
                @if ( session('naoapagado') )
                    erro('Não é possivel apagar a categoria.');
                @endif
            @endif
        });
    </script>
    <script>
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        function erro(mensagem) {
            Toast.fire({
                icon: 'error',
                title: mensagem
            })
        }
    </script>

@stop
