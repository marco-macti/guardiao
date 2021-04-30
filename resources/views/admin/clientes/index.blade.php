@extends('templates.guardiao')

@push('post-scripts')
    <script>
      $(document).ready(function(){
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
      });
    </script>
@endpush

@section('conteudo')

  <div class="slim-mainpanel">
      <div class="container">
      <div class="slim-pageheader">
          <ol class="breadcrumb slim-breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Clientes</li>
          </ol>
          <h6 class="slim-pagetitle">Lista de Clientes</h6>
      </div><!-- slim-pageheader -->
      <div class="section-wrapper">
          <div class="row row-sm mg-t-20">
            <div class="col-md-6">
              <label class="section-title">Clientes</label>
              <p class="mg-b-20 mg-sm-b-40">Aqui você tem a listagem completa de clientes cadastrados.</p>
            </div>
            <div class="col-md-6">
              <a href="{{route('admin.clientes.formulario')}}" class="btn btn-primary btn-block mg-b-10">Novo Cliente</a>
            </div>
          </div>
    
          <div class="table-responsive">
            <table class="table mg-b-0">
              <thead>
                <tr>
                  <th>CNPJ</th>
                  <th>Razao Social</th>
                  <th>Contato</th>
                  <th>Email</th>
                  <th>Estado</th>
                  <th></th>
                </tr>
              </thead>
              @foreach ($clientes as $cliente)
                <tbody>
                  <td class="text-nowrap cnpj">{{$cliente->cnpj}}</td>
                  <td>{{$cliente->razao_social}}</td>
                  <td>{{$cliente->razao_social}}</td>
                  <td>{{$cliente->email_cliente}}</td>
                  <td>{{$cliente->estado}}</td>
                  <td>
                    <a href="{{route('admin.clientes.detalhes', encrypt($cliente->id))}}" class="btn btn-sm btn-primary">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="" class="btn btn-sm btn-danger excluir-cliente">
                      <i class="fa fa-trash"></i>
                    </a>
                  </td>
                </tbody>
              @endforeach
            </table>
            <div class="text-center">
              {{$clientes->links()}}
            </div>
          </div>
      </div>
  </div>
  <div id="modaldemo1" class="modal fade">
    <div class="modal-dialog modal-dialog-vertical-center" role="document">
      <div class="modal-content bd-0 tx-14">
  
          <div class="modal-header">
            <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Cadastro de Cliente</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body pd-25">
            <form action="">
              <div class="row">
                <div class="form-group col">
                    {!! Form::label("cnpj", "CNPJ") !!}
                    {!! Form::text("cnpj", "", ["class" => "form-control form-control-sm cnpj"]) !!}
                </div>
                <div class="form-group col">
                    {!! Form::label("razao_social", "Razão Social") !!}
                    {!! Form::text("razao_social", "", ["class" => "form-control form-control-sm"]) !!}
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="btn btn-sm btn-default" data-dismiss="modal">Cancelar</button>
            <button class="btn btn-sm btn-primary">Cadastrar</button>
          </div>
        </div>
  
      </div>
    </div><!-- modal-dialog -->
  </div>

@endsection