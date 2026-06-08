@extends('adminlte::page')

@section('title', 'Cartão - Editar Despesa')

@section('content_header')
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('cartoes.update_despesa',['ID_Despesa' =>  $despesa['ID_Despesa']]) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="ID_Cartao" id="ID_Cartao" value="{{ Session::get('ID_Cartao') }}">
        <div class="card card-warning">
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
                               value="{{ $despesa['Descricao'] }}"
                               list="cartao_despesa_descricoes"
                               autocomplete="off">

                        <datalist id="cartao_despesa_descricoes"></datalist>
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
                            <span class="input-group-text"><i class="fa fa-landmark"></i></span>
                        </div>

                        <input type="number"
                               id="Ano" name="Ano"
                               min="1970" max="2999"
                               class="form-control form-control-sm fatura-ano"
                               value="{{ substr($fatura->Ano_Mes,0,4) }}">

                        <input type="number"
                               id="Mes" name="Mes"
                               min="0" max="13" step="1"
                               class="form-control form-control-sm fatura-mes"
                               value="{{ (int) substr($fatura->Ano_Mes,5,2) }}">
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
    <style>
        /* Inputs do seletor de Fatura (Ano/Mês) - impede o mês de ficar gigante */
        .input-group .fatura-ano {
            flex: 0 0 90px !important;   /* não cresce, largura fixa */
            max-width: 90px !important;
        }

        .input-group .fatura-mes {
            flex: 0 0 70px !important;   /* não cresce, largura fixa */
            max-width: 70px !important;
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputAno = document.getElementById("Ano");
            const inputMes = document.getElementById("Mes");

            // Segurança: se por algum motivo não existir na tela, não faz nada.
            if (!inputAno || !inputMes) return;

            /**
             * Normaliza Ano/Mês para sempre ficar no intervalo 1..12
             * com “vai-um” para o ano:
             * - 2025 + mês 13 => 2026-01
             * - 2026 + mês 0  => 2025-12
             */
            function normalizarAnoMes() {
                let ano = parseInt(inputAno.value, 10);
                let mes = parseInt(inputMes.value, 10);

                // Se o usuário apagar o campo, evita NaN quebrando a tela.
                if (Number.isNaN(ano)) ano = new Date().getFullYear();
                if (Number.isNaN(mes)) mes = new Date().getMonth() + 1;

                // Enquanto mês estiver fora de 1..12, ajusta “carregando” o ano
                while (mes > 12) {
                    mes -= 12;
                    ano += 1;
                }
                while (mes < 1) {
                    mes += 12;
                    ano -= 1;
                }

                inputAno.value = ano;
                inputMes.value = mes; // number input pode exibir "1" ao invés de "01" (normal)
            }

            // Normaliza ao sair do campo e ao mudar (spinner/teclado)
            inputMes.addEventListener("change", normalizarAnoMes);
            inputMes.addEventListener("blur", normalizarAnoMes);
            inputAno.addEventListener("change", normalizarAnoMes);
            inputAno.addEventListener("blur", normalizarAnoMes);

            // Para setas do teclado (↑ ↓) e scroll do mouse no input number
            inputMes.addEventListener("keydown", function (e) {
                if (e.key === "ArrowUp" || e.key === "ArrowDown") {
                    setTimeout(normalizarAnoMes, 0);
                }
            });
            inputMes.addEventListener("wheel", function () {
                setTimeout(normalizarAnoMes, 0);
            });

            // Garante que ao carregar a página já fica consistente
            normalizarAnoMes();
        });
    </script>

    <script>
        $(document).ready(function () {

            /*
             * Endpoint já usado na tela de criação de despesa de cartão.
             */
            const urlDescricoesCartao = "{{ route('cartoes.despesaDescricoes') }}";

            let debounceTimer = null;

            /*
             * Guarda as sugestões retornadas pelo backend.
             *
             * Cada item vem assim:
             *
             * {
             *   descricao: "Netflix",
             *   id_categoria: 5,
             *   categoria: "Assinaturas -> Streaming",
             *   sugestao: "Netflix (Assinaturas -> Streaming)",
             *   total: 3
             * }
             */
            let sugestoesDespesaCartao = [];

            /*
             * Na tela de edição, o cartão NÃO é selecionável.
             * Ele vem em um campo hidden.
             *
             * Este método apenas lê o cartão atual.
             * Não altera nada no formulário.
             */
            function getIdCartaoAtual() {
                const val = $('#ID_Cartao').val();
                return val ? val : '';
            }

            /*
             * Preenche o datalist com o texto exibido ao usuário.
             */
            function preencherDatalist(lista) {
                const datalist = document.getElementById('cartao_despesa_descricoes');

                if (!datalist) {
                    return;
                }

                datalist.innerHTML = '';

                sugestoesDespesaCartao = lista || [];

                sugestoesDespesaCartao.forEach((item) => {
                    const opt = document.createElement('option');

                    /*
                     * Texto que aparecerá no autocomplete:
                     *
                     * Netflix (Assinaturas -> Streaming)
                     */
                    opt.value = item.sugestao;

                    /*
                     * Dados separados para uso interno.
                     */
                    opt.setAttribute('data-descricao', item.descricao);
                    opt.setAttribute('data-id-categoria', item.id_categoria || '');
                    opt.setAttribute('data-categoria', item.categoria || '');

                    datalist.appendChild(opt);
                });
            }

            /*
             * Busca descrições no backend.
             *
             * Enviamos o ID_Cartao apenas para filtrar as sugestões
             * pelo cartão atual da tela.
             */
            function buscarDescricoes(q) {
                $.getJSON(urlDescricoesCartao, {
                    q: q,
                    limit: 15,
                    ID_Cartao: getIdCartaoAtual()
                })
                    .done(function (data) {
                        preencherDatalist(data);
                    })
                    .fail(function () {
                        preencherDatalist([]);
                    });
            }

            /*
             * Aplica a sugestão escolhida.
             *
             * Exemplo exibido:
             *
             * Netflix (Assinaturas -> Streaming)
             *
             * Depois da seleção:
             *
             * Descrição: Netflix
             * Categoria: Assinaturas -> Streaming
             */
            function aplicarSugestaoSelecionada() {
                const valorDigitado = ($('#Descricao').val() || '').trim();

                const item = sugestoesDespesaCartao.find((s) => s.sugestao === valorDigitado);

                if (!item) {
                    return;
                }

                /*
                 * Limpa a descrição, removendo a categoria entre parênteses.
                 */
                $('#Descricao').val(item.descricao);

                /*
                 * Seleciona a categoria correspondente.
                 */
                if (item.id_categoria) {
                    if ($.fn.selectpicker) {
                        $('#Categoria').selectpicker('val', String(item.id_categoria));
                        $('#Categoria').selectpicker('refresh');
                    } else {
                        $('#Categoria').val(item.id_categoria);
                    }

                    $('#Categoria').trigger('change');
                }
            }

            /*
             * Ao digitar no campo descrição.
             */
            $('#Descricao').on('input', function () {
                const q = ($(this).val() || '').trim();

                /*
                 * Se o usuário acabou de selecionar exatamente uma sugestão,
                 * aplicamos imediatamente.
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
             * Alguns navegadores aplicam a opção do datalist no change ou blur.
             */
            $('#Descricao').on('change blur', function () {
                aplicarSugestaoSelecionada();
            });

            /*
             * Ao focar no campo vazio, carrega as descrições mais usadas
             * para o cartão atual.
             */
            $('#Descricao').on('focus', function () {
                const q = ($(this).val() || '').trim();
                const datalist = document.getElementById('cartao_despesa_descricoes');

                if (q.length === 0 && datalist && datalist.children.length === 0) {
                    buscarDescricoes('');
                }
            });

            /*
             * Garantia final antes de salvar.
             */
            $('#cadastro').on('submit', function () {
                aplicarSugestaoSelecionada();
            });

        });
    </script>

@stop
