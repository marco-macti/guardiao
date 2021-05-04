<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Lote extends Model
{
    protected $table    = 'lotes';
    protected $guarded = [];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }
}
