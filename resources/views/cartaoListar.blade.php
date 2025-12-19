@extends('adminlte::page')

@section('title', 'Cartão - Listar')

@section('content_header')
    @php
        /**
         * Status atual vindo da querystring:
         * - /cartoes?status=ativos
         * - /cartoes?status=inativos
         *
         * Se não vier nada, default = ativos
         */
        $statusAtual = request()->query('status', 'ativos');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Cartões</h1>

        <div class="btn-group">
            {{-- Botão ATIVOS: fica "primary" quando selecionado, senão "outline" --}}
            <a href="{{ route('cartoes.showAll', ['status' => 'ativos']) }}"
               class="btn {{ $statusAtual === 'ativos' ? 'btn-primary' : 'btn-outline-primary' }}">
                Ativos
            </a>

            {{-- Botão INATIVOS: fica "primary" quando selecionado, senão "outline" --}}
            <a href="{{ route('cartoes.showAll', ['status' => 'inativos']) }}"
               class="btn {{ $statusAtual === 'inativos' ? 'btn-primary' : 'btn-outline-primary' }}">
                Inativos
            </a>

            {{-- Botão adicionar (separado visualmente do toggle) --}}
            <a href="{{ route('cartoes.new') }}"
               class="btn btn-success ml-2">
                <i class="fas fa-plus"></i> Adicionar Cartão
            </a>
        </div>
    </div>
@stop

@section('content')

    @forelse($cartoes->chunk(3) as $chunk)
        <div class="row">
            @foreach($chunk as $cartao)
                <div class="col-md-4">
                    <!-- Widget: user widget style 1 -->
                    <div class="card card-widget widget-user">

                        {{-- Cabeçalho do cartão --}}
                        <div class="widget-user-header text-white"
                             style="background: {{ $cartao->Cor }}">
                            <h3 class="widget-user-username">{{ $cartao->Nome }}</h3>
                            <h5 class="widget-user-desc">{{ $cartao->Bandeira }}</h5>
                        </div>

                        {{-- Imagem --}}
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2"
                                 src="{{ asset('storage/cartao.png') }}"
                                 alt="Cartão">
                        </div>

                        {{-- Rodapé --}}
                        <div class="card-footer">
                            <div class="row">

                                {{-- Ano/Mês --}}
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">
                                            {{ $cartao->Ano_Mes ?? '-' }}
                                        </h5>
                                        <span class="description-text">Ano/Mês</span>
                                    </div>
                                </div>

                                {{-- Valor --}}
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">
                                            R$ {{ number_format($cartao->Valor, 2, ',', '.') }}
                                        </h5>
                                        <span class="description-text">Valor Fatura</span>
                                    </div>
                                </div>

                                {{-- Botão Fatura --}}
                                <div class="col-sm-4">
                                    <div class="description-block">
                                        <form id="fatura{{ $cartao->ID_Cartao }}"
                                              action="{{ route('cartoes.fatura') }}"
                                              method="GET">

                                            <input type="hidden" name="ID_Cartao" value="{{ $cartao->ID_Cartao }}">
                                            <input type="hidden" name="Ano_Mes" value="{{ $cartao->Ano_Mes }}">

                                            <a href="javascript:{}"
                                               onclick="document.getElementById('fatura{{ $cartao->ID_Cartao }}').submit();"
                                               class="btn btn-app">
                                                <span class="badge bg-info">
                                                    {{ $cartao->N_Despesas }}
                                                </span>
                                                <i class="fas fa-inbox"></i> Fatura
                                            </a>

                                        </form>
                                    </div>
                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
            @endforeach
        </div>
    @empty
        <div class="alert alert-info">
            Nenhum cartão encontrado.
        </div>
    @endforelse

@stop
