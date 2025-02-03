@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Contas</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Ativas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Inativas</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <tbody>
                                        @foreach($contasAtivas->chunk(3) as $ativas)
                                            <div class="row">
                                                @foreach($ativas as $conta)
                                                    <div class="col-md-4">
                                                        <!-- Widget: user widget style 1 -->
                                                        <div class="card card-widget widget-user" >
                                                            <!-- Add the bg color to the header using any of the bg-* classes -->
                                                            <!-- <div class="widget-user-header bg-info"> -->

                                                            <div class="widget-user-header text-white"
                                                                     style="background:{{ $conta->Cor }}">
                                                                <h3 class="widget-user-username">{{ $conta->Banco }}</h3>
                                                                <h5 class="widget-user-desc">{{ $conta->Descricao }}</h5>
                                                            </div>

                                                            <a onclick="window.location='{{ route('contas.edit', ['ID_Conta' =>$conta->ID_Conta]) }}'" >
                                                                <div class="widget-user-image">

                                                                    @if (! $conta->Imagem == null)
                                                                        <img class="img-circle elevation-2" src='data:image/jpeg;base64,{{base64_encode( $conta->Imagem ) }} ' alt="Imagem">
                                                                    @else
                                                                        <img class="img-circle elevation-2" border=0 ALIGN=MIDDLE src="{{URL::asset('/storage/banco.png')}}" alt="Banco">
                                                                    @endif
                                                                </div>
                                                            </a>

                                                            <div class="card-footer">
                                                                <div class="row">
                                                                    <div class="col-sm-4 border-right">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Saldo, 2
                                                                                            )))) }} </h5>
                                                                            <span class="description-text">SALDO ATUAL</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>
                                                                    <!-- /.col -->
                                                                    <div class="col-sm-4 border-right">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Receitas, 2
                                                                                            )))) }}</h5>
                                                                            <span class="description-text">RECEITAS</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>
                                                                    <!-- /.col -->
                                                                    <div class="col-sm-4">
                                                                        <div class="description-block">
                                                                            <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                {{ 'R$ ' .  str_replace("_",'.',
                                                                                            str_replace(".",',',
                                                                                            str_replace(",",'_',
                                                                                            number_format($conta->Despesas, 2
                                                                                            )))) }}</h5>
                                                                            <span class="description-text">DESPESAS</span>
                                                                        </div>
                                                                        <!-- /.description-block -->
                                                                    </div>

                                                                </div>
                                                                <!-- /.row -->
                                                            </div>
                                                        </div>
                                                        <!-- /.widget-user -->
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <tbody>
                                       @foreach($contasArquivadas->chunk(3) as $inativas)
                                            <div class="row">
                                                @foreach($inativas as $conta)
                                                    <div class="col-md-4">
                                                            <!-- Widget: user widget style 1 -->
                                                            <div class="card card-widget widget-user" >
                                                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                                                <!-- <div class="widget-user-header bg-info"> -->

                                                                <div class="widget-user-header text-white"
                                                                     style="background:{{ $conta->Cor }}">
                                                                    <h3 class="widget-user-username">{{ $conta->Banco }}</h3>
                                                                    <h5 class="widget-user-desc">{{ $conta->Descricao }}</h5>
                                                                </div>

                                                                <a onclick="window.location='{{ route('contas.edit', ['ID_Conta' =>$conta->ID_Conta]) }}'" >
                                                                    <div class="widget-user-image">

                                                                        @if (! $conta->Imagem == null)
                                                                            <img class="img-circle elevation-2" src='data:image/jpeg;base64,{{base64_encode( $conta->Imagem ) }} ' alt="Imagem">
                                                                        @else
                                                                            <img class="img-circle elevation-2" border=0 ALIGN=MIDDLE src="{{URL::asset('/storage/banco.png')}}" alt="Banco">
                                                                        @endif
                                                                    </div>
                                                                </a>

                                                                <div class="card-footer">
                                                                    <div class="row">
                                                                        <div class="col-sm-4 border-right">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Saldo, 2
                                                                                                )))) }} </h5>
                                                                                <span class="description-text">SALDO ATUAL</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>
                                                                        <!-- /.col -->
                                                                        <div class="col-sm-4 border-right">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Receitas, 2
                                                                                                )))) }}</h5>
                                                                                <span class="description-text">RECEITAS</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>
                                                                        <!-- /.col -->
                                                                        <div class="col-sm-4">
                                                                            <div class="description-block">
                                                                                <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                                                                    {{ 'R$ ' .  str_replace("_",'.',
                                                                                                str_replace(".",',',
                                                                                                str_replace(",",'_',
                                                                                                number_format($conta->Despesas, 2
                                                                                                )))) }}</h5>
                                                                                <span class="description-text">DESPESAS</span>
                                                                            </div>
                                                                            <!-- /.description-block -->
                                                                        </div>

                                                                    </div>
                                                                    <!-- /.row -->
                                                                </div>
                                                            </div>
                                                            <!-- /.widget-user -->
                                                        </div>
                                                @endforeach
                                            </div>
                                      @endforeach

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
        </div>
    </div>



    @stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop
