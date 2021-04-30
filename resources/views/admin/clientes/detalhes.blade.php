@extends('templates.guardiao')

@push('post-scripts')
    <script>
      $(document).ready(function(){
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
      });
    </script>
@endpush

@section('conteudo')

    <div class="slim-mainpanel">
        <div class="container">
            <div class="slim-pageheader">
                <ol class="breadcrumb slim-breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.clientes.index')}}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
                <h6 class="slim-pagetitle">{{$cliente->razao_social}}</h6>
            </div><!-- slim-pageheader -->
            <div class="section-wrapper">
                <div class="row row-sm mg-t-20">
                    <div class="col-6">
                        <span>Razão Social: {{$cliente->razao_social}}</span><br>
                        <span>Nome Fantasia: {{$cliente->nome_fantasia}}</span><br>
                        <span>CNPJ: <span class="cnpj">{{$cliente->cnpj}}</span></span><br>
                        <span>Data de Abertura: <span>{{date('d/m/Y', strtotime($cliente->dt_nascimento))}}</span></span><br>
                        <span>Operação: {{$cliente->operacao}}</span><br>
                        <span>Endereço: {{$cliente->endereco .','
                                            . $cliente->numero .','
                                            .$cliente->bairro
                                            .($cliente->complemento?', '.$cliente->endereco:'')
                                            .', '.$cliente->cidade
                                            .' - '.$cliente->estado }}</span><br>
                        <span>Telefone:  {{$cliente->tel1 . ($cliente->tel2?', '.$cliente->tel2:'')}}</span><br>
                        <span>Email:  {{$cliente->email_cliente}}</span>
                    </div>
                    <div class="col-6">
                        <span>Lotes: {{$lotes->count()}}</span><br>
                        <span>Usuarios: {{$usuarios->count()}}</span><br>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="slim-pageheader">
                <ol class="breadcrumb slim-breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.clientes.index')}}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
                <h6 class="slim-pagetitle">{{$cliente->razao_social}}</h6>
            </div><!-- slim-pageheader -->
            <div class="section-wrapper">
            </div>
        </div>
    </div>

@endsection