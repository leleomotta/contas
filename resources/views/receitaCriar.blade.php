@extends('adminlte::page')

@section('title', 'Receita - Criar')

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
                               value="{{ old('Data', \Carbon\Carbon::now()->format('d/m/Y')) }}"
                        />
                    </div>
                </div>

                <label>Descrição</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>

                    {{--
                        list="receita_descricoes" conecta o input ao <datalist>.
                        O usuário continua podendo digitar qualquer coisa, mas recebe sugestões.
                    --}}
                    <input
                        type="text"
                        name="Descricao"
                        class="form-control"
                        id="Descricao"
                        list="receita_descricoes"
                        placeholder="Digite uma descrição para a receita"
                        autocomplete="off"
                    >
                </div>

                {{-- Datalist preenchido via AJAX --}}
                <datalist id="receita_descricoes"></datalist>

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
                        <select name="Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true">
                            <option selected data-default>- Selecione uma categoria -</option>
                            @foreach($categorias as $categoria)
                                <option value="{{$categoria->ID_Categoria}}"
                                        data-content='<span class="icone-circulo" style="background-color: {{ $categoria->Cor  }};">
                                <i class="{{ $categoria->Link }}"></i></span> {{ $categoria->Nome }}'
                                >
                                </option>
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
                            <option value="{{$conta->ID_Conta}}"> {{$conta->Banco . ' - ' . $conta->Nome }}  </option>
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
    <script>
    /**
    * URL do endpoint que criamos (rota nomeada).
    * Evita “hardcode” de URL e facilita manutenção.
    */
    const urlDescricoesReceita = "{{ route('receitas.descricoes') }}";

    // “Debounce”: evita bater no servidor a cada tecla digitada
    let debounceReceita = null;

    /**
    * Preenche o <datalist> com segurança usando DOM API.
        * (evita montar HTML na mão e previne injeções acidentais)
        */
        function preencherDatalistReceita(lista) {
        const datalist = document.getElementById('receita_descricoes');
        datalist.innerHTML = '';

        (lista || []).forEach((texto) => {
        const opt = document.createElement('option');
        opt.value = texto;
        datalist.appendChild(opt);
        });
        }

        /**
        * Busca no backend (GET JSON) e atualiza o datalist.
        */
        function buscarDescricoesReceita(q) {
        $.getJSON(urlDescricoesReceita, { q: q, limit: 15 })
        .done(function (data) {
        preencherDatalistReceita(data);
        })
        .fail(function () {
        // Falhou? Só zera as sugestões e segue a vida.
        preencherDatalistReceita([]);
        });
        }

        /**
        * Ao digitar:
        * - com menos de 2 chars, não busca (reduz chamadas)
        * - com 2+, busca no backend
        */
        $('#Descricao').on('input', function () {
        const q = ($(this).val() || '').trim();

        clearTimeout(debounceReceita);

        debounceReceita = setTimeout(function () {
        if (q.length < 2) {
        preencherDatalistReceita([]);
        return;
        }

        buscarDescricoesReceita(q);
        }, 250);
        });

        /**
        * UX opcional:
        * ao focar no campo vazio, carrega as mais comuns (sem termo).
        */
        $('#Descricao').on('focus', function () {
        const q = ($(this).val() || '').trim();
        const datalist = document.getElementById('receita_descricoes');

        if (q.length === 0 && datalist.children.length === 0) {
        buscarDescricoesReceita('');
        }
        });

    </script>

@stop
