@extends('templates.guardiao')

@push('post-scripts')
    <script>
      $(document).ready(function(){
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
      });

      $('#cadastrar-usuario').on('click', function(){
          $('#exampleModalLong').modal('show');

          return false;
      });

      $('.editar-usuario').on('click', function(){

          var id = $(this).data('id');
          var url = '{{route("admin.clientes.infoUser")}}';

          $.ajax({
              url: url,
              data: {
                  id: id
              },
              success: function(result){
                  var dados = result.user;
                  $('#editar-nome').empty();
                  $('#editar-nome').val(dados.name);

                  $('#editar-email').empty();
                  $('#editar-email').val(dados.email);
                  $('#editar-nome').val(dados.name);

                  $('#user-id').empty();
                  $('#user-id').val(dados.id);
              },
              error: function(){
                  console.log("error");
              }
          });
          $('#editarModal').modal('show');

          return false;
      });
    </script>
@endpush

@section('conteudo')

    <div class="slim-mainpanel">
        <div class="container">
            <div class="slim-pageheader">
                <ol class="breadcrumb slim-breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.clientes.index')}}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
                <h6 class="slim-pagetitle">{{$cliente->razao_social}}</h6>
            </div><!-- slim-pageheader -->
            <div class="section-wrapper">
                <div class="row row-sm mg-t-20">
                  <div class="col-md-6">
                    <label class="section-title">Dados Cadastrais</label>
                  </div>
                  <div class="col-md-6">
                    <a href="{{ route('admin.clientes.edit',$id) }}" class="btn btn-primary btn-block mg-b-10 col-3 pull-right">Editar</a>
                  </div>
                </div>
                <div class="row row-sm mg-t-20">
                    <div class="col-6">
                        <span>Razão Social: {{$cliente->razao_social}}</span><br>
                        <span>Nome Fantasia: {{$cliente->nome_fantasia}}</span><br>
                        <span>CNPJ: <span class="cnpj">{{$cliente->cnpj}}</span></span><br>
                        <span>Data de Abertura: <span>{{date('d/m/Y', strtotime($cliente->dt_nascimento))}}</span></span><br>
                        <span>Operação: {{$cliente->operacao}}</span><br>
                        <span>Endereço: {{$cliente->endereco .','
                                            . $cliente->numero .','
                                            .$cliente->bairro
                                            .($cliente->complemento?', '.$cliente->endereco:'')
                                            .', '.$cliente->cidade
                                            .' - '.$cliente->estado }}</span><br>
                        <span>Telefone:  {{$cliente->tel1 . ($cliente->tel2?', '.$cliente->tel2:'')}}</span><br>
                        <span>Email:  {{$cliente->email_cliente}}</span>
                    </div>
                    <div class="col-6">
                        <span>Lotes: {{$lotes->count()}}</span><br>
                        <span>Usuarios: {{$usuarios->count()}}</span><br>
                    </div>
                </div>
            </div>
            <div class="section-wrapper mt-4">
                <div class="row row-sm mg-t-20">
                  <div class="col-md-6">
                    <label class="section-title">Usuários</label>
                    <p class="mg-b-20 mg-sm-b-40">Lista de usuários ligados a empresa.</p>
                  </div>
                  <div class="col-md-6">
                    <a id="cadastrar-usuario" href="#" class="btn btn-primary btn-block mg-b-10 col-3 pull-right">Cadastrar</a>
                  </div>
                </div>
                <div class="table-responsive">
                    <table class="table mg-b-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td>{{$usuario->name}}</td>
                                    <td>{{$usuario->email}}</td>
                                    <td class="text-right">
                                        <a href="#" class="btn btn-sm btn-primary editar-usuario" data-id="{{encrypt($usuario->id)}}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{route('admin.clientes.removeUser', encrypt($usuario->id))}}" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="section-wrapper mt-4">
                <div class="row row-sm mg-t-20">
                  <div class="col-md-6">
                    <label class="section-title">Lotes</label>
                    <p class="mg-b-20 mg-sm-b-40">Lista de lotes ligados a empresa.</p>
                  </div>
                </div>
                <div class="table-responsive">
                    <table class="table mg-b-0">
                        <thead>
                            <tr>
                                <th>Nº Lote</th>
                                <th>Data de Crição</th>
                                <th>Quantidade</th>
                                <th>Tipo de Documento</th>
                                <th>Competência</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lotes as $lote)
                                <tr>
                                    <td>{{$lote->id}}</td>
                                    <td>{{$lote->created_at->format('d/m/Y')}}</td>
                                    <td>{{$lote->quantidade_de_produtos}}</td>
                                    <td>{{$lote->tipo_documento}}</td>
                                    <td>{{$lote->competencia_ou_numeracao}}</td>
                                    <td class="text-right">
                                        <a href="{{route('admin.lotes.edit', $lote->id)}}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-center">
                    {{$lotes->appends(['modulo' => 'lote'])->links()}}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade " id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{route('admin.clientes.adduser')}}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Cadastrar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {!! Form::hidden('dados[cliente_id]', $cliente->id) !!}
                        <div class="form-group col-12">
                            {!! Form::label('nome', 'Nome', ['class' => '']) !!}
                            {!! Form::text('dados[name]', '', ['class' => 'form-control', 'id' => 'nome']) !!}
                        </div>
                        <div class="form-group col-12">
                            {!! Form::label('email', 'Email', ['class' => '']) !!}
                            {!! Form::text('dados[email]', '', ['class' => 'form-control', 'id' => 'email']) !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade " id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{route('admin.clientes.edituser')}}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarModalTitle">Editar Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {!! Form::hidden('dados[user_id]', '', ['id' => 'user-id']) !!}
                        <div class="form-group col-12">
                            {!! Form::label('nome', 'Nome', ['class' => '']) !!}
                            {!! Form::text('dados[name]', '', ['class' => 'form-control', 'id' => 'editar-nome']) !!}
                        </div>
                        <div class="form-group col-12">
                            {!! Form::label('email', 'Email', ['class' => '']) !!}
                            {!! Form::text('dados[email]', '', ['class' => 'form-control', 'id' => 'editar-email']) !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
