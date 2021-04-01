<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCPerfilContabilIcms extends Model
{
    protected $connection= 'old';
    protected $table = 'bc_perfil_contabil_icms';
    protected $guarded = ['aliquota','possui_st','base_legal_st','inicio','fim','bc_perfil_contabil_fk_id'];
    public $timestamps  = false;
}
