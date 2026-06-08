@extends('adminlte::page')

@section('title', 'Despesa - Criar')

@section('content_header')

@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('despesas.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Criar despesa</h3>
            </div>
            <div class="card-body">
                <div class="box-body">
                    <!-- Data -->
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
                            list="despesa_descricoes" conecta este input ao <datalist> abaixo.
                            Isso faz o browser sugerir opções enquanto o usuário digita,
                            mas ainda permite digitar um valor totalmente novo.
                        --}}
                        <input
                            type="text"
                            name="Descricao"
                            class="form-control"
                            id="Descricao"
                            list="despesa_descricoes"
                            placeholder="Digite uma descrição para a despesa"
                            autocomplete="off"
                        >
                    </div>

                    {{-- Datalist que será preenchido via AJAX --}}
                    <datalist id="despesa_descricoes"></datalist>


                    <label >Valor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                        </div>
                        <input type="text" class="form-control text-left" id="Valor" name="Valor"
                               data-inputmask="'alias': 'numeric',
                           'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',',
                           'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o valor da despesa">
                    </div>

                    <!-- Campos para parcelamento -->
                    <div class="form-row">
                        <div class="col-md-6">
                            <label>Despesa parcelada?</label>
                            <select name="Parcelada" id="Parcelada" class="form-control">
                                <option value="nao">Não</option>
                                <option value="sim">Sim</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="divNumeroParcelas">
                            <label>Número de parcelas</label>
                            <input type="number" name="NumeroParcelas" id="NumeroParcelas" class="form-control"
                                   min="1" max="60" value="1">
                        </div>
                    </div>

                    <label class="mt-3">Categoria</label>
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
                                <option value="{{$conta->ID_Conta}}"> {{$conta->Banco . ' - ' . $conta->Nome }}
                                </option>
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
            format:'DD/MM/YYYY'
        });
    </script>
    <!-- INPUT DATE -->

    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
    <script>
        $('[data-mask]').inputmask();
        $('input').inputmask();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_pt_BR.min.js"></script>
    <script>
        $(document).ready(function () {
            // Lógica para desabilitar/habilitar o campo de parcelas
            const selectParcelada = $('#Parcelada');
            const inputNumeroParcelas = $('#NumeroParcelas');

            function toggleParcelas() {
                if (selectParcelada.val() === 'sim') {
                    inputNumeroParcelas.prop('disabled', false);
                } else {
                    inputNumeroParcelas.val(1);
                    inputNumeroParcelas.prop('disabled', true);
                }
            }

            toggleParcelas();
            selectParcelada.on('change', toggleParcelas);

            // Remova a definição do método valueNotEquals

            $('#cadastro').validate({
                rules: {
                    Data:{
                        required: true
                    },
                    Descricao:{
                        required: true
                    },
                    Valor:{
                        required: true
                    },
                    Parcelada: {
                        required: true
                    },
                    NumeroParcelas: {
                        required: function(element) {
                            return $("#Parcelada").val() === "sim";
                        },
                        min: 1
                    },
                    // Use a regra 'required' simples para os campos select
                    Categoria: {
                        required: true
                    },
                    Conta:{
                        required: true
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
                    NumeroParcelas: {
                        required: "Por favor, informe o número de parcelas.",
                        min: "O número de parcelas deve ser pelo menos 1."
                    },
                    // Remova as mensagens personalizadas para Categoria e Conta
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    // Lógica para o campo Categoria (bootstrap-select)
                    if (element.attr("name") === "Categoria") {
                        // Posiciona o erro após o container do selectpicker
                        error.insertAfter(element.next('.bootstrap-select'));
                    } else {
                        element.closest('.form-group, .input-group, .form-row').append(error);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                    // Adiciona o destaque de erro ao container do selectpicker
                    if ($(element).attr("name") === "Categoria" || $(element).attr("name") === "Conta") {
                        $(element).next('.bootstrap-select').find('.dropdown-toggle').addClass('is-invalid');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                    // Remove o destaque de erro do container do selectpicker
                    if ($(element).attr("name") === "Categoria" || $(element).attr("name") === "Conta") {
                        $(element).next('.bootstrap-select').find('.dropdown-toggle').removeClass('is-invalid');
                    }
                }
            });

            // Disparar a validação do campo Categoria quando o valor do bootstrap-select mudar
            $('#Categoria, #Conta').on('change.bs.select', function() {
                $(this).valid();
            });
        });
    </script>

    <script>
        $(document).ready(function () {

            const urlDescricoes = "{{ route('despesas.descricoes') }}";

            let debounceTimer = null;

            /*
             * Aqui guardamos as sugestões retornadas pelo backend.
             * Antes o backend retornava apenas textos.
             * Agora retorna objetos com descrição, categoria e ID da categoria.
             */
            let sugestoesDespesa = [];

            /*
             * O backend agora retorna objetos assim:
             *
             * {
             *   descricao: "Mercado BH",
             *   id_categoria: 5,
             *   categoria: "Alimentação -> Supermercado",
             *   sugestao: "Mercado BH (Alimentação -> Supermercado)",
             *   total: 3
             * }
             */
            function preencherDatalist(lista) {
                const datalist = document.getElementById('despesa_descricoes');
                datalist.innerHTML = '';

                sugestoesDespesa = lista || [];

                sugestoesDespesa.forEach((item) => {
                    const opt = document.createElement('option');

                    /*
                     * Este é o texto que aparece na lista do autocomplete.
                     * Por isso usamos a versão com a categoria entre parênteses.
                     */
                    opt.value = item.sugestao;

                    /*
                     * Guardamos os dados separados no option.
                     * Isso não é obrigatório para exibir, mas ajuda a manter os dados limpos.
                     */
                    opt.setAttribute('data-descricao', item.descricao);
                    opt.setAttribute('data-id-categoria', item.id_categoria || '');
                    opt.setAttribute('data-categoria', item.categoria || '');

                    datalist.appendChild(opt);
                });
            }

            function buscarDescricoes(q) {
                $.getJSON(urlDescricoes, { q: q, limit: 15 })
                    .done(function (data) {
                        preencherDatalist(data);
                    })
                    .fail(function () {
                        preencherDatalist([]);
                    });
            }

            /*
             * Quando o usuário escolhe uma sugestão da lista:
             *
             * Exemplo exibido:
             * Mercado BH (Alimentação -> Supermercado)
             *
             * O que ficará salvo no campo Descrição:
             * Mercado BH
             *
             * E a categoria será selecionada automaticamente.
             */
            function aplicarSugestaoSelecionada() {
                const valorDigitado = ($('#Descricao').val() || '').trim();

                const item = sugestoesDespesa.find((s) => s.sugestao === valorDigitado);

                if (!item) {
                    return;
                }

                /*
                 * Remove a categoria do texto da descrição.
                 */
                $('#Descricao').val(item.descricao);

                /*
                 * Seleciona automaticamente a categoria correspondente.
                 */
                if (item.id_categoria) {
                    $('#Categoria').val(item.id_categoria);

                    /*
                     * Como o campo Categoria usa bootstrap-select,
                     * precisamos atualizar visualmente o select.
                     */
                    if ($.fn.selectpicker) {
                        $('#Categoria').selectpicker('refresh');
                    }

                    /*
                     * Dispara o evento change para validações e regras já existentes.
                     */
                    $('#Categoria').trigger('change');
                }
            }

            $('#Descricao').on('input', function () {
                const q = ($(this).val() || '').trim();

                /*
                 * Se o usuário selecionou uma sugestão exatamente igual à lista,
                 * aplica imediatamente.
                 */
                aplicarSugestaoSelecionada();

                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    const textoAtual = ($('#Descricao').val() || '').trim();

                    if (textoAtual.length < 2) {
                        preencherDatalist([]);
                        return;
                    }

                    buscarDescricoes(textoAtual);
                }, 250);
            });

            /*
             * Alguns navegadores aplicam a seleção do datalist no change ou no blur,
             * então mantemos esta chamada também.
             */
            $('#Descricao').on('change blur', function () {
                aplicarSugestaoSelecionada();
            });

            /*
             * Quando o campo recebe foco vazio, carrega as descrições mais usadas.
             */
            $('#Descricao').on('focus', function () {
                const q = ($(this).val() || '').trim();
                const datalist = document.getElementById('despesa_descricoes');

                if (q.length === 0 && datalist.children.length === 0) {
                    buscarDescricoes('');
                }
            });

            /*
             * Garantia final:
             * se por algum motivo o usuário submeter com
             * "Descrição (Categoria)" no campo, limpamos antes de enviar.
             */
            $('#cadastro').on('submit', function () {
                aplicarSugestaoSelecionada();
            });

        });
    </script>

@stop
