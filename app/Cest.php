<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cest extends Model
{
    protected $table      = 'cest';
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $fillable   = ['id','denominacao'];
}
