<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCPerfilContabilCofins extends Model
{
    protected $connection= 'old';
    protected $table    = 'bc_perfilcontabil_cofins';
    protected $guarded  = ['aliquota','cst','base_legal','inicio','fim','bc_perfil_contabil_fk_id'];
    public $timestamps  = false;
}
