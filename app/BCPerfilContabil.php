<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCPerfilContabil extends Model
{
    protected $primaryKey = 'id';
    protected $table      = 'bc_perfil_contabil';
    public $timestamps    = false;
    protected $fillable   = [
    	'dt_ult_atualizacao',
		'origem',
		'tributado_4',
		'operacao',
		'uf_origem_fk',
		'uf_dest_fk',
		'cnae_classe_fk_id',
		'dest_mercadoria_fk_id',
		'estab_origem_fk_id',
		'estab_dest_fk_id',
		'ncm_fk_id',
		'trib_estab_destino_fk_id',
		'trib_estab_origem_fk_id',
		'pendencia',
		'id_operacao',
		'id_produto'];

}
