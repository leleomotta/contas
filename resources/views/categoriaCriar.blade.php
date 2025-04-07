@extends('adminlte::page')

@section('title', 'Categoria - Criar')

@section('content_header')
    <h1>Criar a categoria</h1>
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('categorias.store') }}" method="post" enctype="multipart/form-data">
        <div class="card-body">

        @csrf
        <div class="box-body">
            <div class="form-group">
                <label >Nome</label>
                <input type="text" name="Nome" class="form-control" id="Nome" placeholder="Digite um nome para Categoria">
            </div>

            <div class="form-group">
                <label>Cor de marcação da categoria:</label>
                <div class="input-group my-colorpicker2">
                    <input type="text" class="form-control" id="corCategoria" name="corCategoria" value="#FDFDFD">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-square"></i></span>
                    </div>
                </div>
            </div>

            <label>Tipo de categoria</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-list"></i> </span>
                </div>
                <select class="custom-select" id="TipoCat" name="TipoCat">
                    <option selected data-default>- Selecione um tipo de categoria -</option>
                        <option value="D" style="background-color: blue"> Despesa  </option>
                    <option value="R"> Receita  </option>
                </select>
            </div>

            <label>Ícone</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-list"></i> </span>
                </div>
                <select name="Icone" id="Icone" class="form-control selectpicker" data-live-search="true">
                    <option value="">Selecione um ícone</option>
                    @foreach($icones as $icone)
                        <option value="{{ $icone->ID_Icone }}"
                                data-content='<span class="icone-circulo" style="background-color: {{ '#C8C8C8'}};">
                                <i class="{{ $icone->Link }}"></i></span> {{ $icone->Descricao }}'
                        >
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" id="ID_Categoria_Pai" name="ID_Categoria_Pai" value="{{ $categoriaPai }}">
            <input type="hidden" id="TipoCat2" name="TipoCat2" value="">
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
    {{-- Bootstrap Color Picker --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.css">
    {{-- Latest compiled and minified CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        .bootstrap-select > .dropdown-toggle, /* dropdown box */
        .bootstrap-select > .dropdown-menu li a, /* all dropdown options */
        .bootstrap-select > .dropdown-toggle:focus, /* dropdown :focus */
        .bootstrap-select > .dropdown-toggle:hover /* dropdown :hover */
        {
            background-color: white;
        }
        .bootstrap-select > .dropdown-toggle {
            border-color: lightgrey !important;
            background-color: white !important;
            color: black !important; /* Adiciona !important */
        }
        .bootstrap-select > .dropdown-menu li a {
            color: black;
        }

         .icone-circulo {
             display: inline-flex;
             align-items: center;
             justify-content: center;
             width: 30px;
             height: 30px;
             border-radius: 50% !important; /* garante que fique redondo */
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
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/i18n/defaults-pt_BR.min.js"></script>



        <!-- bs-custom-file-input -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bs-custom-file-input/1.3.4/bs-custom-file-input.min.js"></script>
    <!-- bootstrap color picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            //console.log(  document.getElementById("corAltCorreta").value  );
            $('.my-colorpicker2 .fa-square').css('color', document.getElementById("corCategoria").value);
        });

        $(function () {
            //color picker with addon
            $('.my-colorpicker2').colorpicker();

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
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
                    Nome:{
                        required: true
                        //date: true
                    },
                    corCategoria:{
                        required: true
                    },
                    Tipo:{
                        valueNotEquals: "- Selecione um tipo de categoria -"
                    }

                },
                messages: {
                    Nome: {
                        required: "Por favor, entre com um nome para a Categoria."
                    },
                    corCategoria:{
                        required: "Por favor, entre com uma cor para a Categoria."
                    },
                    Tipo:{
                        valueNotEquals: "Por favor, selecione um tipo de Categoria"
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

    <script>
        if('{{ $TipoCategoria }}' != ''){
            document.getElementById("TipoCat").disabled = true;
            $("#TipoCat").val('{{ $TipoCategoria }}');
            $("#TipoCat2").val("{{ $TipoCategoria }}");
        }
    </script>

@stop
