@extends('adminlte::page')

@section('title', 'Transferência - Editar')

@section('content_header')
    <h1>Editar transferência</h1>
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('transferencias.update',['ID_Transferencia' =>  $transferencia['ID_Transferencia']]) }}" method="post" enctype="multipart/form-data">
        <div class="card-body">

            @csrf
            @method('PUT')
            <div class="box-body">
                <!-- Date -->
                <label>Data</label>
                <div class="form-group">
                    <div class="input-group date" id="Data" data-target-input="nearest">
                        <div class="input-group-append" data-target="#Data" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                        <input type="text" class="form-control datetimepicker-input" data-target="#Data" name="Data"
                               data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                               placeholder="dd/mm/yyyy" value=" {{ date('d/m/Y', strtotime($transferencia['Data'])) }}"
                        />
                    </div>
                </div>

                <label >Valor</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control text-left" id="Valor" name="Valor"
                           data-inputmask="'alias': 'numeric',
                           'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',',
                           'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o valor da transferência"
                           value="{{ str_replace("_",'.',
                                                    str_replace(".",',',
                                                    str_replace(",",'_',
                                                    number_format($transferencia['Valor'], 2
                                                    )))) }}">>
                </div>

                <label>Conta Origem</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-landmark"></i> </span>
                    </div>
                    <select class="custom-select" id="Conta_Origem" name="Conta_Origem">
                        <option selected data-default>- Selecione uma conta -</option>
                        @foreach($contas as $conta)
                            <option value="{{$conta->ID_Conta}}"> {{$conta->Nome . ' - ' . $conta->Banco }}  </option>
                        @endforeach
                    </select>
                </div>

                <label>Conta Destino</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-landmark"></i> </span>
                    </div>
                    <select class="custom-select" id="Conta_Destino" name="Conta_Destino">
                        <option selected data-default>- Selecione uma conta -</option>
                        @foreach($contas as $conta)
                            <option value="{{$conta->ID_Conta}}"> {{$conta->Nome . ' - ' . $conta->Banco }}  </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Redefinir</button>
            </div>
        </div>
    </form>

@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

@stop

@section('js')
    <script>
        $("#Conta_Origem").val( {{ $transferencia['ID_Conta_Origem'] }} );
        $("#Conta_Destino").val( {{ $transferencia['ID_Conta_Destino'] }} );
    </script>


    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
        $('input').inputmask();
    </script>

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
        $('[data-mask]').inputmask();
        $('input').inputmask();
        $('#Data').datetimepicker({
            format:'DD/MM/YYYY'
        });

    </script>
@stop
