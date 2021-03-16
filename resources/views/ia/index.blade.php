<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importação planilha</title>
</head>
<body>
    <form action="{{ route('importEAuditor') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="file" name="file" id="file" />
        <input type="submit" value="Enviar" />
    </form>
  
</body>
</html>