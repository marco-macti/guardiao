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

    public function statusImport(){

        $status = ['icon' => 'fa fa-download','status' => 'Importando', 'color' => 'gray'];

        $qtdProdutosImportados = LoteProduto::where('lote_id',$this->id)->count();

        if($qtdProdutosImportados == $this->quantidade_de_produtos ){
            $status['icon']   = 'fa fa-check';
            $status['status'] = 'Finalizada';
            $status['color']  = 'green';
        }

        return $status;
    }
}
