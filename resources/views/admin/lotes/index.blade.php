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
          <label class="section-title">Lotes de Clientes</label>
          <p class="mg-b-20 mg-sm-b-40">Aqui você tem a listagem completa dos lotes dos clientes importados.</p>
        </div>
        <div class="col-md-6">
          <a href="" class="btn btn-primary btn-block mg-b-10" data-toggle="modal" data-target="#modaldemo1">Novo Lote</a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table mg-b-0">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>CNPJ</th>
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
              <td>Green Signal Softwares</td>
              <td>29.692.093/0001-44</td>
              <th scope="row">25</th>
              <td>23/01/2021</td>
              <td>3</td>
              <td>NFE</td>
              <td>0025988585584984984</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ URL('admin/lotes/1/edit') }}" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye"></i></a>
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-remove"></i></a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>Supermercados Supermir LTDA</td>
              <td>29.691.052/0001-44</td>
              <th scope="row">1</th>
              <td>23/01/2021</td>
              <td>2500</td>
              <td>SPEED</td>
              <td>01/20</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ URL('admin/lotes/1/edit') }}" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye"></i></a>
                    <a style="color:white" class="btn btn-secondary"><i class="fa fa-remove"></i></a>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>EPA</td>
              <td>21.005.589/0051-44</td>
              <th scope="row">1</th>
              <td>23/01/2021</td>
              <td>1680</td>
              <td>SINTEGRA</td>
              <td>03/19</td>
              <td>
                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ URL('admin/lotes/1/edit') }}" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye"></i></a>
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

        <div id="pre-loader">

          <h5 class="lh-3 mg-b-20"><a href="" class="tx-inverse hover-primary">Importando seus arquivos de lotes</a></h5>
          <p class="mg-b-5">São permitidos para informar produtos do lote , arquivos oficiais do tipo Speed Fiscal ( .txt ), Sintegra ( .txt ) e Notas Fiscais de Produtos ( .xml ). </p>
            
            <br/>

            <label>Tipo de Arquivo : </label>
            <select class="tipo_arquivo form-control ">
              <option>[-SELECIONE-]</option>
              <option value="SPEED">Speed Fiscal</option>  
              <option style="display:none" value="SINTEGRA">Sintegra</option>  
              <option value="CSV">Arquivo CSV</option>  
              <option value="NFXML">Nota Fiscal XML </option>  
            </select>

            <br/>

            <label>Cliente : </label>
            <select class="cliente_id form-control">
              <option>[-SELECIONE-]</option>
              @foreach ($clientes as $cliente )
                <option value="{{ $cliente->id }}">{{ $cliente->cnpj }} - {{ $cliente->nome_fantasia }}</option>  
              @endforeach
            </select>

            <br/>

            <form method="POST" action="/lotes" enctype="multipart/form-data" id="dropzone" class="dropzone">

              @csrf

              <input type="hidden" class="tipo_arquivo_dropzone" name="tipo_arquivo" value="">
              <input type="hidden" class="cliente_id_dropzone" name="cliente_id" value="">

              <div class="fallback">
                <input name="file" type="file" multiple />
              </div>
            </form>

            <div style="display: none">

              <br/>
              <hr/>
              <br/>

              <form method="POST" action="/lotes" enctype="multipart/form-data" >

                @csrf

                <input type="hidden" class="tipo_arquivo_dropzone" name="tipo_arquivo" value="">
                <input type="hidden" class="cliente_id_dropzone" name="cliente_id" value="">

                <label>Tipo de Arquivo : </label>
                
                <select class="tipo_arquivo form-control">
                  <option>[-SELECIONE-]</option>
                  <option value="SPEED">Speed Fiscal</option>  
                  <option style="display:none" value="SINTEGRA">Sintegra</option>  
                  <option value="CSV">Arquivo CSV</option>  
                  <option value="NFXML">Nota Fiscal XML </option>  
                </select>

                <br/>

                <label>Cliente : </label>
                <select class="cliente_id form-control">
                  <option>[-SELECIONE-]</option>
                  @foreach ($clientes as $cliente )
                    <option value="{{ $cliente->id }}">{{ $cliente->cnpj }} - {{ $cliente->nome_fantasia }}</option>  
                  @endforeach
                </select>

                <br/>

                <div class="fallback">
                  <input class="form-control" name="file" type="file" multiple />
                </div>

                <br/>

                <button class="btn btn-primary" type="submit">Enviar</button>

              </form>

            </div>
            
            <div style="display: none" class="modal-footer">
              <button type="button" class="btn btn-primary">Save changes</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>


        <div id="loader" style="text-align: center;display:none">

          <img style="width: 200px" src="{{ URL('img/loader.gif') }}">
          <h6 id="msg-upload" class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Aguarde enquanto os produtos são importados.</h6>
          <br/>
          <br/>
  
        </div>

      </div>

    </div>
  </div><!-- modal-dialog -->
</div>

@push('post-scripts')

  <script type="text/javascript">

    $(document).ready(function() {

        $(".tipo_arquivo").change(function(){

          var tipo = $(this).find(":selected").val();

          $(".tipo_arquivo_dropzone").val(tipo);

        });

        $(".cliente_id").change(function(){

          var tipo = $(this).find(":selected").val();

          $(".cliente_id_dropzone").val(tipo);

        });

        $("#dropzone").dropzone({
            maxFiles: 1,
            url: "/admin/lotes",
            sending: function(file, response){
              $("#pre-loader").hide();
              $("#loader").show();
              $("#frm-dropzone").submit();
            },
            success: function(file, response){

              if(response.success){
                
                $("#msg-upload").html(response.msg);

                setTimeout(() => {
                  location.href = response.url_redirect;
                }, 2000);

              }else{

                $("#msg-upload").html(response.msg);

              }
              
            }
        });
     
    });


  </script>  

@endpush
@stop