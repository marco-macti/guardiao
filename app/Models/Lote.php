<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table    = 'lotes';
    public $timestamps  = false;
    protected $fillable = ['num_lote_cliente', 'data_lote', 'anotacoes', 'cliente_fk_id', 'cliente_lote_status_fk_id', 'dados'];

    public function cliente(){

        return $this->belongsTo(Cliente::class, 'cliente_fk_id','id');

    }

    public function produtos(){

        return $this->hasMany(LoteProduto::class, 'lote_fk_id','id')->orderBy('seu_nome','ASC');

    }

}
