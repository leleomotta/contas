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

                    <!-- Listagem-->
                    <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mãe</th>
                            <th>Nome</th>
                            <th>Cor</th>
                            <th>Tipo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->ID_Categoria }}</td>
                                <td>Mãe</td>
                                <td>{{ $categoria->Nome }}</td>
                                <td>{{ $categoria->Cor }}</td>
                                <td>{{ $categoria->Tipo }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Mãe</th>
                            <th>Nome</th>
                            <th>Cor</th>
                            <th>Tipo</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

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
