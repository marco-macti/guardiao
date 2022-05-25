@extends('templates.guardiao')
@section('conteudo')
<div class="slim-mainpanel">
   <div class="container">
      <div class="slim-pageheader">
         <ol class="breadcrumb slim-breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home.index')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lotes</li>
            <li class="breadcrumb-item active" aria-current="page">Lote {{ $lote->numero_do_lote }}</li>
         </ol>
         <h6 class="slim-pagetitle">Lotes de produtos</h6>
      </div>
      <!-- slim-pageheader -->
      <div class="col-md-3 pull-right">
              <a href="{{ URL('/') }}" class="btn btn-primary btn-block mg-b-10">Voltar</a>
      </div>
      <br style="clear: both;"/>
      <div class="section-wrapper">
         <div class="row row-sm mg-t-20">
            <div class="col-md-6">
               <label class="section-title">Produtos deste lote</label>
               <p class="mg-b-20 mg-sm-b-40">Lista dos Produtos importados neste lote</p>
               <p class="mg-b-20 mg-sm-b-40">
                  Total: {{$produtos_total}}<br>
                  Auditados: {{$produtos_auditados}}<br>
                  Erros: {{$erros_total}}<br>
                  Acertos: {{$acertos_total}}
               </p>
            </div>
            <div class="col-md-6">
                <a style="float: right" target="_blank" href="{{ URL('lotes/'.$lote->id.'/export') }}" class="btn btn-primary">Exportar</a>
                <a href="#" data-href="{{route('lote.auditar', $lote->id)}}" class="btn btn-primary pull-right mr-3 btn-auditar-ia">Auditar IA</a>
             </div>
         </div>

         <form action="" method="GET">
            <div class="row">
                  <div class="col-md-3">
                     <select name="tipo_busca" class="form-control">
                        <option value="codigo_cliente">Código Interno do Cliente</option>
                        <option value="ncm_cliente">Ncm do Cliente</option>
                        <option value="ncm_ia">Ncm da IA</option>
                        <option value="acuracia">Acuracia</option>
                        <option value="situacao">Situação</option>
                     </select>
                  </div>
                  <div class="col-md-3 area-busca">
                     <input type="text" name="valor" class="form-control" placeholder="Informe o código">
                  </div>
                  <div class="col-md-3">
                     <select name="itens_paginas" class="form-control">
                        <option value="30">Quantidade de itens por página</option>
                        <option value="30">30 itens por página</option>
                        <option value="60">60 itens por página</option>
                        <option value="90">90 itens por página</option>
                     </select>
                  </div>
                  <div class="col-md-3">
                     <button type="submit" class="btn btn-primary">Buscar</button>
                  </div>
            </div>
      </form>

         @isset($msg_filtro)
            <hr>
            <div class="alert alert-primary" role="alert">
               {{$msg_filtro}}
               <br>
               <a href="{{route('lotes.edit', $lote->id)}}" class="text-primary ml-auto">Limpar Filtro</a>
            </div>
         @endisset

         <hr>

         <div class="row">
            <div class="col-md-12">
               <span style="color: black">
               * A responsabilidade civil , administrativa quanto ao NCM auditado e de total responsabilidade do Auditor nos termos da LEI LGPD 13709 de 14 de Agosto de 2018
               e LEI 13853 de 2019
               </span>
            </div>
         </div>

         <div class="table-responsive">
            <table class="table mg-b-0">
               <thead>
                  <tr>
                     {{-- <th>id </th> --}}
                     <th>Imagem </th>
                     <th>Descrição</th>
                     <th>Código</th>
                     <th>NCM Cliente</th>
                     <th>Comparativo</th>
                     <th>NCM IA</th>
                     <th>Acurácia</th>
                     <th>Acertou?</th>
                     <th>Opcoes</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse ($produtos as $produto)
                  <tr>
                     {{-- <td>{{ $produto->id }}</td> --}}
                     <td>
                        <img style="width: 100px" src="{{ URL('img-default.jpeg') }}">
                     </td>
                     <td>{{ $produto->descricao_do_produto }}</td>
                     <td>{{ $produto->codigo_interno_do_cliente }}</td>
                     <td><a data-descricao="{{ $produto->descricao_do_produto }}" data-ncmia="{{$produto->ia_ncm}}" data-ncmimportado="{{$produto->ncm_importado}}" style="color: #212529;background-color: #a0abaa;border-color: #a0abaa;" href="#" class="btn btn-secondary btn-block mg-b-10 diferenca">NCM COMPARADO IA</a></td>
                     <td>{{ $produto->ncm_importado  }} </td>
                     <td>{{ $produto->ia_ncm  }} </td>
                     @php
                     // Trata do retorno da acuracia
                     $classAcuracia  = '';
                     $totalAcuracia  = 0;
                     $classAcertou   = '';
                     $acertou        = '';
                     $permiteAuditar = false;

                     if($produto->acuracia >= 90.00 ){
                        $classAcuracia = 'success';
                        $totalAcuracia = '100%';
                     }elseif ($produto->acuracia >= 80.00 && $produto->acuracia < 90.00) {
                        $classAcuracia = 'warning';
                        $totalAcuracia = $produto->acuracia;
                     }elseif ($produto->acuracia < 80.00) {
                        $classAcuracia = 'danger';
                        $totalAcuracia = $produto->acuracia;
                     }
                     // informa se acertou ou nao
                     if($produto->ia_ncm == $produto->ncm_importado || $produto->auditado()){
                        // $classAcuracia = 'success';
                        // $totalAcuracia = '100%';
                        $classAcertou =  'success';
                        $acertou      =  'Acertou';
                     }elseif ($produto->ia_ncm != $produto->ncm_importado) {
                        $classAcertou =  'danger';
                        $acertou      =  'Errou';
                        $permiteAuditar = true;
                     }
                     @endphp
                     <td><span class="badge badge-{{ $produto->auditado() ? 'success' : $classAcuracia }}"> {{ $produto->auditado() ? '100' : $totalAcuracia  }} %</span></td>
                     <td><span class="badge badge-{{ $produto->auditado() ? 'success' : $classAcertou}}"> {{ $produto->auditado() ? 'Auditado' : $acertou  }} </span></td>
                     <td>
                        @if($produto->auditado() == true)
                            <a title="Produto ja auditado"
                               style="color: #212529;background-color: green;"
                               href="" class="btn btn-warning btn-block mg-b-10 btn-auditar"
                               data-toggle="modal"
                               data-target="#modal-auditar"
                               data-pre-auditado="{{ $produto->preAuditado() }}"
                               data-descricao="{{$produto->descricao_do_produto}}"
                               data-ncm-importado="{{$produto->ncm_importado}}"
                               data-lote-id="{{ $produto->lote_id }}"
                               data-lote-produto-id="{{ $produto->id }}">AUDITADO</a>
                        @else
                            <a title="Produto necessita de auditoria"
                               style="color: #212529;background-color: red;"
                               href=""
                               class="btn btn-warning btn-block mg-b-10 btn-auditar"
                               data-toggle="modal"
                               data-target="#modal-auditar"
                               data-pre-auditado=""
                               data-descricao="{{$produto->descricao_do_produto}}"
                               data-ncm-importado="{{$produto->ncm_importado}}"
                               data-lote-id="{{ $produto->lote_id }}"
                               data-lote-produto-id="{{ $produto->id }}">AUDITAR</a>
                        @endif
                     </td>
                  </tr>
                  @empty
                  <tr colspan="6">Nenhum produto neste lote</tr>
                  @endforelse
               </tbody>
            </table>
            {{ $produtos->appends(request()->input())->links()}}
         </div>
         <!-- table-responsive -->
      </div>
   </div>
   <!-- container -->
