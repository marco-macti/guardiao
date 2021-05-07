<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteProduto extends Model
{
    protected $table       = 'lote_produtos';
    protected $guarded     = [];

    protected $fillable = [
        'lote_id',
        'codigo_interno_do_cliente',
        'descricao_do_produto',
        'ncm_importado',
        'ia_ncm',
        'acuracia'
    ];
}
