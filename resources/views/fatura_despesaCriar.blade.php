@extends('adminlte::page')

@section('title', 'Cartão - Criar Despesa')

@section('content_header')
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="cadastro" role="form" action="{{ route('cartoes.store_despesa') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ID_Cartao" value="{{ Session::get('ID_Cartao') }}">

        <div class="card card-success">
            <div class="card-header py-2">
                <h3 class="card-title mb-0">Criar despesa de cartão</h3>
            </div>

            <div class="card-body py-2">
                <div class="form-group mb-2">
                    <label>Data</label>
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

                <div class="form-group mb-2">
                    <label>Descrição</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                        </div>

                        {{--
                            list="cartao_despesa_descricoes" conecta o input ao datalist.
                            O usuário pode:
                            - escolher uma sugestão existente, OU
                            - digitar uma descrição nova normalmente.
                        --}}
                        <input
                            type="text"
                            name="Descricao"
                            class="form-control"
                            id="Descricao"
                            list="cartao_despesa_descricoes"
                            value="{{ old('Descricao') }}"
                            placeholder="Digite uma descrição para a despesa"
                            autocomplete="off"
                        >
                    </div>

                    {{-- Datalist preenchido via AJAX --}}
                    <datalist id="cartao_despesa_descricoes"></datalist>

                </div>

                <div class="form-group mb-2">
                    <label>Valor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                        </div>
                        <input type="text" class="form-control text-left" id="Valor" name="Valor"
                               data-inputmask="'alias': 'numeric',
                            'groupSeparator': '.', 'autoGroup': true, 'digits': 2, 'digitsOptional': false,'radixPoint': ',',
                            'prefix': 'R$ ', 'placeholder': '0'" placeholder="Digite o valor da despesa"
                               value="{{ old('Valor') }}">
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <label>Despesa parcelada?</label>
                        <select name="Parcelada" id="Parcelada" class="form-control">
                            <option value="nao" {{ old('Parcelada') == 'nao' ? 'selected' : '' }}>Não</option>
                            <option value="sim" {{ old('Parcelada') == 'sim' ? 'selected' : '' }}>Sim</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Número de parcelas</label>
                        <input type="number" name="NumeroParcelas" id="NumeroParcelas" class="form-control"
                               min="1" max="60" value="{{ old('NumeroParcelas', 1) }}">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label>Categoria</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-list-alt"></i></span>
                        </div>
                        <select name="Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true" required>
                            <option value="" selected>- Selecione uma categoria -</option>
                            @foreach($categorias as $categoria)
                                <option value="{{$categoria->ID_Categoria}}"
                                        {{ old('Categoria') == $categoria->ID_Categoria ? 'selected' : '' }}
                                        data-content='<span class="icone-circulo" style="background-color: {{ $categoria->Cor  }};">
                                        <i class="{{ $categoria->Link }}"></i></span> {{ $categoria->Nome }}'>
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-group mb-2">
                    <label>Cartão</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                        </div>
                        <select name="ID_Cartao" id="ID_Cartao" class="form-control selectpicker" data-live-search="true" required>
                            <option disabled selected>- Selecione um cartão -</option>
                            @foreach($cartoes as $cartao)

                                <option
                                    value="{{ $cartao->ID_Cartao }}"

                                    {{-- ===================================================== --}}
                                    {{-- FATURA ABERTA DESTE CARTÃO                            --}}
                                    {{--                                                       --}}
                                    {{-- Esses dois atributos serão lidos pelo JavaScript      --}}
                                    {{-- quando o cartão for selecionado.                      --}}
                                    {{-- ===================================================== --}}
                                    data-ano-fatura="{{ $cartao->Ano_Fatura_Aberta }}"
                                    data-mes-fatura="{{ $cartao->Mes_Fatura_Aberta }}"

                                    {{ old('ID_Cartao', Session::get('ID_Cartao')) == $cartao->ID_Cartao ? 'selected' : '' }}
                                >

                                    {{ $cartao->ID_Cartao }}
                                    -
                                    {{ $cartao->Nome }}
                                    ({{ $cartao->Bandeira }})

                                </option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label>Fatura</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-landmark"></i></span>
                        </div>

                        {{-- ANO: largura fixa para não esticar no input-group --}}
                        <input type="number"
                               id="Ano" name="Ano"
                               min="1900" max="2500"
                               class="form-control form-control-sm fatura-ano"
                               {{-- Ano da fatura aberta calculada pelo controller --}}
                               value="{{ old('Ano', $anoFaturaPadrao) }}">

                        {{-- MÊS: permitimos 0 e 13 para o spinner “passar do limite” e o JS normalizar --}}
                        <input type="number"
                               id="Mes" name="Mes"
                               min="0" max="13" step="1"
                               class="form-control form-control-sm fatura-mes"
                               {{-- Mês da fatura aberta calculada pelo controller --}}
                               value="{{ old('Mes', $mesFaturaPadrao) }}">
                    </div>

                </div>

                <div class="card-footer pt-2 pb-1">
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const selectParcelada = document.getElementById("Parcelada");
            const inputParcelas = document.getElementById("NumeroParcelas");

            function toggleParcelas() {
                inputParcelas.disabled = (selectParcelada.value === "nao");
            }

            toggleParcelas();
            selectParcelada.addEventListener("change", toggleParcelas);
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
             * Endpoint do autocomplete de despesas de cartão.
             */
            const urlDescricoesCartao = "{{ route('cartoes.despesaDescricoes') }}";

            /*
             * Debounce para evitar muitas requisições enquanto o usuário digita.
             */
            let debounceTimer = null;

            /*
             * Aqui guardamos as sugestões retornadas pelo backend.
             *
             * Agora o backend retorna objetos assim:
             *
             * {
             *   descricao: "Uber",
             *   id_categoria: 5,
             *   categoria: "Transporte -> Aplicativo",
             *   sugestao: "Uber (Transporte -> Aplicativo)",
             *   total: 3
             * }
             */
            let sugestoesDespesaCartao = [];

            /*
             * Pega o ID do cartão selecionado.
             *
             * No seu formulário o select do cartão já está assim:
             *
             * <select name="ID_Cartao" id="ID_Cartao" ...>
             */
            function getIdCartaoSelecionado() {
                const val = $('#ID_Cartao').val();
                return val ? val : '';
            }

            /*
             * Preenche o datalist com as sugestões.
             *
             * O usuário verá:
             *
             * Uber (Transporte -> Aplicativo)
             *
             * Mas o sistema ainda guarda separadamente:
             * - descrição limpa;
             * - ID da categoria;
             * - nome da categoria.
             */
            function preencherDatalist(lista) {
                const datalist = document.getElementById('cartao_despesa_descricoes');
                datalist.innerHTML = '';

                sugestoesDespesaCartao = lista || [];

                sugestoesDespesaCartao.forEach((item) => {
                    const opt = document.createElement('option');

                    /*
                     * Este é o texto que aparece no autocomplete.
                     */
                    opt.value = item.sugestao;

                    /*
                     * Dados extras guardados no option.
                     */
                    opt.setAttribute('data-descricao', item.descricao);
                    opt.setAttribute('data-id-categoria', item.id_categoria || '');
                    opt.setAttribute('data-categoria', item.categoria || '');

                    datalist.appendChild(opt);
                });
            }

            /*
             * Faz a busca no backend.
             *
             * Enviamos também o ID_Cartao para que as sugestões sejam
             * mais relevantes ao cartão selecionado.
             */
            function buscarDescricoes(q) {
                $.getJSON(urlDescricoesCartao, {
                    q: q,
                    limit: 15,
                    ID_Cartao: getIdCartaoSelecionado()
                })
                    .done(function (data) {
                        preencherDatalist(data);
                    })
                    .fail(function () {
                        preencherDatalist([]);
                    });
            }

            /*
             * Aplica uma sugestão selecionada pelo usuário.
             *
             * Exemplo exibido:
             *
             * Uber (Transporte -> Aplicativo)
             *
             * Depois de selecionar, o campo Descrição fica apenas:
             *
             * Uber
             *
             * E o campo Categoria é preenchido automaticamente.
             */
            function aplicarSugestaoSelecionada() {
                const valorDigitado = ($('#Descricao').val() || '').trim();

                const item = sugestoesDespesaCartao.find((s) => s.sugestao === valorDigitado);

                if (!item) {
                    return;
                }

                /*
                 * Limpa o campo descrição, removendo a categoria entre parênteses.
                 */
                $('#Descricao').val(item.descricao);

                /*
                 * Seleciona a categoria correspondente.
                 */
                if (item.id_categoria) {

                    /*
                     * Como o campo Categoria usa bootstrap-select,
                     * o ideal é usar selectpicker('val').
                     */
                    if ($.fn.selectpicker) {
                        $('#Categoria').selectpicker('val', String(item.id_categoria));
                        $('#Categoria').selectpicker('refresh');
                    } else {
                        $('#Categoria').val(item.id_categoria);
                    }

                    /*
                     * Dispara change para manter compatibilidade com validações
                     * e outras regras que você possa ter no formulário.
                     */
                    $('#Categoria').trigger('change');
                }
            }

            /*
             * Ao digitar no campo descrição.
             */
            $('#Descricao').on('input', function () {
                const q = ($(this).val() || '').trim();

                /*
                 * Se o usuário escolheu exatamente uma opção do datalist,
                 * aplicamos a sugestão imediatamente.
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
             * por isso mantemos também esses eventos.
             */
            $('#Descricao').on('change blur', function () {
                aplicarSugestaoSelecionada();
            });

            /*
             * Quando o campo recebe foco vazio, carrega as descrições mais usadas.
             */
            $('#Descricao').on('focus', function () {
                const q = ($(this).val() || '').trim();
                const datalist = document.getElementById('cartao_despesa_descricoes');

                if (q.length === 0 && datalist.children.length === 0) {
                    buscarDescricoes('');
                }
            });

            /*
             * Se o usuário trocar o cartão, limpamos as sugestões,
             * pois as sugestões mais relevantes podem mudar.
             */
            $('#ID_Cartao').on('change changed.bs.select', function () {
                preencherDatalist([]);

                const q = ($('#Descricao').val() || '').trim();

                if (q.length >= 2) {
                    buscarDescricoes(q);
                }
            });

            /*
             * Garantia final:
             * se o usuário submeter logo após escolher uma sugestão,
             * limpamos a descrição e selecionamos a categoria antes do envio.
             */
            $('#cadastro').on('submit', function () {
                aplicarSugestaoSelecionada();
            });

        });
    </script>

    <script>
        $(document).ready(function () {

            /*
             * =============================================================
             * PREENCHE A FATURA CONFORME O CARTÃO SELECIONADO
             * =============================================================
             *
             * O controller já calculou previamente qual é a fatura
             * aberta de cada cartão.
             *
             * Aqui apenas copiamos esses valores para os campos Ano/Mês.
             */
            function atualizarFaturaDoCartao() {

                /*
                 * Obtém a option atualmente selecionada.
                 */
                const optionSelecionada =
                    $('#ID_Cartao option:selected');


                /*
                 * Se ainda estiver em:
                 *
                 * "- Selecione um cartão -"
                 *
                 * não fazemos nada.
                 */
                if (
                    !optionSelecionada.length
                    ||
                    !optionSelecionada.val()
                ) {
                    return;
                }


                /*
                 * Recupera os valores calculados pelo backend.
                 *
                 * Exemplo:
                 *
                 * data-ano-fatura="2026"
                 * data-mes-fatura="8"
                 */
                const ano =
                    optionSelecionada.data('ano-fatura');

                const mes =
                    optionSelecionada.data('mes-fatura');


                /*
                 * Só altera quando realmente recebeu os dois valores.
                 */
                if (ano && mes) {

                    $('#Ano').val(ano);

                    $('#Mes').val(mes);
                }
            }


            /*
             * =============================================================
             * QUANDO TROCAR O CARTÃO
             * =============================================================
             *
             * Como o seu select utiliza bootstrap-select,
             * mantemos os dois eventos.
             */
            $('#ID_Cartao').on(
                'change changed.bs.select',
                function () {

                    atualizarFaturaDoCartao();

                }
            );


            /*
             * =============================================================
             * QUANDO A TELA ABRIR
             * =============================================================
             *
             * Se já houver cartão selecionado pela Session/old(),
             * preenche imediatamente a fatura dele.
             *
             * Porém, se houve erro de validação e existem valores old()
             * para Ano/Mês, preservamos aquilo que o usuário digitou.
             */
            @if(old('Ano') === null && old('Mes') === null)

            atualizarFaturaDoCartao();

            @endif

        });
    </script>


@stop
