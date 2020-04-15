<form action="{{ URL('/iob/import-sheet') }}" enctype="multipart/form-data" method="POST">
    {{ csrf_field() }}
    <label>Upload Planilha:</label><br/>
    <input type="file" name="sheet"><br/>
    <button type="submit">Enviar</button>
</form>
