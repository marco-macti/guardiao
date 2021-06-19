<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class Lote extends Model
{
    protected $table    = 'lotes';
    protected $guarded = [];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function statusImport(){

        $status = ['icon' => 'fa fa-download','status' => 'Importando', 'class' => 'warning'];

        $qtdProdutosImportados = LoteProduto::where('lote_id',$this->id)->count();

        if($qtdProdutosImportados == $this->quantidade_de_produtos ){
            $status['icon']   = 'fa fa-check';
            $status['status'] = 'Finalizada';
            $status['class']  = 'success';
        }

        return $status;
    }

    public function totalAcertos(){

        if($this->statusImport() == 'Importando'){
            return 'N/A';
        }else{

            $acertos =  DB::select("SELECT COUNT(*) as acertos FROM lote_produtos WHERE ncm_importado = ia_ncm AND lote_id = $this->id");

            return $acertos[0]->acertos;
        }



    }

    public function totalErros(){

        if($this->statusImport() == 'Importando'){
            return 'N/A';
        }else{

            $erros = DB::select("SELECT COUNT(*) as erros FROM lote_produtos
                                  WHERE ncm_importado <> ia_ncm
                                 AND lote_id = $this->id");


            return $erros[0]->erros;

        }
    }

    public function totalAuditados(){

        return LoteProdutoAuditoria::where('lote_id',$this->id)->count();
    }
}
