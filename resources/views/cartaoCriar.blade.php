@extends('adminlte::page')

@section('title', 'Criar Cartão')

@section('content_header')

@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('cartoes.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Criar cartão</h3>
            </div>
            <div class="card-body">
                <div class="box-body">
                <!-- Date -->

                <label>Nome</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Nome" class="form-control" id="Nome" placeholder="Digite um nome para o cartão">
                </div>

                <label>Bandeira</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Bandeira" class="form-control" id="Bandeira" placeholder="Digite uma bandeira para o cartão">
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
                    <label>Cor de marcação do cartão:</label>
                    <div class="input-group my-colorpicker2">
                        <input type="text" class="form-control" id="corCartao" name="corCartao" value="#85AA79">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-square"></i></span>
                        </div>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.css">
@stop

@section('js')

    <!-- bs-custom-file-input -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bs-custom-file-input/1.3.4/bs-custom-file-input.min.js"></script>
    <!-- bootstrap color picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();
        });

        $(document).ready(function () {
            //console.log(  document.getElementById("corAltCorreta").value  );
            $('.my-colorpicker2 .fa-square').css('color', document.getElementById("corConta").value);
        });

        $(function () {
            //color picker with addon
            $('.my-colorpicker2').colorpicker();

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            });


        });
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
                    Nome:{
                        required: true
                        //date: true
                    },
                    Bandeira:{
                        required: true
                    },
                    Conta:{
                        valueNotEquals: "- Selecione uma conta -"
                    },
                    Cor:{
                        required: true
                    }
                },
                messages: {
                    Nome: {
                        required: "Por favor, entre com um nome para o cartão."
                    },
                    Bandeira:{
                        required: "Por favor, entre com uma bandeira para o cartão."
                    },
                    Conta:{
                        valueNotEquals: "Por favor, selecione uma conta"
                    },
                    Cor:{
                        required: "Por favor, escolha uma cor para o cartão."
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
