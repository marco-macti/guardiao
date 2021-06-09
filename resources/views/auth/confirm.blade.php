@extends('templates.guardiao')
@section('conteudo')

<div class="slim-mainpanel">
      <div class="container">
        <div class="slim-pageheader">
          <ol class="breadcrumb slim-breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="slim-pagetitle">Atualize sua senha </h6>
        </div><!-- slim-pageheader -->
        <div class="container">
            <form method="POST" action="{{ route('atualizar.senha') }}">

                @csrf

                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <div class="form-group">

                    <input placeholder="Digite sua nova senha" id="senha" type="password" class="form-control @error('senha') is-invalid @enderror" name="senha" value="{{ old('senha') }}" required autocomplete="off" autofocus>

                    @error('senha')
                        <small class="invalid-feedback small-alert" role="alert">
                            <strong>{{ $message }}</strong>
                        </small>
                    @enderror

                </div><!-- form-group -->

                <div class="form-group">

                    <input placeholder="Repita sua nova senha" id="confirme_sua_senha" type="password" class="form-control @error('confirme_sua_senha') is-invalid @enderror" name="confirme_sua_senha" value="{{ old('confirme_sua_senha') }}" required autocomplete="off" autofocus>

                    @error('confirme_sua_senha')
                        <small class="invalid-feedback small-alert" role="alert">
                            <strong>{{ $message }}</strong>
                        </small>
                    @enderror

                </div><!-- form-group -->

                <button type="submit" class="btn btn-primary btn-block btn-signin">Atualizar</button>

            </form>
        </div>

      </div><!-- container -->
    </div><!-- slim-mainpanel -->

@stop

