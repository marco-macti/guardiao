@extends('templates.guardiao')
@section('conteudo')

<div class="slim-mainpanel">
  <div class="container">
    <div class="slim-pageheader">
      <ol class="breadcrumb slim-breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
      </ol>
      <h6 class="slim-pagetitle">Lotes de produtos</h6>
    </div><!-- slim-pageheader -->

    <div class="section-wrapper">
      <div class="row row-sm mg-t-20">
        <div class="col-md-6">
          <label class="section-title">Meus lotes importados</label>
          <p class="mg-b-20 mg-sm-b-40">Aqui você tem a listagem completa dos lotes importados.</p>
        </div>
        <div class="col-md-6">
          <a href="" class="btn btn-primary btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1">Novo Lote</a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table mg-b-0">
          <thead>
            <tr>
              <th>Número deste lote</th>
              <th>Data de Criação</th>
              <th>Quantidade de Produtos</th>
              <th>Tipo do Documento</th>
              <th>Competência ou Numeração</th>
              <th>Opções</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">1</th>
              <td>23/01/2021</td>
              <td>3</td>
              <td>NFE</td>
              <td>NFe31181219231827000215550010000095291565130779-nfe</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ URL('/lotes/1/edit') }}" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye"></i></a>
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-remove"></i></a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <th scope="row">1</th>
              <td>23/01/2021</td>
              <td>3</td>
              <td>SPEED</td>
              <td>03/21</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ URL('/lotes/1/edit') }}" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye"></i></a>
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-remove"></i></a>
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
        <h5 class="lh-3 mg-b-20"><a href="" class="tx-inverse hover-primary">Importando seus arquivos de lotes</a></h5>
        <p class="mg-b-5">São permitidos para informar produtos do lote , arquivos oficiais do tipo Speed Fiscal ( .txt ), Sintegra ( .txt ) e Notas Fiscais de Produtos ( .xml ). </p>
        <form action="/lote/1/upload-planilha" id="dropzone" class="dropzone">
          <div class="fallback">
            <input name="file" type="file" multiple />
          </div>
        </form>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div><!-- modal-dialog -->
</div>

@push('post-scripts')

  <!-- https://www.dropzonejs.com/#usage -->

  <script type="text/javascript">

    $(function() {
      
      var myDropzone = new Dropzone("#dropzone");

      myDropzone.on("addedfile", function(file) {

        alert("Arquivo inputado.")
        
      });

  })

  </script>  

@endpush


@stop