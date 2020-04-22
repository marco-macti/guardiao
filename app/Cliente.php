<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $guarded = [];

    public function lotes(){

    	return ClienteLote::where('cliente_fk_id',$this->id)
    			            ->orderBy('num_lote_cliente','ASC')
    	                    ->get();

    }
}
