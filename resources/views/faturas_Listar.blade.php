{{-- resources/views/faturas_Listar.blade.php --}}
@extends('adminlte::page')

@section('title', 'Faturas (Histórico)')

@section('content_header')
    <h1 class="m-0 text-dark">Faturas (Histórico)</h1>
@stop

@section('content')

    {{-- ALERTA: sem cartão selecionado --}}
    @if(empty($filtros['cartao_id']))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Selecione um <strong>Cartão</strong> nos filtros para ver o histórico de faturas.
        </div>
    @endif

    {{-- CARD: FILTROS --}}
    <x-adminlte-card title="Filtros" theme="lightblue" icon="fas fa-filter" collapsible>
        <form id="formFiltrosFaturas" method="GET" action="{{ route('faturas.listar') }}">

            @php
                // Segurança para evitar "Undefined index"
                $cartaoSelecionado  = (string) ($filtros['cartao_id'] ?? '');
                $anoMesDe           = (string) ($filtros['ano_mes_de'] ?? '');
                $anoMesAte          = (string) ($filtros['ano_mes_ate'] ?? '');
                $statusSelecionado  = (string) ($filtros['status'] ?? '');
                $contaSelecionada   = (string) ($filtros['conta_fechamento'] ?? '');
                $ordenarSelecionado = (string) ($filtros['ordenar'] ?? 'mes_desc');

                // Opções do cartão
                $opcoesCartao = $cartoes->pluck('Nome', 'ID_Cartao')->toArray();
            @endphp

            {{-- LINHA 1: todos os filtros na mesma linha (alinhados no topo) --}}
            <div class="d-flex flex-row flex-nowrap align-items-start"
                 style="gap: .75rem; overflow-x: auto; padding-bottom: .25rem;">

                {{-- CARTÃO --}}
                <div style="min-width: 280px; flex: 0 0 auto;">
                    <x-adminlte-select2 name="cartao_id" label="Cartão" fgroup-class="mb-0"
                                        :config="['placeholder' => 'Selecione um cartão...']">
                        <option value="">-- Selecione --</option>
                        @foreach($opcoesCartao as $id => $nome)
                            <option value="{{ $id }}" {{ $cartaoSelecionado === (string)$id ? 'selected' : '' }}>
                                {{ $nome }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>

                {{-- MÊS/ANO DE --}}
                <div style="min-width: 160px; flex: 0 0 auto;">
                    {{-- IMPORTANTE:
                         type="text" + JS para aceitar apenas AAAA-MM
                         inputmode="numeric" ajuda no mobile
                    --}}
                    <x-adminlte-input name="ano_mes_de" label="Mês/Ano de" type="text"
                                      value="{{ $anoMesDe }}"
                                      placeholder="YYYY-MM"
                                      inputmode="numeric"
                                      class="ano-mes-input"
                                      fgroup-class="mb-0"/>
                </div>

                {{-- MÊS/ANO ATÉ --}}
                <div style="min-width: 160px; flex: 0 0 auto;">
                    <x-adminlte-input name="ano_mes_ate" label="Mês/Ano até" type="text"
                                      value="{{ $anoMesAte }}"
                                      placeholder="YYYY-MM"
                                      inputmode="numeric"
                                      class="ano-mes-input"
                                      fgroup-class="mb-0"/>
                </div>

                {{-- STATUS --}}
                <div style="min-width: 160px; flex: 0 0 auto;">
                    <x-adminlte-select name="status" label="Status" fgroup-class="mb-0">
                        <option value="" {{ $statusSelecionado === '' ? 'selected' : '' }}>Todas</option>
                        <option value="abertas" {{ $statusSelecionado === 'abertas' ? 'selected' : '' }}>Abertas</option>
                        <option value="fechadas" {{ $statusSelecionado === 'fechadas' ? 'selected' : '' }}>Fechadas</option>
                    </x-adminlte-select>
                </div>

                {{-- CONTA DE FECHAMENTO --}}
                <div style="min-width: 240px; flex: 0 0 auto;">
                    <x-adminlte-select name="conta_fechamento" label="Conta de fechamento" fgroup-class="mb-0">
                        <option value="" {{ $contaSelecionada === '' ? 'selected' : '' }}>Todas</option>
                        @foreach($contasFechamento as $conta)
                            <option value="{{ $conta->ID_Conta }}"
                                {{ $contaSelecionada === (string)$conta->ID_Conta ? 'selected' : '' }}>
                                {{ $conta->Label }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                {{-- ORDENAR --}}
                <div style="min-width: 200px; flex: 0 0 auto;">
                    <x-adminlte-select name="ordenar" label="Ordenar por" fgroup-class="mb-0">
                        <option value="mes_desc"   {{ $ordenarSelecionado === 'mes_desc' ? 'selected' : '' }}>Mês (desc)</option>
                        <option value="mes_asc"    {{ $ordenarSelecionado === 'mes_asc' ? 'selected' : '' }}>Mês (asc)</option>
                        <option value="total_desc" {{ $ordenarSelecionado === 'total_desc' ? 'selected' : '' }}>Total (desc)</option>
                        <option value="total_asc"  {{ $ordenarSelecionado === 'total_asc' ? 'selected' : '' }}>Total (asc)</option>
                    </x-adminlte-select>
                </div>

            </div>


            {{-- LINHA 2: checkbox à esquerda + botões à direita --}}
            <div class="d-flex justify-content-between align-items-end mt-2">

                {{-- CHECKBOX --}}
                <div class="form-group mb-0">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="somente_com_lancamentos"
                               name="somente_com_lancamentos"
                               value="1"
                            {{ !empty($filtros['somente_com_lancamentos']) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="somente_com_lancamentos">
                            Somente com lançamentos
                        </label>
                    </div>
                </div>

                {{-- BOTÕES --}}
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>

                    <a href="{{ route('faturas.listar') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eraser"></i> Limpar
                    </a>
                </div>

            </div>

        </form>
    </x-adminlte-card>

    {{-- CARDS: RESUMO (só faz sentido se tiver cartão selecionado) --}}
    @if(!empty($filtros['cartao_id']))
        <div class="row">
            <div class="col-md-4">
                <x-adminlte-info-box title="Total no período"
                                     text="R$ {{ number_format($resumo['total_periodo'], 2, ',', '.') }}"
                                     icon="fas fa-coins" theme="info" />
            </div>
            <div class="col-md-4">
                <x-adminlte-info-box title="Total em aberto"
                                     text="R$ {{ number_format($resumo['total_aberto'], 2, ',', '.') }}"
                                     icon="fas fa-exclamation-circle" theme="warning" />
            </div>
            <div class="col-md-4">
                <x-adminlte-info-box title="Meses encontrados"
                                     text="{{ $resumo['qtd_meses'] }}"
                                     icon="far fa-calendar-alt" theme="success" />
            </div>
        </div>
    @endif

    {{-- TABELA: HISTÓRICO --}}
    <x-adminlte-card title="Histórico de Faturas" theme="secondary" icon="fas fa-table">
        @if(empty($filtros['cartao_id']))
            <div class="text-muted">
                Escolha um cartão nos filtros para carregar a tabela.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                    <tr>
                        <th>Mês/Ano</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                        <th>Fechamento</th>
                        <th>Conta</th>
                        <th class="text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($faturas as $f)
                        <tr>
                            <td>{{ $f->Ano_Mes }}</td>
                            <td>
                                @if((int)$f->Fechada === 1)
                                    <span class="badge badge-success">Fechada</span>
                                @else
                                    <span class="badge badge-warning">Aberta</span>
                                @endif
                            </td>

                            <td class="text-right">R$ {{ number_format((float)$f->Total, 2, ',', '.') }}</td>

                            <td>{{ $f->DataFechamentoFmt ?? '-' }}</td>
                            <td>{{ $f->ContaLabel ?? '-' }}</td>

                            {{-- AÇÃO: abrir fatura no padrão do sistema (route cartoes.fatura via GET) --}}
                            <td class="text-center">

                                {{-- Form GET para manter o padrão: /fatura?ID_Cartao=...&Ano_Mes=... --}}
                                <form id="faturaHistorico{{ $filtros['cartao_id'] }}_{{ str_replace('-', '', $f->Ano_Mes) }}"
                                      action="{{ route('cartoes.fatura') }}"
                                      method="GET"
                                      style="display:inline;">

                                    {{-- ID do cartão selecionado --}}
                                    <input type="hidden" name="ID_Cartao" value="{{ $filtros['cartao_id'] }}">

                                    {{-- Ano/Mês da linha do histórico --}}
                                    <input type="hidden" name="Ano_Mes" value="{{ $f->Ano_Mes }}">

                                    {{-- Botão visual (igual padrão do sistema, mas compacto) --}}
                                    <a href="javascript:{}"
                                       onclick="document.getElementById('faturaHistorico{{ $filtros['cartao_id'] }}_{{ str_replace('-', '', $f->Ano_Mes) }}').submit();"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Nenhuma fatura encontrada para os filtros informados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </x-adminlte-card>

@stop

@section('js')
    <script>
        /**
         * Máscara/validação para inputs de Ano/Mês no formato YYYY-MM.
         *
         * Regras:
         * - Permite somente dígitos e insere '-' automaticamente após 4 dígitos
         * - Limita a 7 caracteres (YYYY-MM)
         * - Valida mês de 01 a 12
         * - Se inválido no blur, limpa e força foco
         * - No submit, impede o envio e alerta
         */
        document.addEventListener('DOMContentLoaded', function () {

            const form = document.getElementById('formFiltrosFaturas');
            const inputs = document.querySelectorAll('.ano-mes-input');

            /**
             * Expressão regular do padrão aceito:
             * - 4 dígitos (ano)
             * - hífen
             * - mês de 01 a 12
             */
            const regexAnoMes = /^\d{4}-(0[1-9]|1[0-2])$/;

            /**
             * Aplica máscara YYYY-MM conforme o usuário digita:
             * - Remove caracteres não numéricos
             * - Limita a 6 dígitos (YYYYMM)
             * - Insere '-' após os 4 primeiros dígitos
             */
            function aplicarMascaraYYYYMM(el) {
                let v = el.value || '';

                // Remove tudo que não for número
                v = v.replace(/\D/g, '');

                // Limita a 6 dígitos (YYYYMM)
                if (v.length > 6) v = v.substring(0, 6);

                // Insere o hífen após o ano (YYYY-MM)
                if (v.length >= 5) v = v.substring(0, 4) + '-' + v.substring(4);

                el.value = v;
            }

            /**
             * Valida o valor:
             * - vazio é permitido (filtro opcional)
             * - se não estiver vazio, deve casar com regexAnoMes
             */
            function validoAnoMes(valor) {
                if (!valor) return true;
                return regexAnoMes.test(valor);
            }

            // Configura eventos para cada input
            inputs.forEach(input => {

                // Ajuda a evitar “colagens” com caracteres estranhos
                input.setAttribute('maxlength', '7');

                // Máscara ao digitar
                input.addEventListener('input', function () {
                    aplicarMascaraYYYYMM(this);
                });

                // Validação ao sair do campo
                input.addEventListener('blur', function () {
                    const v = (this.value || '').trim();

                    if (v === '') return;

                    if (!validoAnoMes(v)) {
                        alert('Informe o mês no formato YYYY-MM (ex: 2026-02).');
                        this.value = '';
                        this.focus();
                    }
                });

                // (Opcional) Bloqueia teclas que não fazem sentido
                input.addEventListener('keydown', function (e) {
                    // Permite teclas de navegação/edição
                    const permitidas = [
                        'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                        'ArrowLeft', 'ArrowRight', 'Home', 'End'
                    ];

                    if (permitidas.includes(e.key)) return;

                    // Permite Ctrl/Cmd + A/C/V/X
                    if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) return;

                    // Permite apenas números
                    if (!/^\d$/.test(e.key)) {
                        e.preventDefault();
                    }
                });
            });

            // Validação final antes de enviar o form
            if (form) {
                form.addEventListener('submit', function (e) {
                    // Revalida todos os campos (defesa extra)
                    for (const input of inputs) {
                        const v = (input.value || '').trim();
                        if (!validoAnoMes(v)) {
                            e.preventDefault();
                            alert('Corrija os campos "Mês/Ano de" e "Mês/Ano até" no formato YYYY-MM (ex: 2026-02).');
                            input.focus();
                            return;
                        }
                    }

                    // Regra opcional: se ambos preenchidos, "de" não pode ser maior que "até"
                    const vDe = (document.querySelector('input[name="ano_mes_de"]')?.value || '').trim();
                    const vAte = (document.querySelector('input[name="ano_mes_ate"]')?.value || '').trim();

                    if (vDe && vAte && regexAnoMes.test(vDe) && regexAnoMes.test(vAte) && vDe > vAte) {
                        e.preventDefault();
                        alert('O "Mês/Ano de" não pode ser maior que o "Mês/Ano até".');
                        document.querySelector('input[name="ano_mes_de"]')?.focus();
                    }
                });
            }

        });
    </script>
@stop
