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
              <th>Número de Controle Interno</th>
              <th>Descrição</th>
              <th>NCM Importado</th>
              <th>NCM IA</th>
              <th>Opções</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th><img style="width: 100px" src="{{ URL('img-default.jpeg') }}"></th>
              <th>158863</th>
              <td>Detergente Minuano</td>
              <td>929069</td>
              <td>929059</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th><img style="width: 100px" src="{{ URL('img-default.jpeg') }}"></th>
              <th>158863</th>
              <td>Água Oxigendada Oxygen</td>
              <td>929069</td>
              <td>929059</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th><img style="width: 100px" src="{{ URL('img-default.jpeg') }}"></th>
              <th>158863</th>
              <td>Bucha Higiênica</td>
              <td>929069</td>
              <td>929059</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- table-responsive -->
    </div>

  </div><!-- container -->
</div><!-- slim-mainpanel -->

@stop