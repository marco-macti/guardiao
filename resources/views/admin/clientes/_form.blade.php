
<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)
    <div class="row">
        <div class="col-12 mb-2">
            <h5>Dados da Empresa</h5>
        </div>
        <div class="form-group col-4">
            {!! Form::label('cnpj', ' * CNPJ') !!}
            {!! Form::text('dados[cnpj]', $cliente->cnpj, ['class' => 'form-control form-control-sm cnpj', 'id' => 'cnpj']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('razao_social', '* Razão Social') !!}
            {!! Form::text('dados[razao_social]', $cliente->razao_social, ['class' => 'form-control form-control-sm', 'id' => 'razao_social']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('nome_fantasia', '* Nome Fantasia') !!}
            {!! Form::text('dados[nome_fantasia]', $cliente->nome_fantasia, ['class' => 'form-control form-control-sm', 'id' => 'nome_fantasia']) !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-4">
            {!! Form::label('inscricao_estadual', 'Inscrição Estadual') !!}
            {!! Form::text('dados[inscricao_estadual]', $cliente->inscricao_estadual, ['class' => 'form-control form-control-sm inscricao_estadual', 'id' => 'inscricao_estadual']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('email_cliente', ' * Email') !!}
            {!! Form::text('dados[email_cliente]', $cliente->email_cliente, ['class' => 'form-control form-control-sm', 'id' => 'email_cliente']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('operacao', 'Operação') !!}
            {!! Form::select('dados[operacao]', ['venda' => 'Venda' ], $cliente->operacao, ['class' => 'custom-select custom-select-sm', 'id' => 'operacao']) !!}
        </div>

    </div>

    <div class="row">
        <div class="form-group col-2">
            {!! Form::label('estado_origem', 'Estado Origem') !!}
            {!! Form::select('dados[estado_origem]', $estados, $cliente->estado_origem, ['class' => 'custom-select custom-select-sm', 'id' => 'estado_origem']) !!}
        </div>
        <div class="form-group col-2">
            {!! Form::label('estado_destino', 'Estado Destino') !!}
            {!! Form::select('dados[estado_destino]', $estados, $cliente->estado_destino, ['class' => 'custom-select custom-select-sm', 'id' => 'estado_destino']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('data_nascimento', 'Data de Abertura') !!}
            {!! Form::date('dados[dt_nascimento]', $cliente->dt_nascimento, ['class' => 'form-control form-control-sm', 'id' => 'data_nascimento']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('nome_do_responsavel', '* Nome do Responsável') !!}
            {!! Form::text('dados[nome_do_responsavel]', $cliente->nome_do_responsavel, ['class' => 'form-control form-control-sm', 'id' => 'nome_do_responsavel']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('tel1', '* Telefone 1') !!}
            {!! Form::text('dados[tel1]', $cliente->tel1, ['class' => 'form-control form-control-sm tel_ddd', 'id' => 'tel1']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('tel2', '* Telefone 2') !!}
            {!! Form::text('dados[tel2]', $cliente->tel2, ['class' => 'form-control form-control-sm tel_ddd', 'id' => 'tel2']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('enquadramento_tributario', 'Enquadramento Tributario') !!}
            {!! Form::select('dados[enquadramento_tributario]', ['LR' => 'Lucro Real','LP' => 'Lucro Presumido', 'SN' => 'Simples Nacional'], $cliente->enquadramento_tributario, ['class' => 'custom-select custom-select-sm', 'id' => 'enquadramento_tributario']) !!}
        </div>
    </div>

    {{-- Endereço --}}

    <div class="row">
        <div class="col-12 mb-2 mt-3">
            <h5>Dados de Endereço</h5>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-4">
            {!! Form::label('cep', '* CEP') !!}
            {!! Form::text('dados[cep]', $cliente->cep, ['class' => 'form-control form-control-sm cep', 'id' => 'cep']) !!}
        </div>
        <div class="form-group col-8">
            {!! Form::label('endereco', '* Endereço') !!}
            {!! Form::text('dados[endereco]', $cliente->endereco, ['class' => 'form-control form-control-sm', 'id' => 'endereco']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('estado', '* Estado') !!}
            {!! Form::select('dados[estado]', $estados, $cliente->estado, ['class' => 'custom-select custom-select-sm', 'id' => 'estado']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('cidade', '* Cidade') !!}
            {!! Form::text('dados[cidade]', $cliente->cidade, ['class' => 'form-control form-control-sm', 'id' => 'cidade']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('bairro', '* Bairro') !!}
            {!! Form::text('dados[bairro]', $cliente->bairro, ['class' => 'form-control form-control-sm', 'id' => 'bairro']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('numero', '* Numero') !!}
            {!! Form::text('dados[numero]', $cliente->numero, ['class' => 'form-control form-control-sm', 'id' => 'numero']) !!}
        </div>
        <div class="form-group col-4">
            {!! Form::label('complemento', 'Complemento') !!}
            {!! Form::text('dados[complemento]', $cliente->complemento, ['class' => 'form-control form-control-sm', 'id' => 'complemento']) !!}
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-2 mt-3">
            <h5>Degustação</h5>
        </div>
    </div>
    <div class="row">

        @php
            $emDegustacao = $cliente->em_degustacao == 'S' ? '' : 'disabled';
        @endphp

        <div class="form-group col-3">
            {!! Form::label('em_degustacao', 'Em degustacao') !!}
            {!! Form::select('dados[em_degustacao]', ['S' => 'Sim','N' => 'Não'], !empty($cliente->em_degustacao) ? $cliente->em_degustacao : 'N', ['class' => 'custom-select custom-select-sm', 'id' => 'em_degustacao']) !!}
        </div>

        <div class="form-group col-3">
            {!! Form::label('dt_inicio_degustacao', 'Data Inicio') !!}
            {!! Form::date('dados[dt_inicio_degustacao]', $cliente->dt_inicio_degustacao, [$emDegustacao,'class' => 'form-control form-control-sm campos-degustacao', 'id' => 'dt_inicio_degustacao']) !!}
        </div>

        <div class="form-group col-3">
            {!! Form::label('dt_inicio_degustacao', 'Data Fim') !!}
            {!! Form::date('dados[dt_fim_degustacao]', $cliente->dt_fim_degustacao, [$emDegustacao,'class' => 'form-control form-control-sm campos-degustacao', 'id' => 'dt_fim_degustacao']) !!}
        </div>

        <div class="form-group col-3">
            {!! Form::label('qtd_ncms_degustacao', 'Quantidade de NCMs') !!}
            {!! Form::number('dados[qtd_ncms_degustacao]', $cliente->qtd_ncms_degustacao, [$emDegustacao,'class' => 'form-control form-control-sm campos-degustacao', 'id' => 'qtd_ncms_degustacao']) !!}
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <button class="btn btn-sm btn-primary pull-right" type="submit">Cadastrar Cliente</button>
        </div>
    </div>
</form>
