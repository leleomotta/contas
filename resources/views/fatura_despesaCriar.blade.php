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
                        <input type="text" name="Descricao" class="form-control" id="Descricao" value="{{ old('Descricao') }}"
                               placeholder="Digite uma descrição para a despesa">
                    </div>
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
                        <select name="Categoria" id="Categoria" class="form-control selectpicker" data-live-search="true">
                            <option disabled {{ old('Categoria') ? '' : 'selected' }}>- Selecione uma categoria -</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->ID_Categoria }}"
                                        data-content="<i class='{{ $categoria->Link }}'></i> {{ $categoria->Nome }}"
                                    {{ old('Categoria') == $categoria->ID_Categoria ? 'selected' : '' }}>
                                    {{ $categoria->Nome }}
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
                        <input type="number" id="Ano" name="Ano" min="1900" max="2500"
                               value="{{ old('Ano', \Carbon\Carbon::now()->format('Y')) }}">
                        <input type="number" id="Mes" name="Mes" min="1" max="12"
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
@stop
