<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BCProduto extends Model
{
    protected $connection= 'old';
	protected $primaryKey = 'id';
    protected $table      = 'bc_produto';
    public $timestamps    = false;
    protected $fillable   = ['status','nome','descricao','preco_medio','preco_maximo','thumbnail','altura','largura','comprimento','peso_liquido','cest_fk_id','gpc_fk_id','ncm_fk_id'];

}
