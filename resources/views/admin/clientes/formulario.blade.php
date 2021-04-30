@extends('templates.guardiao')

@push('post-scripts')
    <script>
    $(document).ready(function(){
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
        $('.cep').mask('00000-000');
        $('.date').mask('00/00/0000');
        $('.tel_ddd').mask('(00) 0 0000-0000');
        $('.inscricao_estadual').mask('000000000');
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
            <li class="breadcrumb-item active" aria-current="page">Cadastro</li>
            </ol>
            <h6 class="slim-pagetitle">Cadastro de Clientes</h6>
        </div><!-- slim-pageheader -->
        <div class="section-wrapper">

            <div class="row row-sm mg-t-20">
                <div class="col-md-12">
                    <label class="section-title">Cadastro de Clientes</label>
                    <p class="mg-b-20 mg-sm-b-40">Formulário destinado ao cadastro de novos clientes.</p>
                </div>
            </div>
            
            <form action="{{route('admin.clientes.cadastrar')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12 mb-2">
                        <h5>Dados da Empresa</h5>
                    </div>
                    <div class="form-group col-3">
                        {!! Form::label('cnpj', 'CNPJ') !!}
                        {!! Form::text('dados[cnpj]', null, ['class' => 'form-control form-control-sm cnpj', 'id' => 'cnpj']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('inscricao_estadual', 'Inscrição Estadual') !!}
                        {!! Form::text('dados[inscricao_estadual]', null, ['class' => 'form-control form-control-sm inscricao_estadual', 'id' => 'inscricao_estadual']) !!}
                    </div>
                    <div class="form-group col-3">
                        {!! Form::label('email_cliente', 'Email') !!}
                        {!! Form::text('dados[email_cliente]', null, ['class' => 'form-control form-control-sm', 'id' => 'email_cliente']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('operacao', 'Operação') !!}
                        {!! Form::text('dados[operacao]', null, ['class' => 'form-control form-control-sm', 'id' => 'operacao']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('estado_destino', 'Estado Destino') !!}
                        {!! Form::select('dados[estado_destino]', $estados, null, ['class' => 'custom-select custom-select-sm', 'id' => 'estado_destino']) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-5">
                        {!! Form::label('razao_social', 'Razão Social') !!}
                        {!! Form::text('dados[razao_social]', null, ['class' => 'form-control form-control-sm', 'id' => 'razao_social']) !!}
                    </div>
                    <div class="form-group col-5">
                        {!! Form::label('nome_fantasia', 'Nome Fantasia') !!}
                        {!! Form::text('dados[nome_fantasia]', null, ['class' => 'form-control form-control-sm', 'id' => 'nome_fantasia']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('data_nascimento', 'Data de Abertura') !!}
                        {!! Form::date('dados[dt_nascimento]', null, ['class' => 'form-control form-control-sm', 'id' => 'data_nascimento']) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        {!! Form::label('nome_do_responsavel', 'Nome do Responsável') !!}
                        {!! Form::text('dados[nome_do_responsavel]', null, ['class' => 'form-control form-control-sm', 'id' => 'nome_do_responsavel']) !!}
                    </div>
                    <div class="form-group col-3">
                        {!! Form::label('tel1', 'Telefone') !!}
                        {!! Form::text('dados[tel1]', null, ['class' => 'form-control form-control-sm tel_ddd', 'id' => 'tel1']) !!}
                    </div>
                    <div class="form-group col-3">
                        {!! Form::label('tel2', 'Telefone') !!}
                        {!! Form::text('dados[tel2]', null, ['class' => 'form-control form-control-sm tel_ddd', 'id' => 'tel2']) !!}
                    </div>
                </div>

                {{-- Endereço --}}
                <div class="row">
                    <div class="col-12 mb-2 mt-3">
                        <h5>Dados da Empresa</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-2">
                        {!! Form::label('cep', 'CEP') !!}
                        {!! Form::text('dados[cep]', null, ['class' => 'form-control form-control-sm cep', 'id' => 'cep']) !!}
                    </div>
                    <div class="form-group col-8">
                        {!! Form::label('endereco', 'Endereço') !!}
                        {!! Form::text('dados[endereco]', null, ['class' => 'form-control form-control-sm', 'id' => 'endereco']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('numero', 'Numero') !!}
                        {!! Form::text('dados[numero]', null, ['class' => 'form-control form-control-sm', 'id' => 'numero']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-3">
                        {!! Form::label('bairro', 'Bairro') !!}
                        {!! Form::text('dados[bairro]', null, ['class' => 'form-control form-control-sm', 'id' => 'bairro']) !!}
                    </div>
                    <div class="form-group col-3">
                        {!! Form::label('complemento', 'Complemento') !!}
                        {!! Form::text('dados[complemento]', null, ['class' => 'form-control form-control-sm', 'id' => 'complemento']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('cidade', 'Cidade') !!}
                        {!! Form::text('dados[cidade]', null, ['class' => 'form-control form-control-sm', 'id' => 'cidade']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('estado', 'Estado') !!}
                        {!! Form::select('dados[estado]', $estados, null, ['class' => 'custom-select custom-select-sm', 'id' => 'estado']) !!}
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <button class="btn btn-sm btn-primary pull-right" type="submit">Cadastrar Cliente</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection