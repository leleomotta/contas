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
                                <option value="{{ $cartao->ID_Cartao }}"
                                    {{ old('ID_Cartao', Session::get('ID_Cartao')) == $cartao->ID_Cartao ? 'selected' : '' }}>
                                    {{ $cartao->ID_Cartao }} - {{ $cartao->Nome }} ({{ $cartao->Bandeira }})
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
                               value="{{ old('Ano', \Carbon\Carbon::now()->format('Y')) }}">

                        {{-- MÊS: permitimos 0 e 13 para o spinner “passar do limite” e o JS normalizar --}}
                        <input type="number"
                               id="Mes" name="Mes"
                               min="0" max="13" step="1"
                               class="form-control form-control-sm fatura-mes"
                               value="{{ old('Mes', \Carbon\Carbon::now()->format('m')) }}">
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

            /**
             * Endpoint que criamos no CartaoController
             * (rota nomeada => sem hardcode de URL).
             */
            const urlDescricoesCartao = "{{ route('cartoes.despesaDescricoes') }}";

            // Debounce: reduz número de requisições enquanto o usuário digita
            let debounceTimer = null;

            /**
             * Pega o ID do cartão selecionado no <select>.
             * IMPORTANTE:
             * - Garanta que o <select> do cartão tenha id="ID_Cartao"
             *   (no seu form ele já usa name="ID_Cartao"; manter id igual facilita).
             */
            function getIdCartaoSelecionado() {
                const val = $('#ID_Cartao').val();
                return val ? val : '';
            }

            /**
             * Preenche o datalist usando DOM API (mais seguro do que montar HTML na mão).
             */
            function preencherDatalist(lista) {
                const datalist = document.getElementById('cartao_despesa_descricoes');
                datalist.innerHTML = '';

                (lista || []).forEach((texto) => {
                    const opt = document.createElement('option');
                    opt.value = texto;
                    datalist.appendChild(opt);
                });
            }

            /**
             * Faz a busca no backend.
             * Mandamos também o ID_Cartao para filtrar descrições daquele cartão (melhor UX).
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

            /**
             * Ao digitar: a partir de 2 caracteres buscamos no backend.
             */
            $('#Descricao').on('input', function () {
                const q = ($(this).val() || '').trim();

                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    if (q.length < 2) {
                        preencherDatalist([]);
                        return;
                    }
                    buscarDescricoes(q);
                }, 250);
            });

            /**
             * UX: ao focar no campo vazio, carrega as “mais comuns”.
             */
            $('#Descricao').on('focus', function () {
                const q = ($(this).val() || '').trim();
                const datalist = document.getElementById('cartao_despesa_descricoes');

                // Se está vazio e ainda não carregou nada, traz “top 15”
                if (q.length === 0 && datalist.children.length === 0) {
                    buscarDescricoes('');
                }
            });

            /**
             * Se o usuário trocar o cartão, faz sentido resetar as sugestões,
             * porque o conjunto “mais relevante” muda.
             *
             * Observação: como você usa bootstrap-select, alguns casos disparam
             * o evento 'changed.bs.select' em vez de 'change'. Vamos cobrir os dois.
             */
            $('#ID_Cartao').on('change changed.bs.select', function () {
                preencherDatalist([]);

                // Se o usuário já está com algo digitado, podemos refazer a busca
                const q = ($('#Descricao').val() || '').trim();
                if (q.length >= 2) {
                    buscarDescricoes(q);
                }
            });

        });
    </script>


@stop
