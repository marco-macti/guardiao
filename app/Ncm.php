<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ncm extends Model
{
    protected $table      = 'ncm';
    protected $primaryKey = 'cod_ncm';
    public $timestamps    = false;
    protected $fillable   = ['cod_ncm','descricao'];
}
