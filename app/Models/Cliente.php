<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $guarded = [];

    protected $fillable = [
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cnpj',
        'nome_fantasia',
        'razao_social',
        'inscricao_estadual',
        'ativo',
        'nome_do_responsavel',
        'tel1',
        'tel2',
        'email_cliente',
        'operacao',
        'dt_nascimento',
        'estado_destino'
    ];
}
