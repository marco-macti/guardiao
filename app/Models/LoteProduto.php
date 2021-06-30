<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteProduto extends Model
{
    protected $table       = 'lote_produtos';
    protected $guarded     = [];

    public function auditoria()
    {
        return $this->hasOne(LoteProdutoAuditoria::class, 'lote_produto_id','id');
    }

    public function auditado(){

        $auditado =  LoteProdutoAuditoria::where('lote_id',$this->lote_id)
                                    ->where('lote_produto_id',$this->id)
                                    ->first();

        return is_object($auditado) ? true : false;


    }

    public function preAuditado(){

        $auditado =  LoteProdutoAuditoria::where('lote_id',$this->lote_id)
                                    ->where('lote_produto_id',$this->id)
                                    ->where('pre_auditado','S')
                                    ->first();

        return is_object($auditado) ? 1 : 0;


    }
}