</div>
<!-- slim-mainpanel -->
<div id="modal-auditar" class="modal fade">
   <div class="modal-dialog modal-dialog-vertical-center" role="document">
      <div class="modal-content bd-0 tx-14">
         <div class="modal-header">
            <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Auditoria de NCM </h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body pd-25">
            <p class="mg-b-5"> Informe o NCM correto para efetuar a auditoria deste item.</p>
            <br/>
            <form action="#" id="treinar">
               <label> NCM : </label>
               <input name="ncm_auditoria" type="text" id="ncm_auditoria" class="form-control" />
            </form>
            <br/>
            <p class="mg-b-5"> Ou busque por produtos baseados na descricao deste produto </p>
            <br/>
            <a style="color: #212529;background-color: gray;"
               href=""
               class="btn btn-warning btn-block mg-b-10 btn-buscar-relacionados"
               data-toggle="modal"
               data-target="#modal-relacionados"
               data-descricao=""
               data-pre-auditado=""
               data-ncm-importado=""
               data-lote-id=""
               data-lote-produto-id="">BUSCA POR DESCRICAO</a>
         </div>
         <div class="modal-footer">
            <button id="buscar_ncm_para_auditoria" type="button" class="btn btn-primary">Buscar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
         </div>
      </div>
   </div>
   <!-- modal-dialog -->
