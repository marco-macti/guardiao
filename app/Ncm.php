<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ncm extends Model
{
    protected $table = 'ncm';
    public $timestamps  = false;
    protected $fillable = ['cod_ncm'];
}
