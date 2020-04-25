<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCPerfilContabilPis extends Model
{
    protected $table = 'bc_perfilcontabil_pis';
    protected $guarded = ['aliquota','cst','base_legal','inicio','fim'];
    public $timestamps  = false;
}
