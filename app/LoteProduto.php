<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoteProduto extends Model
{
    protected $table = 'public.lote_produto';
    protected $fillable = ['gtin', 'seu_codigo','seu_nome', 'ncm', 'origem', 'tributado_4', 'uf_origem_fk', 'possui_st', 'aliquota_icm', 'aliquota_pis', 'aliquota_cofins', 'bc_perfilcontabil_fk_id', 'estab_origem_fk_id', 'lote_fk_id', 'status_fk_id', 'trib_estab_origem_fk_id'];

    public function lote(){

        return $this->belongsTo(ClienteLote::class, 'cliente_lote');

    }
}