</div>

<div id="modal-diferenca" class="modal fade" >
   <div class="modal-dialog modal-dialog-vertical-center" role="document" style="min-width: 70%">
      <div class="modal-content bd-0 tx-14">
         <div class="modal-header">
            <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Descricao do NCM</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body pd-25">
            <table class="table">
               <tbody>
                  <tr>
                     <td><img style="width: 300px" src="{{ URL('img-default.jpeg') }}">&nbsp;&nbsp;<span id="descricao-produto-ia"></span></td>
                     <th style="text-align: center" scope="row"></th>
                     <td></td>
                  </tr>
                  <tr>
                     <th class="text-nowrap" id="th-ncm-importado" scope="row"></th>
                     <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
                     <th class="text-nowrap" id="th-ncm-ia" scope="row"></th>
                  </tr>
                  <tr>
                     <td>
                        <b>Capítulo TIPI : </b>
                        <p id="td-captulo-importado"></p>
                     </td>
                     <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
                     <td>
                        <b>Capítulo TIPI : </b>
                        <p id="td-captulo-ia"></p>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <b>Posição TIPI : </b>
                        <p id="td-posicao-importado"></p>
                     </td>
                     <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
                     <td>
                        <b>Posição TIPI : </b>
                        <p id="td-posicao-ia"></p>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <b>Subposiçao TIPI : </b>
                        <p id="td-suposicao-importado"></p>
                     </td>
                     <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
                     <td>
                        <b>Subposiçao TIPI : </b>
                        <p id="td-suposicao-ia"></p>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <b>Subitem TIPI : </b>
                        <p id="td-subitem-importado"></p>
                     </td>
                     <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
                     <td>
                        <b>Subitem TIPI : </b>
                        <p id="td-subitem-ia"></p>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
   <!-- modal-dialog -->
</div>

<div id="modalDadosAuditar" class="modal fade" >
   <div class="modal-dialog modal-dialog-vertical-center" role="document" style="min-width: 70%">
      <div class="modal-content bd-0 tx-14">
         <div class="modal-header">
            <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Consulta de NCM pesquisado</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body pd-25">
            <table class="table">
               <tbody>
                  <tr>
                     <button onClick="assumirNcm(this)" type="button" class="btn btn-secondary btn-assumir-ncm " data-ncm-auditado="" data-ncm-importado="" data-lote-id='' data-lote-produto-id="">Assumir este NCM</button>
                     <br/>
                     <br/>
                  </tr>
                  <tr>
                     <th class="text-nowrap" id="th-ncm-auditar" scope="row"></th>
                  </tr>
                  <tr>
                     <td>
                        <b>Capítulo TIPI : </b>
                        <p id="td-captulo-auditar"></p>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <b>Posição TIPI : </b>
                        <p id="td-posicao-auditar"></p>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <b>Subposiçao TIPI : </b>
                        <p id="td-suposicao-auditar"></p>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <b>Subitem TIPI : </b>
                        <p id="td-subitem-auditar"></p>
                     </td>
                  </tr>
               </tbody>
            </table>
            <h2>Produtos Relacionados</h2>
            <table class="table table-hover table-striped">
               <tbody id="produtos-cosmos">
               </tbody>
            </table>
         </div>
         <div class="modal-footer">
            <button onClick="assumirNcm(this)" type="button" class="btn btn-secondary btn-assumir-ncm" data-ncm-auditado="" data-ncm-importado="" data-lote-id='' data-lote-produto-id="">Assumir este NCM</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
   <!-- modal-dialog -->
