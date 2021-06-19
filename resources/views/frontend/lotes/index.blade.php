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

    <div class="card card-dash-one mg-t-20">
        <div class="row no-gutters">
            <div class="col-lg-3">
            <i class="icon ion-ios-analytics-outline"></i>
            <div class="dash-content">
                <label class="tx-primary">Total de produtos Importados</label>
                <h2>{{ $totalProdutosImportados }} </h2>
            </div><!-- dash-content -->
            </div><!-- col-3 -->
            <div class="col-lg-3">
            <i class="icon ion-ios-pie-outline"></i>
            <div class="dash-content">
                <label class="tx-success">Total de Produtos Auditados</label>
                <h2>{{ $totalDeProdutosAuditados }}</h2>
            </div><!-- dash-content -->
            </div><!-- col-3 -->
            <div class="col-lg-3">
            <i class="icon ion-star"></i>
            <div class="dash-content">
                <label class="tx-purple">Quantidade de Acertos</label>
                <h2>{{ $totalDeProdutosCorretos }}</h2>
            </div><!-- dash-content -->
            </div><!-- col-3 -->
            <div class="col-lg-3">
            <i class="icon ion-close"></i>
            <div class="dash-content">
                <label class="tx-danger">Quantidade de erros</label>
                <h2>{{ $totalDeProdutosIncorretos }}</h2>
            </div><!-- dash-content -->
            </div><!-- col-3 -->
        </div><!-- row -->
    </div>
    <br/>

    <!-- Filtros -->


    <div style="display: none" id="accordion" class="accordion-one" role="tablist" aria-multiselectable="true">
        <div class="card">
          <div class="card-header" role="tab" id="headingOne">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="tx-gray-800 transition collapsed">
              Filtro dos Lotes
            </a>
          </div><!-- card-header -->

          <div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne" style="">
            <div class="card-body">
                <div class="section-wrapper">
                    <div class="form-layout">
                      <div class="row mg-b-25">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Codigo interno do Cliente:</label>
                                <input class="form-control" type="text" name="codigo_interno_do_cliente" value="" >
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">NCM do Cliente:</label>
                                <input class="form-control" type="text" name="ncm_importado" value="" >
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">NCM da IA:</label>
                                <input class="form-control" type="text" name="ncm_importado" value="" >
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-4">
                          <div class="form-group mg-b-10-force">
                            <label class="form-control-label">Acurácia</label>
                            <select class="form-control select2 select2-hidden-accessible" data-placeholder="Choose country" tabindex="-1" aria-hidden="true">
                              <option label="Choose country"></option>
                              <option value="1"><= 80%</option>
                              <option value="2">United Kingdom</option>

                              <option value="Japan">Japan</option>
                            </select>
                            <span class="select2 select2-container select2-container--default select2-container--above" dir="ltr" style="width: 323px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-8ee4-container"><span class="select2-selection__rendered" id="select2-8ee4-container"><span class="select2-selection__placeholder">Choose country</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                          </div>
                        </div><!-- col-8 -->

                      </div><!-- row -->

                      <div class="form-layout-footer">
                        <button class="btn btn-primary bd-0">Submit Form</button>
                        <button class="btn btn-secondary bd-0">Cancel</button>
                      </div><!-- form-layout-footer -->
                    </div><!-- form-layout -->
                  </div>
            </div>
          </div>
        </div>

      </div>



    <!-- Fim Filtros -->

    <br/>
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
              <th>Quantidade de Acertos</th>
              <th>Quantidade de Erros</th>
              <th>Tipo do Documento</th>
              <th>Competência ou Numeração</th>
              <th>Status da Importação </th>
              <th>Opções</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($lotes as $lote)
              <tr>
                <th scope="row">{{ $lote->numero_do_lote }}</th>
                <td>{{ $lote->created_at->format('d/m/Y') }}</td>
                <td>{{ $lote->quantidade_de_produtos }}</td>
                <td>{{ $lote->totalAcertos() }} </td>
                <td>{{ $lote->totalErros() }} </td>
                <td>{{ $lote->tipo_documento }}</td>
                <td>{{ $lote->competencia_ou_numeracao }}</td>
                <td>
                    <span class="badge badge-{{ $lote->statusImport()['class'] }}"> <i class="{{ $lote->statusImport()['icon'] }}"></i>  {{ $lote->statusImport()['status'] }} </span>

                </td>
                <td>
                  <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        @if($lote->statusImport()['status'] == 'Importando')
                            <a title="Visualizar produtos deste lote" href="#" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye lote-em-importacao"></i></a>
                        @else
                            <a title="Visualizar produtos deste lote" href="{{ URL("/lotes/$lote->id/edit") }}" style="color:white" class="btn btn-secondary active"><i class="fa fa-eye"></i></a>
                        @endif

                        @if($lote->totalAuditados() > 0)
                            <a href="#" title="Exportar lista de produtos ja auditados" style="color:white" class="btn btn-secondary active"><i class="fa fa-download"></i></a>
                        @endif

                      <a style="color:white" class="btn btn-secondary"><i class="fa fa-remove"></i></a>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr colspan="6">Nenhum lote importado</tr>
            @endforelse
          </tbody>
        </table>

        {{ $lotes->links() }}
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
            <select class="tipo_arquivo form-control">
              <option>[-SELECIONE-]</option>
              <option value="SPEED">Speed Fiscal</option>
              <option style="display:none" value="SINTEGRA">Sintegra</option>
              <option value="CSV">Arquivo CSV</option>
              <option value="NFXML">Nota Fiscal XML </option>
            </select>

            <br/>

            <form method="POST" action="/lotes" enctype="multipart/form-data" id="dropzone" class="dropzone">

              @csrf

              <input type="hidden" class="tipo_arquivo_dropzone" name="tipo_arquivo" value="">

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

                <label>Tipo de Arquivo : </label>

                <select class="tipo_arquivo form-control">
                  <option>[-SELECIONE-]</option>
                  <option value="SPEED">Speed Fiscal</option>
                  <option style="display:none" value="SINTEGRA">Sintegra</option>
                  <option value="CSV">Arquivo CSV</option>
                  <option value="NFXML">Nota Fiscal XML </option>
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

        $(".lote-em-importacao").click(function(){
            Swal.fire({
                icon: 'info',
                title: 'Aguarde',
                text: 'Aguarde enquanto a Inteligencia Artificial audita os seus produtos. Este processo pode levar ate 2 horas',
                footer: "<img width='200px' src='https://guardiaotributario.com.br/wp-content/uploads/2019/06/guardia%CC%83o_tributario_logotipo.png'>"
            })
        })

        $(".tipo_arquivo").change(function(){

          var tipo = $(this).find(":selected").val();

          $(".tipo_arquivo_dropzone").val(tipo);

        });

        $("#dropzone").dropzone({
            maxFiles: 1,
            url: "/lotes",
            sending: function(file, response){
              $("#pre-loader").hide();
              $("#loader").show();
              $("#frm-dropzone").submit();
            },
            success: function(file, response){

              if(response.success){

                $("#msg-upload").html(response.msg);

                console.log(response.url_redirect);

                /*setTimeout(() => {
                  location.href = response.url_redirect;
                }, 2000);*/

              }else{

                $("#msg-upload").html(response.msg);

              }

            }
        });

    });


  </script>

@endpush


@stop
