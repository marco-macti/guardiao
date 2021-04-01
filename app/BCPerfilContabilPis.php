<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCPerfilContabilPis extends Model
{
    protected $connection= 'old';
	protected $primaryKey = 'id';
    protected $table      = 'bc_perfilcontabil_pis';
    protected $guarded    = ['aliquota','cst','base_legal','inicio','fim','bc_perfil_contabil_fk_id'];
    public $timestamps    = false;
}
