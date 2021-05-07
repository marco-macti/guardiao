@extends('templates.guardiao')
@section('conteudo')

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
        console.log("teste");

        return false;
      });
    </script>
@endpush

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
              <th>Auditar</th>
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
                <a data-ncmia="{{$produto->ia_ncm}}" data-ncmimportado="{{$produto->ncm_importado}}" href="#" class="btn btn-secondary btn-block mg-b-10 diferenca"><i class="fa fa-arrows-h"></i></a>
              </td>
              <td>{{ $produto->ia_ncm  }} </td>
              <td style="color:red">{{ $produto->acuracia  }}</td>
              <td style="color:red">AUDITAR</td>
              <td>
                <a style="color:white" href="" class="btn btn-secondary btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1"><i class="fa fa-check"></i></a>
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
        <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Upload de arquivos</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body pd-25">
        <h5 class="lh-3 mg-b-20"><a href="" class="tx-inverse hover-primary">Treinando a IA</a></h5>
        <p class="mg-b-5">Informa o NCM correto para efetuar o treinamento da IA para este item. </p>
        <br/>
        <form action="#" id="treinar">
          <label> NCM : </label>
          <input name="ncm" type="text" id="ncm" class="form-control" />
        </form>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Treinar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div><!-- modal-dialog -->
</div>

<div id="modaldemo2" class="modal fade">
  <div class="modal-dialog modal-dialog-vertical-center" role="document">
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

@stop