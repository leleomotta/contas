@extends('adminlte::page')

@section('title', 'Categoria - Listar')

@section('content_header')

@stop

@section('content')


    <!--Filtro -->
    <form id="filtro" role="form" action="#" method="GET">

    </form>
    <!--/.Filtro -->

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Categorias</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Despesas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Receitas</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 110px">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($despesas as $despesa)
                                            <tr bgcolor="{{ $despesa->Cor  }}">
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
                                                        @unless ($despesa->ID_Categoria_Pai <> NULL)
                                                            <div class="col-3">
                                                                <form action="{{ route('categorias.new') }}" method="GET">
                                                                    <input type="hidden" name="ID_Categoria_Pai" value="{{ $despesa->ID_Categoria }}">
                                                                    <input type="hidden" name="Tipo" value="D">
                                                                    <button type="submit" class="btn btn-success"
                                                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
                                                                            onclick="return confirm('Deseja adicionar uma subcategoria?')">
                                                                        <span class="fa fa-plus"></span>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endunless
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 110px">&nbsp;</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 110px">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($receitas as $receita)
                                            <tr bgcolor="{{ $receita->Cor  }}">
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
                                                        @unless ($receita->ID_Categoria_Pai <> NULL)
                                                            <div class="col-3">
                                                                <form action="{{ route('categorias.new') }}" method="GET">
                                                                    <input type="hidden" name="ID_Categoria_Pai" value="{{ $receita->ID_Categoria }}">
                                                                    <input type="hidden" name="Tipo" value="R">
                                                                    <button type="submit" class="btn btn-success"
                                                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
                                                                            onclick="return confirm('Deseja adicionar uma subcategoria?')">
                                                                        <span class="fa fa-plus"></span>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endunless
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 110px">&nbsp;</th>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
@stop

@section('js')
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
