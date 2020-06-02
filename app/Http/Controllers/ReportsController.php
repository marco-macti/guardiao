<?php

namespace App\Http\Controllers;

use App\BCProduto;
use App\BCProdutoGtin;
use App\BCProdutoNcm;
use App\Cliente;
use App\ClienteLote;
use App\ClienteLoteStatus;
use App\LoteProduto;
use App\LoteProdutoStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function relatorioLinear(Cliente $cliente){

    
        if(is_object($cliente)){

            // Monta o Cabeçalho da exportação

            $data  = array("COD_INTERNO;CODBARRA;DESCRIÇÃO;%ICMS_PDV;%ICMSENT;REDUCAO_BASE_ICMS;CST_ICMS;NCM;PIS_COFINS;MVA;PAUTA;CST_PIS_ENTRADA;CST_PIS_SAIDA;CST_COFINS_ENTRADA;CST_COFINS_SAIDA;PIS_NATRECEITA;SIT.ESPECIALPIS/COFINS;ALÍQUOTA SIT.ESPECIAL;TIPOSED;CEST");

            // Busca pelos lotes do Cliente com o Status EM MONITORAMENTO

            $lotesEmMonitoramento = ClienteLote::where('cliente_lote_status_fk_id',ClienteLoteStatus::EM_MONITORAMENTO)
                ->where('cliente_fk_id',$cliente->id)
                ->get();

            $produtosEmMonitoramnto = array();

            foreach ($lotesEmMonitoramento as $index => $lote) {

                if(!empty($lote->produtos)){
                    foreach ($lote->produtos as $index => $produto) {
                        // Somente se o produto estiver também com o Status em Monitoramento , ele deixa inserir no vetor.
                        if($produto->status_fk_id == LoteProdutoStatus::EM_MONITORAMENTO){
                            array_push($produtosEmMonitoramnto,$produto);
                        }
                    }
                }
            }

            foreach ($produtosEmMonitoramnto as $key => $produto) {

                // Para Obter o Código do CEST , é preciso buscar por este produto $key na base do guardião

                $bcProdutoGtin = BCProdutoGtin::where('gtin',$produto->gtin)->first();

                // Se encontrar produto no sistema

                if(is_object($bcProdutoGtin)){

                    $bcProduto = BCProduto::select('bc_produto.*','cest.mva')
                                            ->leftJoin('cest', 'bc_produto.cest_fk_id', '=', 'cest.id')
                                            ->where('bc_produto.id',$bcProdutoGtin->bc_produto_fk_id)
                                            ->first();

                    $COD_INTERNO             = $produto->seu_codigo;
                    $CODBARRA                = "'".$produto->gtin."'";
                    $DESCRICAO               = trim($produto->seu_nome);
                    $ICMS_PDV                = '';    // OK
                    $ICMSENT                 = '';    // OK
                    $REDUCAO_BASE_ICMS       = 0;     // Ok
                    $CST_ICMS                = '';    // OK
                    $NCM                     = $produto->ncm;
                    $PIS_COFINS              = '';    // Ok
                    $MVA                     = $bcProduto->mva;    
                    $PAUTA                   = '';    // Ok
                    $CST_PIS_ENTRADA         = '';    // OK
                    $CST_PIS_SAIDA           = '';    // OK
                    $CST_COFINS_ENTRADA      = '';    // OK
                    $CST_COFINS_SAIDA        = '';    // Ok
                    $PIS_NATRECEITA          = '';    // OK
                    $SIT_ESPECIAL_PIS_COFINS = '';    // Enviar em branco
                    $ALIQUOTA_SIT_ESPECIAL   = '';    // Enviar em branco
                    $TIPOSPED                = '00';  //00 – Mercadoria para revenda
                    $CEST                    = $bcProduto->cest_fk_id;


                    if(!empty($bcProduto->mva)){
                        $MVA   = $bcProduto->mva;
                        $PAUTA = '';
                    }else{
                        $MVA   = '';
                        $PAUTA = 0;
                    }                        

                    // Se produto possui ST Sim e Possui CEST ( no Guardiao)
                    //    CST_ICMS = 060
                    // Se produto posui ST SIM e Possui CEST ( no Guardiao) e aliquota vazia
                    //    CST_ICMS = 041
                    // Se produto possui ST não e nao POSSUI CEST ( NO guardiao ) e aliquota vazia
                    //   CST_ICMS = 30 ou 40

                    if( !empty($bcProduto->cest_fk_id) && $bcProduto->cest_fk_id != 1){

                        $ICMS_PDV = 'SUBSTITUICAO'; //$produto->aliquota_icm;
                        $ICMSENT  = 'SUBSTITUICAO'; //$produto->aliquota_icm;
                        $CST_ICMS = (string) '060';

                    }elseif( !empty($bcProduto->cest_fk_id) && $bcProduto->cest_fk_id != 1 && $produto->aliquota_icm == null ){ 
                    // SE o produto contiver substituição Tributária e aliquota  vazia

                        $ICMS_PDV = 'NAO TRIBUTADO';
                        $ICMSENT  = 'NAO TRIBUTADO';
                        $CST_ICMS = (string) '041';  // Não tributada

                    }elseif( (empty($bcProduto->cest_fk_id) || $bcProduto->cest_fk_id == 1) && 
                        $produto->aliquota_icm == null && $produto->possui_st == "Não"){ 
                    // SE o produto não contiver substituição Tributária e aliquota  vazia

                        $ICMS_PDV = 'ISENTO';
                        $ICMSENT  = 'ISENTO';
                        $CST_ICMS = (string) '040';   // Isenta

                    }elseif(  $produto->possui_st == "Não" && $produto->aliquota_icm != null ){ // SE o produto não contiver substituição Tributária e aliquota não vazia

                        $ICMS_PDV = 'ISENTO';
                        $ICMSENT  = 'ISENTO';
                        $CST_ICMS = (string) '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária

                    }else{

                        $ICMS_PDV = 'ISENTO';
                        $ICMSENT  = 'ISENTO';
                        $CST_ICMS = (string) '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária 
                    }

                    //CST_ENTRADA_PIS_COFINS
                    // SE o produto poussui_ST e não possui Aliquota = 004
                    // SE o produto poussui_ST e  possui Aliquota = 001
                    //CST_SAIDA_PIS_COFINS
                    // SE o produto poussui_ST e não possui Aliquota = 070

                    //PISNATRECEITA
                    // SE o produto poussui_ST e não possui Aliquota = 004
                    // SE o produto poussui_ST e  possui Aliquota = 001

                    if(!empty($bcProduto->cest_fk_id) && $bcProduto->cest_fk_id != 1 && $produto->aliquota_icm != null){

                        $CST_PIS_ENTRADA     = '004';
                        $CST_COFINS_ENTRADA  = '004';

                        $CST_PIS_SAIDA       = '070';
                        $CST_COFINS_SAIDA    = '070';

                        $PIS_NATRECEITA      = '004';

                    }else{

                        $CST_PIS_ENTRADA    = '001';
                        $CST_COFINS_ENTRADA = '001';
                        $PIS_NATRECEITA     = '001';
                        $CST_PIS_SAIDA      = '050';
                        $CST_COFINS_SAIDA   = '050';

                    }

                    $strItem = "{$COD_INTERNO};
                                {$CODBARRA};
                                {$DESCRICAO};
                                {$ICMS_PDV};
                                {$ICMSENT};
                                {$REDUCAO_BASE_ICMS};
                                {$CST_ICMS};
                                {$NCM};
                                {$PIS_COFINS};
                                {$MVA};
                                {$PAUTA};
                                {$CST_PIS_ENTRADA};
                                {$CST_PIS_SAIDA};
                                {$CST_COFINS_ENTRADA};
                                {$CST_COFINS_SAIDA};
                                {$PIS_NATRECEITA};
                                {$SIT_ESPECIAL_PIS_COFINS};
                                {$ALIQUOTA_SIT_ESPECIAL};
                                {$TIPOSPED};
                                {$CEST}";

                    array_push($data,$strItem);

                }
            }

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=PRODUTOS_EM_MONITORAMENTO_LINEAR.csv");

            $fp = fopen('php://output', 'wb');

            foreach ($data as $line ) {

                $val = explode(";", $line);
                fputcsv($fp, $val);
            }

            fclose($fp);
        }else{
            return response('É preciso informar o parâmetro Cliente.')->status('302');
        }

    }
}
