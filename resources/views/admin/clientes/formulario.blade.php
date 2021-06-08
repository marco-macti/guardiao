@extends('templates.guardiao')

@push('post-scripts')
    <script>
    $(document).ready(function(){

        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
        $('.cep').mask('00000-000');
        $('.date').mask('00/00/0000');
        $('.tel_ddd').mask('(00) 0 0000-0000');
        $('.inscricao_estadual').mask('000000000');

        $("#cep").blur(function() {

            //Nova variável "cep" somente com dígitos.
            var cep = $(this).val().replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if(validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    $("#rua").val("...");
                    $("#bairro").val("...");
                    $("#cidade").val("...");
                    $("#uf").val("...");
                    $("#ibge").val("...");

                    //Consulta o webservice viacep.com.br/
                    $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                        if (!("erro" in dados)) {
                            //Atualiza os campos com os valores da consulta.
                            $("#endereco").val(dados.logradouro);
                            $("#bairro").val(dados.bairro);
                            $("#cidade").val(dados.localidade);
                            $("#estado").val(dados.uf);
                        } //end if.
                        else {
                            //CEP pesquisado não foi encontrado.
                            limpa_formulário_cep();
                            alert("CEP não encontrado.");
                        }
                    });
                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        });
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
            <form action="{{route('admin.clientes.cadastrar')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12 mb-2">
                        <h5>Dados da Empresa</h5>
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('cnpj', 'CNPJ') !!}
                        {!! Form::text('dados[cnpj]', null, ['class' => 'form-control form-control-sm cnpj', 'id' => 'cnpj']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('razao_social', 'Razão Social') !!}
                        {!! Form::text('dados[razao_social]', null, ['class' => 'form-control form-control-sm', 'id' => 'razao_social']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('nome_fantasia', 'Nome Fantasia') !!}
                        {!! Form::text('dados[nome_fantasia]', null, ['class' => 'form-control form-control-sm', 'id' => 'nome_fantasia']) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-4">
                        {!! Form::label('inscricao_estadual', 'Inscrição Estadual') !!}
                        {!! Form::text('dados[inscricao_estadual]', null, ['class' => 'form-control form-control-sm inscricao_estadual', 'id' => 'inscricao_estadual']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('email_cliente', 'Email') !!}
                        {!! Form::text('dados[email_cliente]', null, ['class' => 'form-control form-control-sm', 'id' => 'email_cliente']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('operacao', 'Operação') !!}
                        {!! Form::select('dados[operacao]', ['venda' => 'Venda' ], null, ['class' => 'custom-select custom-select-sm', 'id' => 'operacao']) !!}
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-2">
                        {!! Form::label('estado_origem', 'Estado Origem') !!}
                        {!! Form::select('dados[estado_origem]', $estados, null, ['class' => 'custom-select custom-select-sm', 'id' => 'estado_origem']) !!}
                    </div>
                    <div class="form-group col-2">
                        {!! Form::label('estado_destino', 'Estado Destino') !!}
                        {!! Form::select('dados[estado_destino]', $estados, null, ['class' => 'custom-select custom-select-sm', 'id' => 'estado_destino']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('data_nascimento', 'Data de Abertura') !!}
                        {!! Form::date('dados[dt_nascimento]', null, ['class' => 'form-control form-control-sm', 'id' => 'data_nascimento']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('nome_do_responsavel', 'Nome do Responsável') !!}
                        {!! Form::text('dados[nome_do_responsavel]', null, ['class' => 'form-control form-control-sm', 'id' => 'nome_do_responsavel']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('tel1', 'Telefone 1') !!}
                        {!! Form::text('dados[tel1]', null, ['class' => 'form-control form-control-sm tel_ddd', 'id' => 'tel1']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('tel2', 'Telefone 2') !!}
                        {!! Form::text('dados[tel2]', null, ['class' => 'form-control form-control-sm tel_ddd', 'id' => 'tel2']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('enquadramento_tributario', 'Enquadramento Tributario') !!}
                        {!! Form::select('dados[enquadramento_tributario]', ['LR' => 'Lucro Real','LP' => 'Lucro Presumido', 'SN' => 'Simples Nacional'], null, ['class' => 'custom-select custom-select-sm', 'id' => 'enquadramento_tributario']) !!}
                    </div>
                </div>

                {{-- Endereço --}}

                <div class="row">
                    <div class="col-12 mb-2 mt-3">
                        <h5>Dados da Empresa</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-4">
                        {!! Form::label('cep', 'CEP') !!}
                        {!! Form::text('dados[cep]', null, ['class' => 'form-control form-control-sm cep', 'id' => 'cep']) !!}
                    </div>
                    <div class="form-group col-8">
                        {!! Form::label('endereco', 'Endereço') !!}
                        {!! Form::text('dados[endereco]', null, ['class' => 'form-control form-control-sm', 'id' => 'endereco']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('estado', 'Estado') !!}
                        {!! Form::select('dados[estado]', $estados, null, ['class' => 'custom-select custom-select-sm', 'id' => 'estado']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('cidade', 'Cidade') !!}
                        {!! Form::text('dados[cidade]', null, ['class' => 'form-control form-control-sm', 'id' => 'cidade']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('bairro', 'Bairro') !!}
                        {!! Form::text('dados[bairro]', null, ['class' => 'form-control form-control-sm', 'id' => 'bairro']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('numero', 'Numero') !!}
                        {!! Form::text('dados[numero]', null, ['class' => 'form-control form-control-sm', 'id' => 'numero']) !!}
                    </div>
                    <div class="form-group col-4">
                        {!! Form::label('complemento', 'Complemento') !!}
                        {!! Form::text('dados[complemento]', null, ['class' => 'form-control form-control-sm', 'id' => 'complemento']) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-2 mt-3">
                        <h5>Situação do Cadastro</h5>
                    </div>
                </div>
                <div class="row">

                    <div class="form-group col-3">
                        {!! Form::label('em_degustacao', 'Em degustacao') !!}
                        {!! Form::select('dados[em_degustacao]', ['S' => 'Sim','N' => 'Não'], 'N', ['class' => 'custom-select custom-select-sm', 'id' => 'em_degustacao']) !!}
                    </div>

                    <div class="form-group col-3">
                        {!! Form::label('data', 'Data Inicio') !!}
                        {!! Form::date('dados[dt_nascimento]', null, ['class' => 'form-control form-control-sm', 'id' => 'data_nascimento']) !!}
                    </div>

                    <div class="form-group col-3">
                        {!! Form::label('data_nascimento', 'Data Fim') !!}
                        {!! Form::date('dados[dt_nascimento]', null, ['class' => 'form-control form-control-sm', 'id' => 'data_nascimento']) !!}
                    </div>

                    <div class="form-group col-3">
                        {!! Form::label('cidade', 'Quantidade de NCMs') !!}
                        {!! Form::text('dados[cidade]', null, ['class' => 'form-control form-control-sm', 'id' => 'cidade']) !!}
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
