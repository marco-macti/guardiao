<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClienteLote extends Model
{
    protected $table    = 'cliente_lote';
    public $timestamps  = false;
    protected $fillable = ['num_lote_cliente', 'data_lote', 'anotacoes', 'cliente_fk_id', 'cliente_lote_status_fk_id', 'dados'];

    public function cliente(){

        return $this->belongsTo(Cliente::class, 'cliente_fk_id','id');

    }

    public function produtos(){

        return $this->hasMany(LoteProduto::class, 'lote_fk_id','id');
    }


}
