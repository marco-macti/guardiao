@extends('templates.guardiao')
@section('conteudo')

<div class="slim-mainpanel">
      <div class="container">
        <div class="slim-pageheader">
          <ol class="breadcrumb slim-breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="slim-pagetitle">Bem Vindo(a) ao Guardião Tributário!</h6>
        </div><!-- slim-pageheader -->
        <h1> Visão do Usuário </h1>
        @include('includes._user_cards')
        <br/>
        <h1> Visão do Admin </h1>
        @include('includes._admin_cards')

      </div><!-- container -->
    </div><!-- slim-mainpanel -->

@stop