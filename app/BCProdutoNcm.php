<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCProdutoNcm extends Model
{
    protected $connection= 'old';
    protected $table = 'bc_produto_ncm';
    public $timestamps  = false;
    protected $fillable = ['id','inicio','fim','ncm_fk_id','bc_produto_fk_id'];
}
