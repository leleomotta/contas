@extends('adminlte::page')

@section('title', 'Transferências')

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
                <div class="card-body">
                    <div class="tab-content">

                        <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID_Conta_Origem</th>
                                        <th>ID_Conta_Destino</th>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th style="width: 110px">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                            @foreach($transferencias as $transferencia)
                                            <tr>
                                                <td>{{ $transferencia->ID_Conta_Origem }}</td>
                                                <td>{{ $transferencia->ID_Conta_Destino }}</td>
                                                <td>{{ $transferencia->Data }}</td>
                                                <td>{{ $transferencia->Valor }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>ID_Conta_Origem</th>
                                        <th>ID_Conta_Destino</th>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th style="width: 110px">&nbsp;</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                    </div>

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
