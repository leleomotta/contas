@extends('adminlte::page')

@section('title', 'Cartão - Editar Despesa')

@section('content_header')

@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('cartoes.update_despesa',['ID_Despesa' =>  $despesa['ID_Despesa']]) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="ID_Cartao" value="{{Session::get('ID_Cartao')}}">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Editar despesa de cartão</h3>
            </div>
            <div class="card-body">
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
                               placeholder="dd/mm/yyyy" value=" {{ date('d/m/Y', strtotime($despesa['Data'])) }}"
                        />
                    </div>
                </div>

                <label >Descrição</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Descricao" class="form-control" id="Descricao"
                           placeholder="Digite uma descrição para a despesa"
                           value="{{ $despesa['Descricao'] }}">
                </div>

                <label >Valor</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                    </div>
                    <input type="text" class="form-control text-left" id="Valor" name="Valor"
                           data-inputmask="'alias': 'numeric',
                           'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',',
                           'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o valor da despesa"
                           value="{{ str_replace("_",'.',
                                            str_replace(".",',',
                                            str_replace(",",'_',
                                            number_format($despesa['Valor'], 2
                                            )))) }}">
                </div>

                <label>Categoria</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-list-alt"></i> </span>
                    </div>
                    <select class="custom-select" id="Categoria" name="Categoria">
                        <option selected data-default>- Selecione uma categoria -</option>
                        @foreach($categorias as $categoria)
                            <option value="{{$categoria->ID_Categoria}}"> {{$categoria->Nome}}  </option>
                        @endforeach
                    </select>
                </div>

                <label>Fatura</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-landmark"></i> </span>
                    </div>


                    <input type="number" id="Ano" name="Ano" min="1970" max="2999" value="{{ substr($fatura->Ano_Mes,0,4) }}">
                    <input type="number" id="Mes" name="Mes" min="1" max="12" value="{{ substr($fatura->Ano_Mes,5,2) }}">

                </div>
            </div>

                <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Redefinir</button>
            </div>
            </div>
        </div>


    </form>

@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    {{-- Tempusdominus Bootstrap 4 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>

@stop

@section('js')
    <script>
        $("#Categoria").val( {{ $despesa['ID_Categoria'] }} );
    </script>
    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>

    <script>
        //Date picker
        $('#Data').datetimepicker({
            format:'DD/MM/YYYY'
        });
    </script>
    <!-- INPUT DATE -->

    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
        $('[data-mask]').inputmask();
        $('input').inputmask();
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/localization/messages_pt_BR.min.js"></script>
    <script>
        $(document).ready(function () {

            $.validator.addMethod("valueNotEquals", function(value, element, arg){
                return arg !== value;
            }, "Value must not equal arg.");

            $('#cadastro').validate({
                rules: {
                    Data:{
                        required: true
                        //date: true
                    },
                    Descricao:{
                        required: true
                    },
                    Valor:{
                        required: true
                    },
                    Categoria: {
                        valueNotEquals: "- Selecione uma categoria -"
                    }

                },
                messages: {
                    Data: {
                        required: "Por favor, entre com uma data para a despesa."
                    },
                    Descricao:{
                        required: "Por favor, entre com uma descrição para a despesa."
                    },
                    Valor:{
                        required: "Por favor, entre com um valor para a despesa."
                    },
                    Categoria: {
                        valueNotEquals: "Por favor, selecione uma categoria."
                    }

                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@stop
