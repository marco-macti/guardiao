@extends('templates.guardiao')
@section('conteudo')

<div class="slim-mainpanel">
      <div class="container">
        <div class="slim-pageheader">
          <ol class="breadcrumb slim-breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="slim-pagetitle">Bem Vindo(a) ao Guardião Tributário!</h6>
        </div><!-- slim-pageheader -->
        @if(auth()->user()->is_superuser == 'Y') 
          @include('includes._admin_cards')
        @else
          @include('includes._user_cards')
        @endif
      </div><!-- container -->
    </div><!-- slim-mainpanel -->

@stop