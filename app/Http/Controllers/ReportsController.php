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

                    $bcProduto = BCProduto::where('id',$bcProdutoGtin->bc_produto_fk_id)->first();

                    $COD_INTERNO             = $produto->seu_codigo;
                    $CODBARRA                = $produto->gtin;
                    $DESCRICAO               = trim($produto->seu_nome);
                    $ICMS_PDV                = '';    // Tratado Abaixo
                    $ICMSENT                 = '';    // Tratado Abaixo
                    $REDUCAO_BASE_ICMS       = '';    // Coletar retorno do IOB
                    $CST_ICMS                = '';    // Tratado Abaixo
                    $NCM                     = $produto->ncm;
                    $PIS_COFINS              = '';    // Tratado Abaixo
                    $MVA                     = '';    // Coletar retorno do IOB
                    $PAUTA                   = '';    // Coletar retorno do IOB
                    $CST_PIS_ENTRADA         = '';    // OK
                    $CST_PIS_SAIDA           = '';    // OK
                    $CST_COFINS_ENTRADA      = '';    // OK
                    $CST_COFINS_SAIDA        = '';    // Ok
                    $PIS_NATRECEITA          = '';    // OK
                    $SIT_ESPECIAL_PIS_COFINS = '';    // Enviar em branco
                    $ALIQUOTA_SIT_ESPECIAL   = '';    // Enviar em branco
                    $TIPOSPED                = '00';  //00 – Mercadoria para revenda
                    $CEST                    = $bcProduto->cest_fk_id;

                    // Se produto possui ST Sim e Possui CEST ( no Guardiao)
                    //    CST_ICMS = 060
                    // Se produto posui ST SIM e Possui CEST ( no Guardiao) e aliquota vazia
                    //    CST_ICMS = 041
                    // Se produto possui ST não e nao POSSUI CEST ( NO guardiao ) e aliquota vazia
                    //   CST_ICMS = 30 ou 40

                    if( $produto->possui_st == "Sim" && $bcProduto->cest_fk_id != null ){

                        $ICMS_PDV = $produto->aliquota_icm;
                        $ICMSENT  = $produto->aliquota_icm;
                        $CST_ICMS = '060';

                    }elseif( $produto->possui_st == "Sim" && $produto->aliquota_icm == null && $bcProduto->cest_fk_id != null ){ // SE o produto contiver substituição Tributária e aliquota  vazia

                        $ICMS_PDV = 'NAO TRIBUTADO';
                        $ICMSENT  = 'NAO TRIBUTADO';
                        $CST_ICMS = '041';            // Não tributada

                    }elseif(  $produto->possui_st == "Não" && $produto->aliquota_icm == null && $bcProduto->cest_fk_id == null ){ // SE o produto não contiver substituição Tributária e aliquota  vazia

                        $ICMS_PDV = 'ISENTO';
                        $ICMSENT  = 'ISENTO';
                        $CST_ICMS = '040';                 // Isenta

                    }elseif(  $produto->possui_st == "Não" && $produto->aliquota_icm != null ){ // SE o produto não contiver substituição Tributária e aliquota não vazia

                        $ICMS_PDV = 'ISENTO';
                        $ICMSENT  = 'ISENTO';
                        $CST_ICMS = '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária

                    }

                    //CST_ENTRADA_PIS_COFINS
                    // SE o produto poussui_ST e não possui Aliquota = 004
                    // SE o produto poussui_ST e  possui Aliquota = 001
                    //CST_SAIDA_PIS_COFINS
                    // SE o produto poussui_ST e não possui Aliquota = 070

                    //PISNATRECEITA
                    // SE o produto poussui_ST e não possui Aliquota = 004
                    // SE o produto poussui_ST e  possui Aliquota = 001

                    if($produto->possui_st == "Sim" && $produto->aliquota_icm == null){
                        $CST_PIS_ENTRADA     = '004';
                        $CST_COFINS_ENTRADA  = '004';
                        $CST_PIS_SAIDA       = '070';
                        $CST_COFINS_SAIDA    = '070';
                        $PIS_NATRECEITA      = '004';
                    }elseif($produto->possui_st == "Sim" && $produto->aliquota_icm != null){
                        $CST_PIS_ENTRADA    = '001';
                        $CST_COFINS_ENTRADA = '001';
                        $PIS_NATRECEITA     = '001';
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
