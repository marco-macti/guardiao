<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCPerfilContabilCofins extends Model
{
    protected $table = 'bc_perfil_contabil_icms';
    protected $guarded = ['aliquota','cst','base_legal','inicio','fim'];
    public $timestamps  = false;
}
