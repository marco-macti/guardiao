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
        <div class="col-md-3 pull-right">
              <a href="{{ URL('/') }}" class="btn btn-primary btn-block mg-b-10">Voltar</a>
      </div>
      <br style="clear: both;"/>
        <div class="section-wrapper">
            @include('admin.clientes._form',['method' => 'POST','action' => route('admin.clientes.store'), 'estados'=> $estados , 'cliente' => $cliente ])
        </div>
    </div>
</div>

@endsection

@push('post-scripts')
    <script>

    function validarCNPJ(cnpj) {

        if(cnpj == '') return false;

        if (cnpj.length != 14)
            return false;

        if (cnpj == "00000000000000" ||
            cnpj == "11111111111111" ||
            cnpj == "22222222222222" ||
            cnpj == "33333333333333" ||
            cnpj == "44444444444444" ||
            cnpj == "55555555555555" ||
            cnpj == "66666666666666" ||
            cnpj == "77777777777777" ||
            cnpj == "88888888888888" ||
            cnpj == "99999999999999")
            return false;

        tamanho = cnpj.length - 2
        numeros = cnpj.substring(0,tamanho);
        digitos = cnpj.substring(tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
                pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
                pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
            return false;

        return true;

    }

    $(document).ready(function(){

        var selecao = $("#em_degustacao option:selected").val();
        if(selecao == 'S'){
            $('.campos-degustacao').prop("disabled", false);
        }else{
            $('.campos-degustacao').prop("disabled", true);
        }

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

        $("#cnpj").on('blur',function(){

            let cnpj    = $("#cnpj").val().replace(/[^\d]+/g,'');
            var isValid = validarCNPJ(cnpj);

            if(!isValid){
                Swal.fire({
                    title: 'Atenção',
                    html: 'O cnpj informado não é valido , de acordo com o padrão da RFB.',
                    showCancelButton: false,
                });
            }

            $.getJSON("/admin/clientes/check-cnpj?cnpj="+ cnpj, function(dados) {
                if(!dados.isValid){
                    Swal.fire({
                    title: 'Atenção',
                    html: 'CNPJ já cadastrado na base de dados.',
                    showCancelButton: false,
                });
                }
            });

        })
    });
    </script>
@endpush
