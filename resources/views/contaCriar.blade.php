@extends('adminlte::page')

@section('title', 'Conta - Criar')

@section('content_header')
    <h1>Criar a conta</h1>
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('contas.store') }}" method="post" enctype="multipart/form-data">
        <div class="card-body">

            @csrf
            <div class="box-body">
                <div class="form-group">
                    <label >Nome</label>
                    <input type="text" name="Nome" class="form-control" id="Nome" placeholder="Digite um nome para a conta">
                </div>

                <div class="form-group">
                    <label >Descrição</label>
                    <input type="text" name="Descricao" class="form-control" id="Descricao" placeholder="Digite uma descrição para a conta">
                </div>

                <div class="form-group">
                    <label >Banco</label>
                    <input type="text" name="Banco" class="form-control" id="Banco" placeholder="Digite o nome do banco">
                </div>


                <label >Saldo Inicial</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control text-left" id="Saldo_Inicial" name="Saldo_Inicial"
                           data-inputmask="'alias': 'numeric',
                           'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',',
                           'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o saldo inicial da conta">
                </div>

                <div class="form-group">
                    <label>Cor de marcação da conta:</label>
                    <div class="input-group my-colorpicker2">
                        <input type="text" class="form-control" id="corConta" name="corConta" value="#85AA79">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-square"></i></span>
                        </div>
                    </div>
                </div>

                <label>Imagem da conta</label>
                <div class="input-group" >
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="imagem" id="imagem">
                        <label class="custom-file-label">Selecione uma imagem para esta conta</label>
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
    </form>

@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    {{-- Bootstrap Color Picker --}}
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

    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
        $('input').inputmask();
    </script>
@stop
