@extends('adminlte::page')

@section('title', 'Recorrência - Editar')

@section('content_header')
@stop

@section('content')
    <form id="cadastro" role="form" action="{{ route('recorrencias.update', ['ID_Recorrencia' => $recorrencia->ID_Recorrencia]) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar despesa recorrente</h3>
            </div>
            <div class="card-body">
                <!-- Campos fixos -->
                <label>Descrição</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-info-circle"></i></span>
                    </div>
                    <input type="text" name="Descricao" class="form-control" id="Descricao" placeholder="Descrição" value="{{ $recorrencia->Descricao }}">
                </div>

                <label>Valor</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-money-bill"></i></span></div>
                    <input type="text" id="Valor" name="Valor" class="form-control text-left"
                           data-inputmask="'alias':'numeric','groupSeparator':'.','autoGroup':true,'digits':2,'digitsOptional':false,'radixPoint':',','prefix':'R$ ','placeholder':'0'"
                           placeholder="Digite o valor da despesa" value="R$ {{ number_format($recorrencia->Valor, 2, ',', '.') }}">
                </div>

                <label>Categoria</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-list-alt"></i></span></div>
                    <select name="ID_Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true">
                        <option data-default>- Selecione uma categoria -</option>
                        @foreach($categorias as $categoria)
                            <option value="{{$categoria->ID_Categoria}}"
                                    {{ $categoria->ID_Categoria == $recorrencia->ID_Categoria ? 'selected' : '' }}
                                    data-content="<span class='icone-circulo' style='background-color: {{ $categoria->Cor }};'><i class='{{ $categoria->Link }}'></i></span> {{$categoria->Nome}}">
                                {{$categoria->Nome}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label>Tipo</label>
                <div class="form-group">
                    <select class="form-control" name="TipoPagamento" id="TipoPagamento">
                        <option value="">- Selecione -</option>
                        <option value="conta" {{ $recorrencia->ID_Conta ? 'selected' : '' }}>Conta</option>
                        <option value="cartao" {{ $recorrencia->ID_Cartao ? 'selected' : '' }}>Cartão</option>
                    </select>
                </div>

                <div id="divConta" style="display: {{ $recorrencia->ID_Conta ? 'block' : 'none' }}">
                    <label>Conta</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-landmark"></i></span></div>
                        <select name="ID_Conta" id="Conta" class="form-control selectpicker" data-live-search="true">
                            <option>- Selecione uma conta -</option>
                            @foreach($contas as $conta)
                                <option value="{{$conta->ID_Conta}}" {{ $conta->ID_Conta == $recorrencia->ID_Conta ? 'selected' : '' }}>{{$conta->Nome}} - {{$conta->Banco}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="divCartao" style="display: {{ $recorrencia->ID_Cartao ? 'block' : 'none' }}">
                    <label>Cartão</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-credit-card"></i></span></div>
                        <select name="ID_Cartao" id="Cartao" class="form-control selectpicker" data-live-search="true">
                            <option>- Selecione um cartão -</option>
                            @foreach($cartoes as $cartao)
                                <option value="{{$cartao->ID_Cartao}}" {{ $cartao->ID_Cartao == $recorrencia->ID_Cartao ? 'selected' : '' }}>{{$cartao->Nome}}</option>
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

                <!-- Campo dinâmico DiaVencimento -->
                <div class="form-group" id="grupoDia"></div>

                <label>Data de início</label>
                <div class="input-group date" id="DataInicio" data-target-input="nearest">
                    <div class="input-group-append" data-target="#DataInicio" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    <input type="text" name="DataInicio" class="form-control datetimepicker-input" data-target="#DataInicio" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask placeholder="dd/mm/yyyy" value="{{ \Carbon\Carbon::parse($recorrencia->DataInicio)->format('d/m/Y') }}"/>
                </div>

                <label class="mt-3">Data de fim (opcional)</label>
                <div class="input-group date" id="DataFim" data-target-input="nearest">
                    <div class="input-group-append" data-target="#DataFim" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar-times"></i></div>
                    </div>
                    <input type="text" name="DataFim" class="form-control datetimepicker-input" data-target="#DataFim" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask placeholder="dd/mm/yyyy" value="{{ $recorrencia->DataFim ? \Carbon\Carbon::parse($recorrencia->DataFim)->format('d/m/Y') : '' }}"/>
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
                <a href="{{ route('recorrencias.showAll') }}" class="btn btn-default"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </div>
    </form>
@stop

@section('js')


    <script>
        $(function () {
            function renderGrupoDia(periodicidade, valor) {
                let grupo = $('#grupoDia');
                let html = '';

                valor = (valor || '').trim();
                valor = valor.charAt(0).toUpperCase() + valor.slice(1).toLowerCase();

                if (periodicidade === 'Mensal') {
                    html = '<label id="labelDia">Dia do mês</label><input type="text" class="form-control" name="DiaVencimento" id="DiaVencimento" placeholder="Ex: 15" data-inputmask-alias="numeric" data-inputmask-inputformat="99" data-mask value="' + valor + '">';
                } else if (periodicidade === 'Anual') {
                    html = '<label id="labelDia">Data (dia e mês)</label><input type="text" class="form-control" name="DiaVencimento" id="DiaVencimento" placeholder="Ex: 25/12" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm" data-mask value="' + valor + '">';
                } else if (periodicidade === 'Semanal') {
                    html = `<label id="labelDia">Dia da semana</label><select class="form-control" name="DiaVencimento" id="DiaVencimento">
                        <option value="">- Selecione -</option>
                        <option value="Domingo" ${valor === 'Domingo' ? 'selected' : ''}>Domingo</option>
                        <option value="Segunda" ${valor === 'Segunda' ? 'selected' : ''}>Segunda</option>
                        <option value="Terca" ${valor === 'Terca' ? 'selected' : ''}>Terça</option>
                        <option value="Quarta" ${valor === 'Quarta' ? 'selected' : ''}>Quarta</option>
                        <option value="Quinta" ${valor === 'Quinta' ? 'selected' : ''}>Quinta</option>
                        <option value="Sexta" ${valor === 'Sexta' ? 'selected' : ''}>Sexta</option>
                        <option value="Sábado" ${valor === 'Sábado' ? 'selected' : ''}>Sábado</option>
                    </select>`;
                }
                grupo.html(html);
                $('[data-mask]').inputmask();
            }

            // Inicializa com os dados da recorrência
            renderGrupoDia("{{ $recorrencia->Periodicidade }}", "{{ $recorrencia->Dia_vencimento }}");

            // Atualiza campo dinâmico ao trocar periodicidade
            $('#Periodicidade').change(function () {
                renderGrupoDia($(this).val(), '');
            });
        });
    </script>
@endsection
