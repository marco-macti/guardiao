<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ncm extends Model
{
    protected $connection= 'old';
    protected $table      = 'ncm';
    protected $primaryKey = 'cod_ncm';
    public $timestamps    = false;
    protected $fillable   = ['cod_ncm','descricao', 'dt_inicio_vigencia'];
}
