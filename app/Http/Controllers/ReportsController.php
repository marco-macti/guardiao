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

            //Monta cabeçalho inicial da exportaçáo

            $cabecalho  = array("PISCOFINS;Descrição;Valor Alíquota(PIS);Valor Alíquota (COFINS);CST Entrada PIS/COFINS; CST Saída PIS/COFINS;Natureza Receita PIS/COFINS");

            $cabecalhoLinha1 = "1;ISENTO;0;0;73;6;105";

            array_push($cabecalho,$cabecalhoLinha1);

             $cabecalhoLinha2 = "2;PIS 1,65 COFINS 7,60;1,65;7,6;50;1;";

            array_push($cabecalho,$cabecalhoLinha2);

             $cabecalhoLinha3 = "3;PIS 0,198 COFINS 0,912;0,198;0,912;60;1;";

            array_push($cabecalho,$cabecalhoLinha3);

            $cabecalhoLinha4 = "4;PIS 0,66 COFINS 3,04;0,66;3,04;60;1;";

            array_push($cabecalho,$cabecalhoLinha4);

            $cabecalhoLinha5 = "5;MONOFASICA;0;0;73;4;202";

            array_push($cabecalho,$cabecalhoLinha5);

            $cabecalhoLinha6 = "6;PIS 0,495 COFINS 2,28;0,495;2,28;60;1;";

            array_push($cabecalho,$cabecalhoLinha6);

            $cabecalhoLinha7 = "7;OUTRAS;0;0;98;49;";

            array_push($cabecalho,$cabecalhoLinha7);



            // Monta o Cabeçalho da exportação

            $data  = "cod_interno;codbarra;desc;%icms PDV;icmsent;redução base icms;CST_ICMS;NCM;piscofins;MVA (margemst);PAUTA(calculo st);cst_pis_entrada;cst_pis_saida;cst_cofins_entrada;cst_cofins_saida;pis_natreceita;Sit.Especial PISCOFINS;Aliq. Sit. Especial;CST Sit.Especial;TipoSPED;CEST";

            array_push($cabecalho,$data);


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

                //$bcProdutoGtin = BCProdutoGtin::where('gtin',$produto->gtin)->first();

                // Se encontrar produto no sistema

                //if(is_object($bcProdutoGtin)){

                    /*$bcProduto = BCProduto::select('bc_produto.*','cest.mva')
                                            ->leftJoin('cest', 'bc_produto.cest_fk_id', '=', 'cest.id')
                                            ->where('bc_produto.id',$bcProdutoGtin->bc_produto_fk_id)
                                            ->first();*/

                    $COD_INTERNO             = $produto->seu_codigo;
                    $CODBARRA                = "'".$produto->gtin."'";
                    $DESCRICAO               = trim($produto->seu_nome);
                    $ICMS_PDV                = '';    // OK
                    $ICMSENT                 = '';    // OK
                    $REDUCAO_BASE_ICMS       = 0;     // Ok
                    $CST_ICMS                = '';    // OK
                    $NCM                     = $produto->ncm;
                    $PIS_COFINS              = '';    // Ok
                    $MVA                     = $produto->mva;    
                    $PAUTA                   = '';    // Ok
                    $CST_PIS_ENTRADA         = '';    // OK
                    $CST_PIS_SAIDA           = '';    // OK
                    $CST_COFINS_ENTRADA      = '';    // OK
                    $CST_COFINS_SAIDA        = '';    // Ok
                    $PIS_NATRECEITA          = '';    // OK
                    $SIT_ESPECIAL_PIS_COFINS = '';    // Enviar em branco
                    $ALIQUOTA_SIT_ESPECIAL   = '';    // Enviar em branco
                    $TIPOSPED                = '00';  //00 – Mercadoria para revenda
                    $CEST                    = $produto->cest;


                    if(!empty($produto->mva)){
                        $MVA   = $produto->mva;
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

                    if(!empty($CEST) && $CEST != 1){

                        $ICMS_PDV = 'SUBSTITUICAO'; //$produto->aliquota_icm;
                        $ICMSENT  = 'SUBSTITUICAO'; //$produto->aliquota_icm;
                        $CST_ICMS = (string) '060';

                    }elseif( !empty($CEST) && $CEST != 1 && $produto->aliquota_icm == null ){ 
                    // SE o produto contiver substituição Tributária e aliquota  vazia
                        if(is_numeric($produto->aliquota_icm)){
                            $ICMS_PDV = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $ICMSENT  = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $CST_ICMS = (string) '041';  // Não tributada
                            if($ICMS_PDV == 'ISENTO'){
                                $PIS_COFINS = '1';
                            }
                        }
                        else{
                            $ICMS_PDV = 'NAO TRIBUTADO';
                            $ICMSENT  = 'NAO TRIBUTADO';
                            $CST_ICMS = (string) '041';  // Não tributada
                        }
                        

                    }elseif( (empty($CEST) || $CEST == 1) && 
                        $produto->aliquota_icm == null && $produto->possui_st == "Não"){ 
                    // SE o produto não contiver substituição Tributária e aliquota  vazia

                        if(is_numeric($produto->aliquota_icm)){
                            $ICMS_PDV = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $ICMSENT  = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $CST_ICMS = (string) '040';   // Isenta

                            if($ICMS_PDV == 'ISENTO'){
                                $PIS_COFINS = '1';
                            }
                        }
                        else{
                            $PIS_COFINS = '1';
                            $ICMS_PDV = 'ISENTO';
                            $ICMSENT  = 'ISENTO';
                            $CST_ICMS = (string) '040';   // Isenta
                        }
                        

                    }elseif(  $produto->possui_st == "Não" && $produto->aliquota_icm != null ){ // SE o produto não contiver substituição Tributária e aliquota não vazia
                        if(is_numeric($produto->aliquota_icm)){
                            $ICMS_PDV = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $ICMSENT  = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $CST_ICMS = (string) '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária

                            if($ICMS_PDV == 'ISENTO'){
                                $PIS_COFINS = '1';
                            }
                        }
                        else{
                            $PIS_COFINS = '1';
                            $ICMS_PDV = 'ISENTO';
                            $ICMSENT  = 'ISENTO';
                            $CST_ICMS = (string) '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária
                        }

                    }else{

                        if(is_numeric($produto->aliquota_icm)){
                            $ICMS_PDV = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $ICMSENT  = ($produto->aliquota_icm == 0) ? 'ISENTO' : $produto->aliquota_icm;
                            $CST_ICMS = (string) '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária

                            if($ICMS_PDV == 'ISENTO'){
                                $PIS_COFINS = '1';
                            }
                        }
                        else{
                            $PIS_COFINS = '1';
                            $ICMS_PDV = 'ISENTO';
                            $ICMSENT  = 'ISENTO';
                            $CST_ICMS = (string) '030';          // Isenta ou não tributária e com cobrança do ICMS por substituição tributária 
                        }
                    }

                    if($produto->aliquota_pis == '1.65' OR $produto->aliquota_cofins == '7.60'){
                        $PIS_COFINS = '2';
                    }
                    else if($produto->aliquota_pis == '0.198' OR $produto->aliquota_cofins == '0.912'){
                        $PIS_COFINS = '3';
                    }
                    else if($produto->aliquota_pis == '0.66' OR $produto->aliquota_cofins == '3.04'){
                        $PIS_COFINS = '4';
                    }
                    else if($produto->aliquota_pis == '0.495' && $produto->aliquota_cofins == '2.28'){
                        $PIS_COFINS = '6';
                    }
                    else{
                        $PIS_COFINS = '7';   
                    }

                                     
                    

                    $arrNcm = array("27101159",
                                "27101259",
                                "27101921",
                                "27111910",
                                "27101911",
                                "38249029",
                                "38249029",
                                "38260000",
                                "38260000",
                                "22071000",
                                "22072010",
                                "22089000",
                                "220710",
                                "2207201",
                                "22021000",
                                "22021000",
                                "22029000",
                                "22029000",
                                "22030000",
                                "22030000",
                                "70109021",
                                "39233000",
                                "73102110",
                                "76129019",
                                "39233000",
                                "22011000",
                                "22011000",
                                "22011000",
                                "21069010");
                    $ncm_lote = str_replace(".", "", $produto->ncm);

                    if(in_array(trim($produto->ncm), $arrNcm)){
                        $PIS_COFINS = '5';
                    }

                    //CST_ENTRADA_PIS_COFINS
                    // SE o produto poussui_ST e não possui Aliquota = 004
                    // SE o produto poussui_ST e  possui Aliquota = 001
                    //CST_SAIDA_PIS_COFINS
                    // SE o produto poussui_ST e não possui Aliquota = 070

                    //PISNATRECEITA
                    // SE o produto poussui_ST e não possui Aliquota = 004
                    // SE o produto poussui_ST e  possui Aliquota = 001

                    if(!empty($CEST) && $CEST != 1 && $produto->aliquota_icm != null){

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

                    if($CEST == 1 || $CEST == '1'){
                        $CEST = '';
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
                                ;
                                {$TIPOSPED};
                                {$CEST}";

                    array_push($cabecalho,$strItem);

                //}
            }

            ob_start();
            ob_flush();
            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=PRODUTOS_EM_MONITORAMENTO_LINEAR.csv");

            $fp = fopen('php://output', 'wb');
            foreach ($cabecalho as $line ) {

                $val = explode(";", $line);
                fputcsv($fp, $val);
            }
            fclose($fp);
        }else{
            return response('É preciso informar o parâmetro Cliente.')->status('302');
        }

    }
}
