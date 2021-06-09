@extends('templates.guardiao')

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

            @include('admin.clientes._form',['method' => 'PUT','action' => route('admin.clientes.update',$cliente->id),'estados'=> $estados , 'cliente' => $cliente ])

        </div>
    </div>
</div>

@endsection

@push('post-scripts')
    <script>
    $(document).ready(function(){

        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
        $('.cep').mask('00000-000');
        $('.date').mask('00/00/0000');
        $('.tel_ddd').mask('(00) 0 0000-0000');
        $('.inscricao_estadual').mask('000000000');

        $("#em_degustacao").change(function(){

            var selecao = $("#em_degustacao option:selected").val();

            if(selecao == 'S'){

                $('.campos-degustacao').prop("disabled", false);

            }else{

                $('.campos-degustacao').prop("disabled", true);

            }

        });

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
