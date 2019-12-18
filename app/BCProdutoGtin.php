<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCProdutoGtin extends Model
{
    protected $table = 'bc_produto_gtin';
    public $timestamps  = false;
    protected $fillable = ['gtin','bc_produto_fk_id'];
}
