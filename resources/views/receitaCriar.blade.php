@extends('adminlte::page')

@section('title', 'Criar Receita')

@section('content_header')

@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('receitas.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Criar receita</h3>
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
                               placeholder="dd/mm/yyyy"
                        />
                    </div>
                </div>

                <label >Descrição</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Descricao" class="form-control" id="Descricao" placeholder="Digite uma descrição para a receita">
                </div>

                <label >Valor</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                    </div>
                    <input type="text" class="form-control text-left" id="Valor" name="Valor"
                           data-inputmask="'alias': 'numeric',
                           'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',',
                           'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o valor da receita">
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

                <label>Conta</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-landmark"></i> </span>
                    </div>
                    <select class="custom-select" id="Conta" name="Conta">
                        <option selected data-default>- Selecione uma conta -</option>
                        @foreach($contas as $conta)
                            <option value="{{$conta->ID_Conta}}"> {{$conta->Banco . ' - ' . $conta->Descricao }}  </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="Efetivada" name="Efetivada">
                        <label class="custom-control-label" for="Efetivada">Efetivada</label>
                    </div>
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
    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>

    <script>
        //Date picker
        $('#Data').datetimepicker({
            //format:'DD/MM/YYYY',
            format:'DD/MM/YYYY',
            language: 'pt-BR',

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
                    },
                    Conta:{
                        valueNotEquals: "- Selecione uma conta -"
                    }

                },
                messages: {
                    Data: {
                        required: "Por favor, entre com uma data para a receita."
                    },
                    Descricao:{
                        required: "Por favor, entre com uma descrição para a receita."
                    },
                    Valor:{
                        required: "Por favor, entre com um valor para a receita."
                    },
                    Categoria: {
                        valueNotEquals: "Por favor, selecione uma categoria."
                    },
                    Conta:{
                        valueNotEquals: "Por favor, selecione uma conta"
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
