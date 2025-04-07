@extends('adminlte::page')

@section('title', 'Categoria - Listar')

@section('content_header')
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Categorias</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Despesas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Receitas</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <div class="table-responsive p-0">
                                <table id="despesasTable" class="table text-nowrap table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 110px;">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($despesas as $despesa)
                                            <tr>
                                                <td>
                                                    <span class="icone-circulo" style="background-color: {{ $despesa->Cor }};">
                                                        <i class="{{ $despesa->Link }}"></i>
                                                    </span>
                                                    {{ $despesa->Nome }}
                                                </td>

                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('categorias.edit', ['ID_Categoria' => $despesa->ID_Categoria]) }}" class="btn btn-primary btn-sm mr-1" title="Editar"><i class="fa fa-edit"></i></a>
                                                        <form action="{{ route('categorias.destroy', ['ID_Categoria' => $despesa->ID_Categoria]) }}" method="POST" class="mr-1">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="btn btn-danger btn-sm delete-btn" title="Excluir"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                        @if(is_null($despesa->ID_Categoria_Pai))
                                                            <form action="{{ route('categorias.new') }}" method="GET">
                                                                <input type="hidden" name="ID_Categoria_Pai" value="{{ $despesa->ID_Categoria }}">
                                                                <input type="hidden" name="Tipo" value="D">
                                                                <button type="submit" class="btn btn-success btn-sm" title="Adicionar Subcategoria"><i class="fa fa-plus"></i></button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Ações</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <div class="table-responsive p-0">
                                <table id="receitasTable" class="table text-nowrap table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width: 110px;">Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($receitas as $receita)
                                        <tr>
                                            <td>
                                                <span class="icone-circulo" style="background-color: {{ $receita->Cor }};">
                                                    <i class="{{ $receita->Link }}"></i>
                                                </span>
                                                {{ $receita->Nome }}
                                            </td>

                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('categorias.edit', ['ID_Categoria' => $receita->ID_Categoria]) }}" class="btn btn-primary btn-sm mr-1" title="Editar"><i class="fa fa-edit"></i></a>
                                                    <form action="{{ route('categorias.destroy', ['ID_Categoria' => $receita->ID_Categoria]) }}" method="POST" class="mr-1">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="button" class="btn btn-danger btn-sm delete-btn" title="Excluir"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                    @if(is_null($receita->ID_Categoria_Pai))
                                                        <form action="{{ route('categorias.new') }}" method="GET">
                                                            <input type="hidden" name="ID_Categoria_Pai" value="{{ $receita->ID_Categoria }}">
                                                            <input type="hidden" name="Tipo" value="R">
                                                            <button type="submit" class="btn btn-success btn-sm" title="Adicionar Subcategoria"><i class="fa fa-plus"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Ações</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#despesasTable').DataTable({
                "paging": false, // Desabilita a paginação
                "searching": false, // Desabilita a pesquisa (filtros)
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "info": false, // Oculta a informação de "Mostrando X de Y registros"
                "ordering": false // Desabilita a ordenação
            });
            $('#receitasTable').DataTable({
                "paging": false , // Desabilita a paginação
                "searching": false, // Desabilita a pesquisa (filtros)
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "info": false, // Oculta a informação de "Mostrando X de Y registros"
            });
            $('.delete-btn').on('click', function () {
                Swal.fire({
                    title: 'Deseja realmente excluir este registro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).closest('form').submit();
                    }
                });
            });
            @if (Session::has('naoapagado'))
            @if (session('naoapagado'))
            Swal.fire({
                icon: 'error',
                title: 'Não é possível apagar a categoria.',
            });
            @endif
            @endif
        });
    </script>
@stop
