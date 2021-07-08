<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Guardião Tributário - Importação Finalizada </title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/sticky-footer/">

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="sticky-footer.css" rel="stylesheet">
  </head>

  <body>

    <main role="main" class="container">

        <h1 class="mt-5">Olá! Seu lote acabou de ser importado.</h1>
        <p>Abaixo os dados da Importação</p>

        <div class="row">
            <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Número do Lote </th>
                    <th scope="col">Quantidade de Produtos </th>
                    <th scope="col">Tipo do Documento </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>{{ $lote->numero_do_lote }}</th>
                    <th>{{ $lote->quantidade_de_produtos }}</th>
                    <th>{{ $lote->tipo_documento }}</th>
                  </tr>
                </tbody>
              </table>

        </div>

      <p>Para acessar os produtos deste lote pelo sistema, clique <a href="http://app.guardiaotributario.com.br/lotes/{{$lote->id}}/edit">AQUI</a>.</p>

    </main>

    <footer class="footer">
      <div class="container">
          <br/>
          <span class="text-muted">Qualquer duvida estamos sempre a disposicao. </span>
          <br/>
          <span class="text-muted">Att,</span>
      </div>
    </footer>
  </body>
</html>
