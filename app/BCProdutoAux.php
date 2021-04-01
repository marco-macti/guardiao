<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class   BCProdutoAux extends Model
{
    protected $connection= 'old';
    protected $table = 'bc_produto_aux';
}
