@extends('templates.guardiao')
@section('conteudo')

<div class="slim-mainpanel">
  <div class="container">
    <div class="slim-pageheader">
      <ol class="breadcrumb slim-breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home.index')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Lotes</li>
        <li class="breadcrumb-item active" aria-current="page">Lote {{$lote->id}}</li>
      </ol>
      <h6 class="slim-pagetitle">Lotes de produtos</h6>
    </div><!-- slim-pageheader -->

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
          <a href="{{route('admin.lotes.edit', $lote->id)}}" class="text-primary ml-auto">Limpar Filtro</a>
        </div>
      @endisset

      <hr>

      <div class="table-responsive">
        <table class="table mg-b-0">
          <thead>
            <tr>
              <th>Imagem </th>
              <th>Descrição</th>
              <th>Código</th>
              <th>NCM Cliente</th>
              <th>NCM IA</th>
              <th>Acurácia</th>
              <th>Acertou?</th>
              <th>Treinar</th>
              <th>Tributação</th>
              <th>Monofásico</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($produtos as $produto)
              <tr>
                <td>
                  <img style="width: 100px" src="{{ URL('img-default.jpeg') }}">
                </td>
                <td>{{$produto->descricao_do_produto}}</td>
                <td>{{$produto->codigo_interno_do_cliente}}</td>
                <td>
                  <a href="" data-toggle="modal" data-target="#modaldemo2">
                    <i class="fa fa-info-circle"></i>
                  </a> {{$produto->ncm_importado}}
                </td>
                <td>
                  <a href="" data-toggle="modal" data-target="#modaldemo2">
                    <i class="fa fa-info-circle"></i>
                  </a> {{$produto->ia_ncm}} 
                </td>
                <td style="color:red">{{$produto->acuracia}}</td>
                <td style="color:red">AUDITAR</td>
                <td>
                  <a style="color:white" href="" class="btn btn-secondary btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1"><i class="fa fa-check"></i></a>
                </td>
                <td>ST</td>
                <td>Não</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div><!-- table-responsive -->

      <div class="row justify-content-center">
        {{$produtos->appends(request()->input())->links()}}
      </div>
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
              <th scope="row">Capítulo </th>
              <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</td>
              
            </tr>
            <tr>
              <th scope="row">Posição </th>
              <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</td>
              
            </tr>
            <tr>
              <th>Subposição </th>
              <td>Outros</td>
              
            </tr>
            <tr>
              <th>Subitem </th>
              <td>Outros</td>
              
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

@endsection

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
    $('.btn-auditar-ia').on('click', function(){
      var href = $(this).data('href');

      Swal.fire({
         title: 'Atenção',
         html: 'Você deseja enviar os produtos deste lote, par que sejam auditados pela Inteligência Artificial do <strong>Guardião Tributário</strong>?<br>Esse processo será efetuado em fila e poderá demorar alguns minutos!',
         showCancelButton: true,
      }).then(function(result){
         if(result.isConfirmed)
         {
            window.location.href = href;
         }
      });
    });
  </script>
@endpush