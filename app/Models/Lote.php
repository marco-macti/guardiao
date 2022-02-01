<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class Lote extends Model
{
    protected $table    = 'lotes';
    protected $guarded = [];
    protected $fillable = [
        "numero_do_lote",
        "cliente_id",
        "quantidade_de_produtos",
        "tipo_documento",
        "competencia_ou_numeracao",
        "status_importacao",
        "numero_do_documento_fiscal",
        "valor_frete",
    ];

    const STATUSLOTES = [
        [
            "name" => "Importando",
            "class" => "info",
            "icon"  => "fa fa-spinner" 
        ],
        [
            "name"  => "Importado",
            "class" => "success",
            "icon"  => "fa fa-check" 
        ],
        [
            "name" => "Importado com erro",
            "class" => "warning",
            "icon"  => "fa fa-exclamation-triangle" 
        ],
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function produtos()
    {
        return $this->hasMany(LoteProduto::class, 'lote_id', 'id');
    }

    public function statusImport(){

        // $status = ['icon' => 'fa fa-download','status' => 'Importando', 'class' => 'warning'];

        // $qtdProdutosImportados = LoteProduto::where('lote_id',$this->id)->count();
        
        // if($qtdProdutosImportados >= $this->quantidade_de_produtos ){
        //     $status['icon']   = 'fa fa-check';
        //     $status['status'] = 'Finalizada';
        //     $status['class']  = 'success';
        // }

        return $this::STATUSLOTES[$this->status_importacao];
        // return $status;
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
