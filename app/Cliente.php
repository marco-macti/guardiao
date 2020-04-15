<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $table = 'cliente';

    public function lotes(){

    	return ClienteLote::where('cliente_fk_id',$this->id)->get();
    	
    }
}
