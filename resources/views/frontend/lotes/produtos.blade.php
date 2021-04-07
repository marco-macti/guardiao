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

      <div class="table-responsive">
        <table class="table mg-b-0">
          <thead>
            <tr>
              <th>Imagem </th>
              <th>Descrição</th>
              <th>Código</th>
              <th>NCM do Cliente</th>
              <th>NCM IA</th>
              <th>Acurácia</th>
              <th>Acertou?</th>
              <th>Treinar</th>
              <th>Tributação</th>
              <th>Monofásico</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <img style="width: 100px" src="{{ URL('img-default.jpeg') }}">
              </td>
              <td>Bolo Re no Pote</td>
              <td>158659</td>
              <td>1909006</td>
              <td>1908555</td>
              <td style="color:red">66%</td>
              <td style="color:red">AUDITAR</td>
              <td>
                <a style="color:white" href="" class="btn btn-secondary btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1"><i class="fa fa-check"></i></a>
              </td>
              <td>ST</td>
              <td>Não</td>
            </tr>
            <tr>
              <td>
                <img style="width: 100px" src="{{ URL('img-default.jpeg') }}">
              </td>
              <td>Cup Cake Unidade</td>
              <td>158659</td>
              <td>1909006</td>
              <td>1908555</td>
              <td style="color:green">96%</td>
              <td style="color:green">NCM CORRETO</td>
              <td>
                <a style="color:white" href="" class="btn btn-secondary btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1"><i class="fa fa-check"></i></a>
              </td>
              <td>ST</td>
              <td>Não</td>
            </tr>
          </tbody>
        </table>
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

@stop