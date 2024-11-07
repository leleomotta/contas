@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Contas</h1>
@stop

@section('content')

    @foreach($contas->chunk(3) as $chunk)
        <div class="row">
            @foreach($chunk as $conta)
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

                        <div class="widget-user-image">
                            @if (! $conta['Imagem'] == null)
                                <img class="img-circle elevation-2" src='data:image/jpeg;base64,{{base64_encode( $conta['Imagem'] ) }} ' alt="Imagem">
                            @else
                                <img class="img-circle elevation-2" border=0 ALIGN=MIDDLE src='/contas/resources/img/user2-160x160.jpg' alt="Imagem">
                            @endif
                        </div>


                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header" data-inputmask="'alias': 'numeric', 'prefix': 'R$ '">
                                            {{ 'R$ ' .  str_replace("-",'.',
                                                        str_replace(".",',',
                                                        str_replace(",",'-',
                                                        number_format($conta->Saldo_Inicial, 2
                                                        )))) }}</h5>
                                        <span class="description-text">SALDO INICIAL</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">13,000</h5>
                                        <span class="description-text">RECEITAS</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4">
                                    <div class="description-block">
                                        <h5 class="description-header">35</h5>
                                        <span class="description-text">DESPESAS</span>
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
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
