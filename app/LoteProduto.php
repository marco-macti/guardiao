<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoteProduto extends Model
{
    protected $connection= 'old';
    protected $table       = 'lote_produto';
    protected $primaryKey  = 'id';
    public $timestamps     = false;
    protected $guarded    = ['id','gtin', 'seu_codigo','seu_nome', 'ncm', 'origem', 'tributado_4', 'uf_origem_fk', 'possui_st', 'aliquota_icm', 'aliquota_pis', 'aliquota_cofins', 'bc_perfilcontabil_fk_id', 'estab_origem_fk_id', 'lote_fk_id', 'status_fk_id', 'trib_estab_origem_fk_id', 'cest', 'mva'];

    public function lote(){

        return $this->belongsTo(ClienteLote::class, 'cliente_lote');

    }
}
