@extends('templates.guardiao')
@section('conteudo')

<div class="slim-mainpanel">
  <div class="container">
    <div class="slim-pageheader">
      <ol class="breadcrumb slim-breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Lotes</li>
        <li class="breadcrumb-item active" aria-current="page">Lote 1</li>
      </ol>
      <h6 class="slim-pagetitle">Lotes de produtos</h6>
    </div><!-- slim-pageheader -->

    <div class="section-wrapper">
      <div class="row row-sm mg-t-20">
        <div class="col-md-12">
          <label class="section-title">Produtos deste lote</label>
          <p class="mg-b-20 mg-sm-b-40">Lista dos Produtos importados neste lote</p>
        </div>
      </div>

      <span style="color: black">
        * A responsabilidade civil , administrativa quanto ao NCM auditado e de total responsabilidade do Auditor nos termos da LEI LGPD 13709 de 14 de Agosto de 2018
        e LEI 13853 de 2019
      </span>

      <div class="table-responsive">
        <table class="table mg-b-0">
          <thead>
            <tr>
              <th>Imagem </th>
              <th>Descrição</th>
              <th>Código</th>
              <th>NCM Cliente</th>
              <th>Diferença entre NCMs</th>
              <th>NCM IA</th>
              <th>Acurácia</th>
              <th>Acertou?</th>
              <th>Situação</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($produtos as $produto)
            <tr>
              <td>
                <img style="width: 100px" src="{{ URL('img-default.jpeg') }}">
              </td>
              <td>{{ $produto->descricao_do_produto }}</td>
              <td>{{ $produto->codigo_interno_do_cliente }}</td>
              <td>{{ $produto->ncm_importado  }} </td>
              <td>
                <a data-descricao="{{ $produto->descricao_do_produto }}" data-ncmia="{{$produto->ia_ncm}}" data-ncmimportado="{{$produto->ncm_importado}}" style="color: #212529;background-color: #a0abaa;border-color: #a0abaa;" href="#" class="btn btn-secondary btn-block mg-b-10 diferenca">NCM COMPARADO</a>
              </td>
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


                if($produto->ia_ncm == $produto->ncm_importado ){
                    $classAcertou =  'success';
                    $acertou      =  'Acertou';
                }elseif ($produto->ia_ncm != $produto->ncm_importado) {
                    $classAcertou =  'danger';
                    $acertou      =  'Errou';
                    $permiteAuditar = true;
                }

              @endphp
              <td><span class="badge badge-{{ $classAcuracia}}"> {{ $totalAcuracia  }} %</span></td>
              <td><span class="badge badge-{{ $classAcertou}}"> {{ $acertou  }} </span></td>
              <td>
                <a title="Produto necessita de auditoria" style="color: #212529;background-color: #a0abaa;border-color: #a0abaa;" href="" class="btn btn-warning btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1">AUDITAR</a>
              </td>
            </tr>
            @empty
              <tr colspan="6">Nenhum produto neste lote</tr>
            @endforelse
          </tbody>
        </table>

        {{ $produtos->links()}}
      </div><!-- table-responsive -->
    </div>

  </div><!-- container -->

</div><!-- slim-mainpanel -->


<div id="modaldemo1" class="modal fade">
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
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Auditar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div><!-- modal-dialog -->
</div>

<div id="modaldemo2" class="modal fade" >
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
              <td><b>Capítulo TIPI : </b> <p id="td-captulo-importado"></p></td>
              <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
              <td><b>Capítulo TIPI : </b> <p id="td-captulo-ia"></p></td>
            </tr>
            <tr>
              <td><b>Posição TIPI : </b><p id="td-posicao-importado"></p></td>
              <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
              <td><b>Posição TIPI : </b><p id="td-posicao-ia"></p></td>
            </tr>
            <tr>
              <td><b>Subposiçao TIPI : </b> <p id="td-suposicao-importado"></p> </td>
              <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
              <td><b>Subposiçao TIPI : </b> <p id="td-suposicao-ia"></p> </td>
            </tr>
            <tr>
              <td><b>Subitem TIPI : </b> <p id="td-subitem-importado"></p> </td>
              <th style="text-align: center" scope="row"><i class="fa fa-arrows-h"></i> </th>
              <td><b>Subitem TIPI : </b> <p id="td-subitem-ia"></p> </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div><!-- modal-dialog -->
</div>

@push('post-scripts')
    <script>

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
        var ncmia = $(this).data('ncmia');
        var descricao = $(this).data('descricao');

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
            $('#modaldemo2').modal('show');
          }
        });

        return false;
      });
    </script>
@endpush

@stop
