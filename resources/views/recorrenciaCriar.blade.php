@extends('adminlte::page')

@section('title', 'Recorrência - Criar')

@section('content_header')
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('recorrencias.store') }}" method="post">
        @csrf
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Criar despesa recorrente</h3>
            </div>
            <div class="card-body">

                <label>Descrição</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Descricao" class="form-control" id="Descricao" placeholder="Digite uma descrição">
                </div>

                <label>Valor</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                    </div>
                    <input type="text" class="form-control text-left" id="Valor" name="Valor"
                           data-inputmask="'alias': 'numeric', 'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',', 'prefix': 'R$ ', 'placeholder': '0'"
                           placeholder="Digite o valor da despesa">
                </div>

                <label>Categoria</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-list-alt"></i></span>
                    </div>
                    <select name="ID_Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true">
                        <option selected data-default>- Selecione uma categoria -</option>
                        @foreach($categorias as $categoria)
                            <option value="{{$categoria->ID_Categoria}}"
                                    data-content="<span class='icone-circulo' style='background-color: {{ $categoria->Cor }};'><i class='{{ $categoria->Link }}'></i></span> {{ $categoria->Nome }}">
                                {{ $categoria->Nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label>Tipo</label>
                <div class="form-group">
                    <select class="form-control" id="TipoPagamento" name="TipoPagamento">
                        <option selected value="">- Selecione -</option>
                        <option value="conta">Conta</option>
                        <option value="cartao">Cartão</option>
                    </select>
                </div>

                <div id="divConta" style="display:none">
                    <label>Conta</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-landmark"></i> </span>
                        </div>
                        <select class="form-control selectpicker" data-live-search="true" id="Conta" name="ID_Conta">
                            <option selected>- Selecione uma conta -</option>
                            @foreach($contas as $conta)
                                <option value="{{$conta->ID_Conta}}">{{$conta->Nome}} - {{$conta->Banco}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="divCartao" style="display:none">
                    <label>Cartão</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                        </div>
                        <select class="form-control selectpicker" data-live-search="true" id="Cartao" name="ID_Cartao">
                            <option selected>- Selecione um cartão -</option>
                            @foreach($cartoes as $cartao)
                                <option value="{{$cartao->ID_Cartao}}">{{$cartao->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label>Dia do vencimento</label>
                <input type="number" class="form-control" name="DiaVencimento" min="1" max="31">

                <label>Periodicidade</label>
                <div class="form-group">
                    <select class="form-control" name="Periodicidade">
                        <option value="mensal" selected>Mensal</option>
                        <option value="anual">Anual</option>
                        <option value="semanal">Semanal</option>
                    </select>
                </div>

                <label>Data de início</label>
                <div class="input-group date" id="DataInicio" data-target-input="nearest">
                    <div class="input-group-append" data-target="#DataInicio" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    <input type="text" class="form-control datetimepicker-input" data-target="#DataInicio" name="DataInicio"
                           data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                           placeholder="dd/mm/yyyy" value="{{ old('DataInicio') }}"/>
                </div>

                <label class="mt-3">Data de fim (opcional)</label>
                <div class="input-group date" id="DataFim" data-target-input="nearest">
                    <div class="input-group-append" data-target="#DataFim" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar-times"></i></div>
                    </div>
                    <input type="text" class="form-control datetimepicker-input" data-target="#DataFim" name="DataFim"
                           data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                           placeholder="dd/mm/yyyy" value="{{ old('DataFim') }}"/>
                </div>

                <div class="form-group mt-3">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="Ativa" name="Ativa" checked>
                        <label class="custom-control-label" for="Ativa">Ativa</label>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        .bootstrap-select > .dropdown-toggle,
        .bootstrap-select > .dropdown-menu li a,
        .bootstrap-select > .dropdown-toggle:focus,
        .bootstrap-select > .dropdown-toggle:hover {
            background-color: white;
        }
        .bootstrap-select > .dropdown-toggle {
            border-color: lightgrey !important;
            background-color: white !important;
            color: black !important;
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
            border-radius: 50% !important;
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

    <!-- INPUT DATE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>

    <script>
        //Date picker
        $('#DataInicio').datetimepicker({
            format:'DD/MM/YYYY'
        });
        $('#DataFim').datetimepicker({ format: 'DD/MM/YYYY' });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>

        $('[data-mask]').inputmask();
        $('#Valor').inputmask();
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/localization/messages_pt_BR.min.js"></script>
    <script>
        $('.selectpicker').selectpicker();

        $('#TipoPagamento').change(function () {
            $('#divConta').hide();
            $('#divCartao').hide();

            if ($(this).val() === 'conta') {
                $('#divConta').show();
            } else if ($(this).val() === 'cartao') {
                $('#divCartao').show();
            }
        });

        $('#cadastro').validate({
            rules: {
                Descricao: { required: true },
                Valor: { required: true },
                ID_Categoria: { required: true },
                TipoPagamento: { required: true },
                ID_Conta: {
                    required: function () {
                        return $('#TipoPagamento').val() === 'conta';
                    }
                },
                ID_Cartao: {
                    required: function () {
                        return $('#TipoPagamento').val() === 'cartao';
                    }
                },
                DiaVencimento: { required: true, min: 1, max: 31 },
                DataInicio: { required: true }
            },
            messages: {
                Descricao: { required: 'Por favor, informe uma descrição.' },
                Valor: { required: 'Por favor, informe o valor.' },
                ID_Categoria: { required: 'Por favor, selecione uma categoria.' },
                TipoPagamento: { required: 'Por favor, selecione Conta ou Cartão.' },
                ID_Conta: { required: 'Selecione uma conta.' },
                ID_Cartao: { required: 'Selecione um cartão.' },
                DiaVencimento: { required: 'Informe o dia do vencimento.', min: 'Dia inválido', max: 'Dia inválido' },
                DataInicio: { required: 'Informe a data de início.' }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group, .input-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    </script>
@stop
