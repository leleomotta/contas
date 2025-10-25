@extends('adminlte::page')

@section('title', 'Recorrência - Editar')

@section('content_header')
@stop

@section('content')
    <form id="edicao" role="form" action="{{ route('recorrencias.update', $recorrencia->ID_Recorrencia) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card card-primary">
            <div class="card-header">
                {{-- TÍTULO ATUALIZADO --}}
                <h3 class="card-title">Editar Recorrência</h3>
            </div>
            <div class="card-body">

                <label>Descrição</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Descricao" class="form-control" id="Descricao" placeholder="Descrição" value="{{ $recorrencia->Descricao }}">
                </div>

                {{-- NOVO CAMPO: TIPO (RECEITA/DESPESA) --}}
                <label>Tipo</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-exchange-alt"></i></span></div>
                    {{-- O campo Tipo é desabilitado pois não deve ser alterado na edição --}}
                    <select name="Tipo" id="Tipo" class="form-control" disabled>
                        <option value="D" {{ $recorrencia->Tipo == 'D' ? 'selected' : '' }}>Despesa</option>
                        <option value="R" {{ $recorrencia->Tipo == 'R' ? 'selected' : '' }}>Receita</option>
                    </select>
                </div>

                <label>Valor</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-money-bill"></i></span></div>
                    <input type="text" id="Valor" name="Valor"
                           class="form-control text-left"
                           data-inputmask="'alias':'numeric','groupSeparator':'.','autoGroup':true,'digits':2,'digitsOptional':false,'radixPoint':',','prefix':'R$ ','placeholder':'0'"
                           placeholder="Digite o valor" value="R$ {{ number_format($recorrencia->Valor, 2, ',', '.') }}">
                </div>

                <label>Categoria</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-list-alt"></i></span></div>
                    <select name="ID_Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-default>- Selecione uma categoria -</option>
                        @foreach($categorias as $categoria)
                            {{-- ATUALIZADO: Adicionado data-tipo para filtragem JS --}}
                            <option value="{{$categoria->ID_Categoria}}"
                                    data-tipo="{{ $categoria->Tipo }}"
                                    data-content="<span class='icone-circulo' style='background-color: {{ $categoria->Cor }};'><i class='{{ $categoria->Link }}'></i></span> {{$categoria->Nome}}"
                                {{ $recorrencia->ID_Categoria == $categoria->ID_Categoria ? 'selected' : '' }}>
                                {{$categoria->Nome}}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ATUALIZADO: Bloco de pagamento (apenas para Despesa) --}}
                <div id="blocoDespesa" style="display:{{ $recorrencia->Tipo == 'D' ? 'block' : 'none' }}">
                    <label id="labelPagamento">Forma de Pagamento</label>
                    <div class="form-group">
                        <select class="form-control" name="TipoPagamento" id="TipoPagamento">
                            <option value="">- Selecione -</option>
                            <option value="conta" {{ !is_null($recorrencia->ID_Conta) ? 'selected' : '' }}>Conta</option>
                            <option value="cartao" {{ !is_null($recorrencia->ID_Cartao) ? 'selected' : '' }}>Cartão</option>
                        </select>
                    </div>

                    {{-- Bloco do Cartão (movido para dentro do blocoDespesa) --}}
                    <div id="divCartao" style="display:{{ !is_null($recorrencia->ID_Cartao) ? 'block' : 'none' }}">
                        <label>Cartão</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-credit-card"></i></span></div>
                            <select name="ID_Cartao" id="Cartao" class="form-control selectpicker" data-live-search="true">
                                <option value="">- Selecione um cartão -</option>
                                @foreach($cartoes as $cartao)
                                    <option value="{{$cartao->ID_Cartao}}" {{ $recorrencia->ID_Cartao == $cartao->ID_Cartao ? 'selected' : '' }}>{{$cartao->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ATUALIZADO: Bloco da Conta (agora usado por Receita e Despesa) --}}
                <div id="divConta" style="display:{{ !is_null($recorrencia->ID_Conta) ? 'block' : 'none' }}">
                    <label id="labelConta">Conta</label> {{-- ID e texto serão setados via JS --}}
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-landmark"></i></span></div>
                        <select name="ID_Conta" id="Conta" class="form-control selectpicker" data-live-search="true">
                            <option value="">- Selecione uma conta -</option>
                            @foreach($contas as $conta)
                                <option value="{{$conta->ID_Conta}}" {{ $recorrencia->ID_Conta == $conta->ID_Conta ? 'selected' : '' }}>{{$conta->Nome}} - {{$conta->Banco}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <label>Periodicidade</label>
                <div class="form-group">
                    <select class="form-control" id="Periodicidade" name="Periodicidade">
                        <option value="Mensal" {{ $recorrencia->Periodicidade == 'Mensal' ? 'selected' : '' }}>Mensal</option>
                        <option value="Anual" {{ $recorrencia->Periodicidade == 'Anual' ? 'selected' : '' }}>Anual</option>
                        <option value="Semanal" {{ $recorrencia->Periodicidade == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                    </select>
                </div>

                {{-- Campo dinâmico DiaVencimento (lógica movida para o JS para permitir a troca) --}}
                <div class="form-group" id="grupoDia">
                    {{-- O JS vai popular isso na carga da página --}}
                </div>

                <label>Data de início</label>
                <div class="input-group date" id="DataInicio" data-target-input="nearest">
                    <div class="input-group-append" data-target="#DataInicio" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    <input type="text" name="DataInicio" class="form-control datetimepicker-input" data-target="#DataInicio" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask placeholder="dd/mm/yyyy" value="{{ \Carbon\Carbon::parse($recorrencia->Data_inicio)->format('d/m/Y') }}"/>
                </div>

                <label class="mt-3">Data de fim (opcional)</label>
                <div class="input-group date" id="DataFim" data-target-input="nearest">
                    <div class="input-group-append" data-target="#DataFim" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar-times"></i></div>
                    </div>
                    <input type="text" name="DataFim" class="form-control datetimepicker-input" data-target="#DataFim" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask placeholder="dd/mm/yyyy" value="{{ $recorrencia->Data_fim ? \Carbon\Carbon::parse($recorrencia->Data_fim)->format('d/m/Y') : '' }}"/>
                </div>

                <div class="form-group mt-3">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="Ativa" name="Ativa" {{ $recorrencia->Ativa ? 'checked' : '' }}>
                        <label class="custom-control-label" for="Ativa">Ativa</label>
                    </div>
                </div>

            </div>

            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Redefinir</button>
            </div>
        </div>
    </form>


@stop

@section('css')
    {{-- Seção CSS idêntica à de criação --}}
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
        .is-invalid .dropdown-toggle {
            border-color: #dc3545 !important;
        }
    </style>
@stop

@section('js')
    {{-- JS Quase idêntico ao de criação, com a lógica de inicialização --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/i18n/defaults-pt_BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.js"></script>
    <script>
        $('#DataInicio').datetimepicker({ format:'DD/MM/YYYY' });
        $('#DataFim').datetimepicker({ format:'DD/MM/YYYY' });
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

        // --- Função para filtrar categorias ---
        function filtrarCategorias(tipo, categoriaSelecionadaId) {
            let $categoriaSelect = $('#Categoria');
            let $opcoes = $categoriaSelect.find('option');

            $opcoes.first().show();

            $opcoes.each(function() {
                if ($(this).data('default')) return;

                if ($(this).data('tipo') === tipo) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Se o ID da categoria selecionada não pertencer ao TIPO, reseta
            let $opcaoSelecionada = $categoriaSelect.find('option[value="' + categoriaSelecionadaId + '"]');
            if ($opcaoSelecionada.length > 0 && $opcaoSelecionada.data('tipo') !== tipo) {
                $categoriaSelect.val($opcoes.first().val());
            } else {
                // Mantém a seleção atual
                $categoriaSelect.val(categoriaSelecionadaId);
            }

            $categoriaSelect.selectpicker('refresh');
        }

        // --- Lógica principal para Receita/Despesa ---
        function atualizarFormPeloTipo() {
            let tipo = $('#Tipo').val();
            // Pega a categoria que está salva no DB
            let categoriaSelecionadaId = '{{ $recorrencia->ID_Categoria }}';

            if (tipo === 'R') { // É Receita
                $('#blocoDespesa').hide();
                $('#divCartao').hide();
                $('#divConta').show();
                $('#labelConta').text('Conta de Destino');

                $('#TipoPagamento').val('');
                $('#ID_Cartao').val('').selectpicker('refresh');

            } else { // É Despesa (default)
                $('#blocoDespesa').show();
                $('#labelConta').text('Conta');

                // Dispara o change do TipoPagamento para re-exibir o campo correto (conta/cartao)
                // A seleção de conta/cartao já foi definida no HTML
                $('#TipoPagamento').trigger('change');
            }

            // Filtra categorias, mantendo a seleção se possível
            filtrarCategorias(tipo, categoriaSelecionadaId);
        }

        // Lógica de exibição Conta/Cartão para Despesas
        $('#TipoPagamento').change(function () {
            if ($('#Tipo').val() === 'D') {
                $('#divConta').hide();
                $('#divCartao').hide();

                if ($(this).val() === 'conta') {
                    $('#divConta').show();
                    $('#ID_Cartao').val('').selectpicker('refresh');
                } else if ($(this).val() === 'cartao') {
                    $('#divCartao').show();
                    $('#ID_Conta').val('').selectpicker('refresh');
                }
            }
        });

        // --- Validação com regras dinâmicas ---
        $('#edicao').validate({
            rules: {
                Descricao: { required: true },
                Valor: { required: true },
                ID_Categoria: { required: true },
                TipoPagamento: {
                    required: function () {
                        return $('#Tipo').val() === 'D';
                    }
                },
                ID_Conta: {
                    required: function () {
                        return $('#Tipo').val() === 'R' ||
                            ($('#Tipo').val() === 'D' && $('#TipoPagamento').val() === 'conta');
                    }
                },
                ID_Cartao: {
                    required: function () {
                        return $('#Tipo').val() === 'D' && $('#TipoPagamento').val() === 'cartao';
                    }
                },
                Dia_vencimento: { // Nome do campo atualizado
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    }
                },
                DataInicio: { required: true }
            },
            messages: {
                Descricao: { required: 'A descrição é obrigatória.' },
                Valor: { required: 'O valor é obrigatório.' },
                ID_Categoria: { required: 'Selecione uma categoria.' },
                TipoPagamento: { required: 'Selecione a forma de pagamento.' },
                ID_Conta: { required: 'Selecione uma conta.' },
                ID_Cartao: { required: 'Selecione um cartão.' },
                Dia_vencimento: { required: 'Informe um valor válido.' },
                DataInicio: { required: 'A data de início é obrigatória.' }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.hasClass('selectpicker')) {
                    element.closest('.input-group').append(error);
                } else if (element.closest('.input-group').length) {
                    element.closest('.input-group').append(error);
                } else {
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
                if ($(element).hasClass('selectpicker')) {
                    $(element).closest('.input-group').find('.dropdown-toggle').addClass('is-invalid');
                }
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                if ($(element).hasClass('selectpicker')) {
                    $(element).closest('.input-group').find('.dropdown-toggle').removeClass('is-invalid');
                }
            }
        });

        // --- Lógica para recriar o campo de vencimento ---
        // Pega os valores que vieram do PHP
        let periodicidadeAtual = '{{ $recorrencia->Periodicidade }}';
        let vencimentoAtual = '{{ $recorrencia->Dia_vencimento }}';

        function recriarCampoVencimento(tipo, valor) {
            let grupo = $('#grupoDia');
            let html = '';

            if (tipo === 'Mensal') {
                html = '<label id="labelDia">Dia do mês</label><input type="text" class="form-control text-left" name="Dia_vencimento" id="Dia_vencimento" placeholder="Ex: 15" data-inputmask-alias="numeric" data-inputmask-inputformat="99" data-mask>';
            } else if (tipo === 'Anual') {
                html = '<label id="labelDia">Data (dia e mês)</label><input type="text" class="form-control text-left" name="Dia_vencimento" id="Dia_vencimento" placeholder="Ex: 25/12" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm" data-mask>';
            } else if (tipo === 'Semanal') {
                html = `<label id="labelDia">Dia da semana</label>
                <select class="form-control" name="Dia_vencimento" id="Dia_vencimento">
                    <option value="">- Selecione -</option>
                    <option value="Domingo">Domingo</option>
                    <option value="Segunda">Segunda</option>
                    <option value="Terca">Terça</option>
                    <option value="Quarta">Quarta</option>
                    <option value="Quinta">Quinta</option>
                    <option value="Sexta">Sexta</option>
                    <option value="Sábado">Sábado</option>
                </select>`;
            }

            grupo.html(html);

            // Aplica o valor e as máscaras
            $('#Dia_vencimento').val(valor);
            if (tipo === 'Semanal') {
                // Para o select, apenas setar o val() é suficiente
            } else {
                $('[data-mask]').inputmask();
            }
        }

        // Ao trocar a periodicidade, recria o campo (sem valor)
        $('#Periodicidade').change(function () {
            recriarCampoVencimento($(this).val(), '');
        });

        // --- Dispara o change na carga da página para setar o estado inicial ---
        $(document).ready(function() {
            // Recria o campo de vencimento com o valor salvo
            recriarCampoVencimento(periodicidadeAtual, vencimentoAtual);

            // Define o estado inicial do formulário (Receita/Despesa)
            atualizarFormPeloTipo();
        });

    </script>
@stop
