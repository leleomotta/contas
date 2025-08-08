@extends('adminlte::page')

@section('title', 'Cartão - Listar')

@section('content_header')

@stop

@section('content')

    @foreach($cartoes->chunk(3) as $chunk)
        <div class="row">
            @foreach($chunk as $cartao)
                <div class="col-md-4">
                    <!-- Widget: user widget style 1 -->
                    <div class="card card-widget widget-user" >
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <!-- <div class="widget-user-header bg-info"> -->

                        <div class="widget-user-header text-white"
                                 style="background:{{ $cartao->Cor }}">

                            <h3 class="widget-user-username">{{ $cartao->Nome }}</h3>
                            <h5 class="widget-user-desc">{{ $cartao->Bandeira }}</h5>
                        </div>

                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" border=0 ALIGN=MIDDLE src="{{URL::asset('/storage/cartao.png')}}" alt="Cartão">
                        </div>


                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                            {{
                                            $cartao->Ano_Mes
                                            }}
                                        </h5>
                                        <span class="description-text">Ano/Mês</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">
                                            {{ 'R$ ' .  str_replace("_",'.',
                                                        str_replace(".",',',
                                                        str_replace(",",'_',
                                                        number_format($cartao->Valor, 2
                                                        )))) }}
                                        </h5>
                                        <span class="description-text">Valor Fatura</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4">
                                    <div class="description-block">
                                        <form id="fatura{{$cartao->ID_Cartao}}" role="form" action="{{ route('cartoes.fatura') }}" method="GET">
                                            <input type="hidden" name="ID_Cartao" value="{{ $cartao->ID_Cartao }}">
                                            <input type="hidden" name="Ano_Mes" value="{{ $cartao->Ano_Mes }}">
                                            <a href="javascript:{}" onclick="document.getElementById('fatura{{$cartao->ID_Cartao}}').submit();" class="btn btn-app">
                                                <span class="badge bg-info">{{$cartao->N_Despesas}}</span>
                                                <i class="fas fa-inbox"></i> Fatura
                                            </a>


                                        </form>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
            @endforeach
        </div>
    @endforeach


    @stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop
