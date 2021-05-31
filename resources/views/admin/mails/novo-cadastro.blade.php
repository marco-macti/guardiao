<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Sticky Footer Template for Bootstrap</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/sticky-footer/">

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="sticky-footer.css" rel="stylesheet">
  </head>

  <body>

    <!-- Begin page content -->
    <main role="main" class="container">
      <h1 class="mt-5">Bem vindo ao Guardiao Tributario</h1>
      <p class="lead">Ola {{ $user->name }}!</p>
      <p>Seu cadastro na plataforma Guardiao Tributario foi concluido com sucesso. Abaixo seus dados de acesso :</p>
      <strong>E-mail : {{ $user->email }} </strong>
      <br/>
      <strong>Semha  : {{ $user->senha }} </strong>
      <br/>
      <br/>
      <p>Use os dados acima para fazer login na plataforma clicando <a href="http://guardiaotributario.com.br">AQUI</a>.</p>
    </main>

    <footer class="footer">
      <div class="container">
          <br/>
          <span class="text-muted">Qualquer duvida estamos sempre a disposicao. </span>
          <br/>
          <span class="text-muted">Atte,</span>
      </div>
    </footer>
  </body>
</html>
