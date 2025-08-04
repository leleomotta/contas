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

                    {{-- MENSAGENS DE ERRO --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- AVISO DE PARCELAMENTO --}}
                    @if($despesa['TotalParcelas'] > 1)
                        <div class="alert alert-info">
                            Esta despesa faz parte de um parcelamento ({{ $despesa['Parcela'] }}/{{ $despesa['TotalParcelas'] }}).
                            A edição afetará <strong>apenas esta parcela</strong>.
                        </div>
                    @endif

                    {{-- DATA --}}
                    <label>Data</label>
                    <div class="form-group">
                        <div class="input-group date" id="Data" data-target-input="nearest">
                            <div class="input-group-append" data-target="#Data" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                            <input type="text" class="form-control datetimepicker-input" data-target="#Data" name="Data"
                                   data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask
                                   placeholder="dd/mm/yyyy" value="{{ date('d/m/Y', strtotime($despesa['Data'])) }}" />
                        </div>
                    </div>

                    {{-- DESCRIÇÃO --}}
                    <label>Descrição</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                        </div>
                        <input type="text" name="Descricao" class="form-control" id="Descricao"
                               placeholder="Digite uma descrição para a despesa"
                               value="{{ $despesa['Descricao'] }}">
                    </div>

                    {{-- VALOR --}}
                    <label>Valor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                        </div>
                        <input type="text" class="form-control text-left" id="Valor" name="Valor"
                               data-inputmask="'alias': 'numeric',
                               'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,
                               'radixPoint': ',', 'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o valor da despesa"
                               value="{{ str_replace("_",'.',
                                            str_replace(".",',',
                                            str_replace(",",'_',
                                            number_format($despesa['Valor'], 2)
                                            ))) }}">
                    </div>

                    {{-- CATEGORIA --}}
                    <label>Categoria</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-list-alt"></i> </span>
                        </div>

                        <select name="Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true">
                            <option selected data-default>- Selecione uma categoria -</option>
                            @foreach($categorias as $categoria)
                                <option value="{{$categoria->ID_Categoria}}"
                                        data-content='<span class="icone-circulo" style="background-color: {{ $categoria->Cor  }};"
                                        {{ old('Categoria') == $categoria->ID_Categoria ? 'selected' : '' }}>
                                <i class="{{ $categoria->Link }}"></i></span> {{ $categoria->Nome }}'
                                >
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CAMPOS DE PARCELAMENTO --}}
                    @if($despesa['TotalParcelas'] > 1)
                        <label>Valor total do parcelamento</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calculator"></i></span>
                            </div>
                            <input type="text" class="form-control" value="R$ {{ number_format($despesa['ValorTotal'], 2, ',', '.') }}" readonly>
                        </div>

                        <label>Parcela</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $despesa['Parcela'] }}/{{ $despesa['TotalParcelas'] }}" readonly>
                        </div>
                    @endif

                    {{-- FATURA --}}
                    <label>Fatura</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-landmark"></i> </span>
                        </div>
                        <input type="number" id="Ano" name="Ano" min="1970" max="2999" value="{{ substr($fatura->Ano_Mes,0,4) }}">
                        <input type="number" id="Mes" name="Mes" min="1" max="12" value="{{ substr($fatura->Ano_Mes,5,2) }}">
                    </div>
                </div>

                {{-- BOTÕES --}}
                <div class="card-footer">
                    <div class="float-right">
                        <button type="submit" class="btn btn-success">Salvar alterações</button>
                    </div>
                    <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Redefinir</button>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.2/build/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/i18n/defaults-pt_BR.min.js"></script>
    <script>
        $("#Categoria").val( {{ $despesa['ID_Categoria'] }} );
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script>
        $('#Data').datetimepicker({
            format:'DD/MM/YYYY'
        });
    </script>

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
                    Data:{ required: true },
                    Descricao:{ required: true },
                    Valor:{ required: true },
                    Categoria: {
                        valueNotEquals: "- Selecione uma categoria -"
                    }
                },
                messages: {
                    Data: { required: "Por favor, entre com uma data para a despesa." },
                    Descricao:{ required: "Por favor, entre com uma descrição para a despesa." },
                    Valor:{ required: "Por favor, entre com um valor para a despesa." },
                    Categoria: {
                        valueNotEquals: "Por favor, selecione uma categoria."
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@stop