</div>

<div id="modal-relacionados" class="modal fade" >
   <div class="modal-dialog modal-dialog-vertical-center" role="document" style="min-width: 70%">
      <div class="modal-content bd-0 tx-14">
         <div class="modal-header">
            <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Produtos relacionados</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body pd-25">
            <table class="table table-hover table-striped">
               <thead>
                    <th>Imagem do Produto</th>
                    <th>Nome do Produto</th>
                    <th>GTIN/EAN</th>
                    <th>NCM</th>
                    <th>opções</th>
               </thead>
               <tbody id="produtos-relacionados-cosmos">
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <!-- modal-dialog -->
</div>

@push('post-scripts')

<script>

   $(document).on('change', 'select[name=tipo_busca]', function(){
      tipo = $(this).val();
      html = '';
      switch (tipo) {
         case 'codigo_cliente':
            html = '<input type="text" name="valor" class="form-control" placeholder="Informe o código">';
            break;

         case 'ncm_cliente':
            html = '<input type="text" name="valor" class="form-control" placeholder="Informe o NCM cliente">';
            break;

         case 'ncm_ia':
            html = '<input type="text" name="valor" class="form-control" placeholder="Informe o NCM da IA">';
            break;

         case 'acuracia':
            html =   '<select name="valor" class="form-control">'+
                     '<option value="1">menor que 80%</option>'+
                     '<option value="2">Entre 80% e 90%</option>'+
                     '<option value="3">maior que 90%</option>'+
                     '</select>';
            break;

         case 'situacao':
            html =   '<select name="valor" class="form-control">'+
                     '<option value="acerto">Acertou</option>'+
                     '<option value="erro">Errou</option>'+
                     '</select>';
            break;

         default:
            break;
      }

      $('.area-busca').empty();
      $('.area-busca').append(html);
   });

    function assumirNcm(target){

        let ncm_importado   = target.dataset.ncmImportado;
        let ncm_auditado    = target.dataset.ncmAuditado; $(this).attr('data-ncm-auditado');
        let lote_id         = target.dataset.loteId;
        let lote_produto_id = target.dataset.loteProdutoId;

        var url = '/lotes/assumir-ncm/?ncm_importado='+ncm_importado+'&ncm_auditado='+ncm_auditado+'&lote_id='+lote_id+'&lote_produto_id='+lote_produto_id;

        $.ajax({
            url: url,
            success: function(response){

            response = JSON.parse(response)

            if(response.success){
                Swal.fire({
                title: 'Atualizado com sucesso',
                text: 'O NCM '+ncm_importado+' foi auditado para '+ncm_auditado,
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok'
                }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
                })
            }else{
                Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Ocorreu uma falha, faça contato conosco ou tente mais tarde'
                })
            }
            }
        });

        return false;

    }

   $(".btn-auditar").on('click', function(){

       var preAuditado = $(this).attr('data-pre-auditado');

        if(preAuditado != '' && preAuditado != 0){
           Swal.fire({
               icon: 'error',
               title: 'Cuidado!',
               text: 'Ops! O produto em questão possui acerto durante a Auditoria da Inteligencia Artificial. Deseja mesmo prosseguir?'
             })
        }

       $("#buscar_ncm_para_auditoria").attr('data-ncm-importado', $(this).attr('data-ncm-importado'))
       $("#buscar_ncm_para_auditoria").attr('data-lote-id', $(this).attr('data-lote-id'))
       $("#buscar_ncm_para_auditoria").attr('data-lote-produto-id', $(this).attr('data-lote-produto-id'))


       $(".btn-buscar-relacionados").attr('data-descricao', $(this).attr('data-descricao'))
       $(".btn-buscar-relacionados").attr('data-ncm-importado', $(this).attr('data-ncm-importado'))
       $(".btn-buscar-relacionados").attr('data-lote-id', $(this).attr('data-lote-id'))
       $(".btn-buscar-relacionados").attr('data-lote-produto-id', $(this).attr('data-lote-produto-id'))
   })

   $('.diferenca').on('click', function(){

       Swal.fire({
       title: 'Aguarde',
       html: 'Consulta de NCM em progresso...',
       timerProgressBar: true,
       didOpen: () => {
           Swal.showLoading()
       }
       });

       var ncmimportado = $(this).data('ncmimportado');
       var ncmia        = $(this).data('ncmia');
       var descricao    = $(this).data('descricao');

       $("#descricao-produto-ia").html(descricao);

       var url = '{{route("ia.consulta.ncm")}}'+'/?ia='+ncmia+'&importado='+ncmimportado;

       $.ajax({
           url: url,
           success: function(response){
               $('#th-ncm-importado').empty();
               $('#th-ncm-importado').append('NCM IMPORTADO : '+ncmimportado);
               $('#th-ncm-ia').empty();
               $('#th-ncm-ia').append('NCM IA : '+ncmia);

               $('#td-captulo-importado').empty();
               $('#td-captulo-importado').append(response.importado.desc_ncm_cliente_capitulo.ex_capitulo);
               $('#td-captulo-ia').empty();
               $('#td-captulo-ia').append(response.ncm.desc_ncm_cliente_capitulo.ex_capitulo);

               $('#td-posicao-importado').empty();
               $('#td-posicao-importado').append(response.importado.desc_ncm_cliente_posicao.ex_posicao);
               $('#td-posicao-ia').empty();
               $('#td-posicao-ia').append(response.ncm.desc_ncm_cliente_posicao.ex_posicao);

               $('#td-suposicao-importado').empty();
               $('#td-suposicao-importado').append(response.importado.desc_ncm_cliente_subposicao.ex_subposicao);
               $('#td-suposicao-ia').empty();
               $('#td-suposicao-ia').append(response.ncm.desc_ncm_cliente_subposicao.ex_subposicao);

               $('#td-subitem-importado').empty();
               $('#td-subitem-importado').append(response.importado.desc_ncm_cliente_subitem.ex_sub_item);
               $('#td-subitem-ia').empty();
               $('#td-subitem-ia').append(response.ncm.desc_ncm_cliente_subitem.ex_sub_item);

               Swal.close();
               $('#modal-diferenca').modal('show');
           }
       });

       return false;
   });

    /*$(".btn-assumir-ncm").on('click', function(){

        let ncm_importado   = $(this).attr('data-ncm-importado');
        let ncm_auditado    = $(this).attr('data-ncm-auditado');
        let lote_id         = $(this).attr('data-lote-id');
        let lote_produto_id = $(this).attr('data-lote-produto-id');

        var url = '/lotes/assumir-ncm/?ncm_importado='+ncm_importado+'&ncm_auditado='+ncm_auditado+'&lote_id='+lote_id+'&lote_produto_id='+lote_produto_id;

        $.ajax({
            url: url,
            success: function(response){

            response = JSON.parse(response)

            if(response.success){
                Swal.fire({
                title: 'Atualizado com sucesso',
                text: 'O NCM '+ncm_importado+' foi auditado para '+ncm_auditado,
                icon: 'success',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok'
                }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
                })
            }else{
                Swal.fire({
                icon: 'error',
                title: 'Oops',
                text: 'Ocorreu uma falha, faça contato conosco ou tente mais tarde'
                })
            }
            }
     });

     return false;
    });*/

    $('#buscar_ncm_para_auditoria').on('click', function(){

      let ncm_importado   = $(this).attr('data-ncm-importado');
      let lote_id         = $(this).attr('data-lote-id');
      let lote_produto_id = $(this).attr('data-lote-produto-id');

       Swal.fire({
       title: 'Aguarde',
       html: 'Consulta de NCM em progresso...',
       timerProgressBar: true,
       didOpen: () => {
           Swal.showLoading()
       }
       });

       var ncm_auditado = $('#ncm_auditoria').val();

       var url = '/ia/retorna-dados-planilha/'+ncm_auditado;

       $.ajax({
           url: url,
           success: function(response){

               $('#th-ncm-auditar').empty();
               $('#th-ncm-auditar').append('NCM : '+ncm_auditado);

               $('#td-captulo-auditar').empty();
               $('#td-captulo-auditar').append(response.desc_ncm_cliente_capitulo.ex_capitulo);


               $('#td-posicao-auditar').empty();
               $('#td-posicao-auditar').append(response.desc_ncm_cliente_posicao.ex_posicao);


               $('#td-suposicao-auditar').empty();
               $('#td-suposicao-auditar').append(response.desc_ncm_cliente_subposicao.ex_subposicao);


               $('#td-subitem-auditar').empty();
               $('#td-subitem-auditar').append(response.desc_ncm_cliente_subitem.ex_sub_item);

               Swal.close();

               $('#modalDadosAuditar').modal('show');

               if(response.data != ''){
                    for (let index = 0; index < response.cosmos.length; index++) {
                        if(index <= 10){
                            $("#produtos-cosmos").append('<tr>'+
                              '<td align="center">'+
                                       '<img src="'+response.cosmos[index].thumbnail+'" width="100" height="100" />'+
                              '</td>'+
                              '<td align="center">'+
                              response.cosmos[index].description+
                              '</td>'+
                              '</tr>');
                        }
                    }
               }



               $(".btn-assumir-ncm").attr('data-ncm-importado', ncm_importado)
               $(".btn-assumir-ncm").attr('data-ncm-auditado', ncm_auditado)
               $(".btn-assumir-ncm").attr('data-lote-id', lote_id)
               $(".btn-assumir-ncm").attr('data-lote-produto-id', lote_produto_id)

               //data-ncm-auditado="" data-ncm-importado="" data-lote-id='' data-lote-produto-id=""
           }
       });

       return false;
    });

    $('.btn-auditar-ia').on('click', function(){
      var href = $(this).data('href');

      Swal.fire({
         title: 'Atenção',
         html: 'Você deseja enviar os produtos deste lote, para que sejam auditados pela Inteligência Artificial do <strong>Guardião Tributário</strong>?<br>Esse processo será efetuado em fila e poderá demorar alguns minutos!',
         showCancelButton: true,
      }).then(function(result){
         if(result.isConfirmed)
         {
            window.location.href = href;
         }
      });
    });

    $('.btn-buscar-relacionados').on('click', function(){

        document.getElementById("produtos-relacionados-cosmos").innerHTML = "";

        let ncm_importado   = $(this).attr('data-ncm-importado');
        let lote_id         = $(this).attr('data-lote-id');
        let lote_produto_id = $(this).attr('data-lote-produto-id');
        let descricao       = $(this).attr('data-descricao');

        Swal.fire({
            title: 'Aguarde',
            html: 'Consulta de NCM em progresso...',
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        var ncm_auditado = $('#ncm_auditoria').val();

        var url = '/lotes/busca-relacionados-by-descricao?descricao='+descricao;

        $.ajax({
            url: url,
            success: function(response){

                Swal.close();

                $('#modal-relacionados').modal('show');

                if(response.data != ''){

                    for (let index = 0; index < response.data.length; index++) {

                        if(index <= 10 && response.data[index].ncm != '' ){

                            let imagem = 'Sem imagem';

                            if(response.data[index].thumbnail){
                                imagem = '<img src="'+response.data[index].thumbnail+'" width="100" height="100" />';
                            }

                        $("#produtos-relacionados-cosmos").append('<tr>'+
                                                    '<td align="center">'+imagem+'</td>'+
                                                    '<td align="center">'+response.data[index].description+'</td>'+
                                                    '<td align="center">'+response.data[index].gtins[0].gtin+'</td>'+
                                                    '<td align="center">'+response.data[index].ncm.code+'</td>'+
                                                    '<td align="center"><button onClick="assumirNcm(this)" type="button" class="btn btn-secondary btn-assumir-ncm" data-ncm-auditado="'+response.data[index].ncm.code+'" data-ncm-importado="'+ncm_importado+'" data-lote-id="'+lote_id+'" data-lote-produto-id="'+lote_produto_id+'">Assumir este NCM</button></td>'+
                                                    '</tr>');
                        }

                    }
                }


            }
        });
        return false;
    });

</script>
@endpush
@stop
