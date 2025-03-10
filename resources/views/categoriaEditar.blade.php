@extends('adminlte::page')

@section('title', 'Categoria - Editar')

@section('content_header')
    <h1>Editar categoria</h1>
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('categorias.update',['ID_Categoria' =>  $categoria['ID_Categoria']]) }}" method="post" enctype="multipart/form-data">
        <div class="card-body">
            @csrf
            @method('PUT')
            <div class="box-body">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="Nome" class="form-control" id="Nome" placeholder="Digite um nome para Categoria" value="{{ $categoria['Nome'] }}">
                </div>

                <div class="form-group">
                    <label>Cor de marcação da categoria:</label>
                    <div class="input-group my-colorpicker2">
                        <input type="text" class="form-control" id="corCategoria" name="corCategoria" value="{{ $categoria['Cor'] }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-square"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ícone</label>
                    <select name="Icone" id="Icone" class="form-control selectpicker" data-live-search="true">
                        <option value="">Selecione um ícone</option>
                        @foreach($icones as $icone)
                            <option value="{{$icone->Link}}" data-content="{{$icone->Link . ' ' . $icone->Descricao }}"
                                    @if(old('Icone', $categoria->Icone) === $icone->Link) selected @endif>
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-success">Editar</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Redefinir</button>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        .bootstrap-select > .dropdown-toggle,
        .bootstrap-select > .dropdown-menu li a {
            background-color: white;
        }

        .bootstrap-select > .dropdown-toggle {
            border-color: lightgrey !important;
            color: black !important;
        }

        .bootstrap-select > .dropdown-menu li a {
            color: black;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/i18n/defaults-pt_BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bs-custom-file-input/1.3.4/bs-custom-file-input.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/localization/messages_pt_BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.my-colorpicker2').colorpicker();

        $('.my-colorpicker2').on('colorpickerChange', function(event) {
            $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            $('.my-colorpicker2').colorpicker({
                value: '{{ $categoria->Cor }}'
            });

        });
    });
    </script>
    <script>
        $(document).ready(function () {
            $('#cadastro').validate({
                rules: {
                    Nome: {
                        required: true
                    },
                    corCategoria: {
                        required: true
                    },
                    Tipo: {
                        required_if: {
                            depends: function(element) {
                                return $('#Icone').val() !== '';
                            }
                        }
                    }
                },
                messages: {
                    Nome: {
                        required: "Por favor, entre com um nome para a Categoria."
                    },
                    corCategoria: {
                        required: "Por favor, entre com uma cor para a Categoria."
                    },
                    Tipo: {
                        required: "Por favor, selecione um tipo de Categoria"
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
