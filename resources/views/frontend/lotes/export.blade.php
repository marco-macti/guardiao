<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <style>
        @media print{@page {size: landscape}}
        body{
            zoom: 60%
        }
    </style>

    <title>{{ $lote->cliente->cnpj }}-{{ $lote->cliente->razao_social }}</title>
  </head>
  <body>
      <div class="col-md-12">
          <img style="width: 20%" src="https://guardiaotributario.com.br/wp-content/uploads/2019/06/guardia%CC%83o_tributario_logotipo.png">
      </div>
      <hr/>
      <div class="col-md-12">
        <h3 style="text-decoration: underline">Relatorio de Movimentacao</h3>
       </div>
       <br/>
       <div class="col-md-6">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Nome empresarial : </td>
                    <td>{{ $lote->cliente->razao_social }}</td>
                </tr>
                <tr>
                    <td>CNPJ : </td>
                    <td>{{ $lote->cliente->cnpj }}</td>
                </tr>
                <tr>
                    <td>Periodo de Apuracao :</td>
                    <td>{{ date('m/Y') }}</td>
                </tr>
            </tbody>
        </table>
       </div>
       <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Codigo Interno</th>
                    <th scope="col">EAN</th>
                    <th scope="col">NCM Cliente</th>
                    <th scope="col">NCM Auditado</th>
                    <th scope="col">Ex</th>
                    <th scope="col">Descricao</th>
                    <th scope="col">Data de Emissao</th>
                    <th scope="col">Tributação PIS/COFINS</th>
                    <th scope="col">Tributação ICMS</th>
                    <th scope="col">Nº Documento Fiscal</th>
                    <th scope="col">Modelo</th>
                    <th scope="col">CFOP</th>
                    <th scope="col">CST ICMS</th>
                    <th scope="col">CSOSN</th>
                    <th scope="col">Chave</th>
                    <th scope="col">Indicador de Operação</th>
                    <th scope="col">Situação do Documento</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Valor Desconto</th>
                    <th scope="col">Valor Frete</th>
                    <th scope="col">Valor Acréscimo</th>
                    <th scope="col">Numero Item</th>
                    <th scope="col">Grupo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lote->produtos as $produto)
                    <tr>
                        <td>{{ $produto->codigo_interno_do_cliente }}</td>
                        <td></td>
                        <td>{{ $produto->ncm_importado }}</td>
                        <td>{{ is_object($produto->auditoria) ? $produto->auditoria->ncm_auditado : '-' }}</td>
                        <td></td>
                        <td style="min-width: 200px">{{ $produto->descricao_do_produto }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
       </div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>

