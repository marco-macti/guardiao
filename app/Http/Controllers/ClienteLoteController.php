<?php

namespace App\Http\Controllers;

use App\BCProduto;
use App\BCProdutoAux;
use App\BCProdutoGtin;
use App\ClienteLote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteLoteController extends Controller
{
    public function relatorioLote($loteId){

        // Busca pelo Cliente do Lote

        $lote     = ClienteLote::find($loteId);
        $produtos = $lote->produtos;

        foreach ($produtos as $index => $produto) {

            $produtoBC = DB::select("SELECT
                                                bcp.*,
                                                bcp.nome as base_comparativa_nome,
                                                bcgtin.gtin as base_comparativa_gtin,
                                                pcicms.aliquota as base_comparativa_icms_aliquota,
                                                pcicms.possui_st as base_comparativa_icms_possui_st,
                                                pccofins.aliquota as base_comparativa_cofins_aliquota,
                                                pccofins.cst as base_comparativa_cofins_cst,
                                                pcpis.aliquota as base_comparativa_pis_aliquota,
                                                pcpis.cst as base_comparativa_pis_cst
                                            FROM bc_produto_gtin AS bcgtin
                                               INNER JOIN bc_produto AS bcp ON bcp.id = bcgtin.bc_produto_fk_id
                                               LEFT JOIN bc_perfil_contabil pc ON pc.ncm_fk_id = bcp.ncm_fk_id
                                               LEFT JOIN bc_perfil_contabil_icms pcicms ON pcicms.bc_perfil_contabil_fk_id = pc.id
                                               LEFT JOIN bc_perfilcontabil_cofins pccofins ON pccofins.bc_perfil_contabil_fk_id = pc.id
                                               LEFT JOIN bc_perfilcontabil_pis pcpis ON pcpis.bc_perfil_contabil_fk_id = pc.id
                                            WHERE bcgtin.gtin = '{$produto->gtin}' AND pc.trib_estab_origem_fk_id = {$lote->cliente->enquadramento_tributario_fk_id} ");

            // Verifica se o produto está na base comparativa
            if(isset($produtoBC[0])){
                echo "GTIN ".$produto->gtin.' -  encontrado na base comparativa';
                echo "<br/>";
            }else{

                // Tenta uma consulta no cosmos

                $url = 'https://api.cosmos.bluesoft.com.br/gtins/7891910000197.json';
                $headers = array(
                    "Content-Type: application/json",
                    "X-Cosmos-Token: SJaFhcrcDrvFrwch5xPQvw"
                );

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FAILONERROR, true);

                $data = curl_exec($curl);

                if ($data === false || $data == NULL) {
                    var_dump(curl_error($curl));
                } else {

                    $object = json_decode($data);

                    if(is_object($object)){

                        // Se não encontrar o produto na base comparativa , e nem na base auxiliar,  procura no cosmos

                        try {

                            $gtin  = BCProdutoGtin::where('gtin','=',$produto->gtin)->get();

                            if(count($gtin) == 0){

                                // Criar
                                $newProdutoBC = BCProduto::create([
                                    'status'        => '',
                                    'nome'          => "$object->description",
                                    'descricao'     => "$object->description",
                                    'preco_medio'   => "$object->avg_price",
                                    'preco_maximo'  => "$object->max_price",
                                    'thumbnail'     => "$object->thumbnail",
                                    'altura'        => "$object->height",
                                    'largura'       => "$object->width",
                                    'comprimento'   => "$object->length",
                                    'peso_liquido'  => "$object->net_weight",
                                    'cest_fk_id'    => isset($object->cest->code) ? $object->cest->code : 1 ,
                                    'gpc_fk_id'     => isset($object->gpc->code) ? $object->gpc->code : 1,
                                    'ncm_fk_id'     => isset($object->ncm->code) ? $object->ncm->code : 1
                                ]);

                                print_r($newProdutoBC);

                                $newProdutoBCGtin = BCProdutoGtin::create([
                                    'gtin'             => $object->gtin,
                                    'bc_produto_fk_id' => $newProdutoBC->id
                                ]);

                                print_r($newProdutoBCGtin);
                                die;
                                echo "GTIN ".$produto->gtin.' encontrado no COSMOS e inserido na base de dados.';
                                echo "<br/>";
                                echo "Incidências:".count($gtin);
                                echo "<br/>";
                            }else{
                                echo "GTIN ".$produto->gtin.' Já cadastrado na base de dados.';
                                echo "<br/>";
                                echo "Incidências:".count($gtin);
                                echo "<br/>";
                            }

                        }catch (\PDOException $e){
                            echo $e->getMessage();
                            die;
                        }


                    }else{
                        // Se não encontrar o produto na base comparativa , e nem na base auxiliar,  procura no cosmos
                        echo "GTIN ".$produto->gtin.' não foi encontrado em nenhuma das consultas disponíveis.';
                        echo "<br/>";
                    }
                }

                curl_close($curl);

                }
            }
//            try{
//
//                $produtos[$index]->base_comparativa_nome            = empty($produtoBC->base_comparativa_nome) ? 'N/A' : $produtoBC->base_comparativa_nome ;
//                $produtos[$index]->base_comparativa_gtin            = empty($produtoBC->base_comparativa_gtin) ? 'N/A' : $produtoBC->base_comparativa_gtin;
//                $produtos[$index]->base_comparativa_ncm             = empty($produtoBC->ncm_fk_id) ? 'N/A' : $produtoBC->ncm_fk_id;
//                $produtos[$index]->base_comparativa_tributado_4     = empty($produtoBC->tributado_4) ? 'N/A' : $produtoBC->tributado_4;
//                $produtos[$index]->base_comparativa_cnae_clase      = empty($produtoBC->cnae_classe_fk_id) ? 'N/A' : $produtoBC->cnae_classe_fk_id;
//                $produtos[$index]->base_comparativa_cnae            = empty($produtoBC->ncm_fk_id) ? 'N/A' : $produtoBC->ncm_fk_id  ;
//                $produtos[$index]->base_comparativa_icms_aliquota   = (empty($produtoBC->base_comparativa_icms_aliquota) || is_null($produtoBC->base_comparativa_icms_aliquota)) ? 'N/A' : $produtoBC->base_comparativa_icms_aliquota;
//                $produtos[$index]->base_comparativa_icms_possui_st  = empty($produtoBC->base_comparativa_icms_possui_st) ? 'N/A' : $produtoBC->base_comparativa_icms_possui_st;
//                $produtos[$index]->base_comparativa_cofins_aliquota = empty($produtoBC->base_comparativa_cofins_aliquota) ? 'N/A' : $produtoBC->base_comparativa_cofins_aliquota;
//                $produtos[$index]->base_comparativa_cofins_cst      = empty($produtoBC->base_comparativa_cofins_cst) ? 'N/A' : $produtoBC->base_comparativa_cofins_cst;
//                $produtos[$index]->base_comparativa_pis_aliquota    = empty($produtoBC->base_comparativa_pis_aliquota) ? 'N/A' : $produtoBC->base_comparativa_pis_aliquota;
//                $produtos[$index]->base_comparativa_pis_cst         = empty($produtoBC->base_comparativa_pis_cst) ? 'N/A' : $produtoBC->base_comparativa_pis_cst;
//
//            }catch (PDOException $e){
//                echo $e->getMessage();
//            }
        die;

        try{

            $data = array('CODIGO_DO_PRODUTO_NO_CLIENTE;NOME_DO_PRODUTO_NO_CLIENTE;NOME_PRODUTO_NA_BASE_COMPARATIVA;GTIN_NO_CLIENTE;GTIN_NA_BASE_COMPARATIVA;NCM_NO_CLIENTE;NCM_NA_BASE_COMPARATIVA;ALIQUOTA_ICMS_NO_CLIENTE;ALIQUOTA_ICMS_NA_BASE_COMPARATIVA;ALIQUOTA_PIS_NO_CLIENTE;ALIQUOTA_PIS_NA_BASE_COMPARATIVA;ALIQUOTA_COFINS_NO_CLIENTE;ALIQUOTA_COFINS_NA_BASE_COMPARATIVA;POSSUI_ST_NO_CLIENTE;POSSUI_ST_NA_BASE_COMPARATIVA;BASE_COMPARATIVA_PIS_CST;BASE_COMPARATIVA_COFINS_CST');

            foreach ($produtos as $index => $itemLote) {

                $gtinNoCliente = (string) $itemLote->gtin;

                $strItem = "{$itemLote->seu_codigo};
                        $itemLote->seu_nome;
                        $itemLote->base_comparativa_nome;
                        $gtinNoCliente;
                        $itemLote->base_comparativa_gtin;
                        $itemLote->ncm;
                        $itemLote->base_comparativa_ncm;
                        $itemLote->aliquota_icm;
                        $itemLote->base_comparativa_icms_aliquota;
                        $itemLote->aliquota_pis;
                        $itemLote->base_comparativa_pis_aliquota;
                        $itemLote->aliquota_cofins;
                        $itemLote->base_comparativa_cofins_aliquota;
                        $itemLote->possui_st;
                        $itemLote->base_comparativa_icms_possui_st;
                        $itemLote->base_comparativa_pis_cst;
                        $itemLote->base_comparativa_cofins_cst";

                array_push($data,$strItem);
            }

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment; filename=Relatorio_lote_{$loteId}.csv");

            $fp = fopen('php://output', 'wb');

            foreach ($data as $line ) {

                $val = explode(";", $line);
                fputcsv($fp, $val);
            }

            fclose($fp);

        }catch (\Exception $e){

            echo $e->getMessage();

        }

        }
    public function sincronizarLote($loteId){
        echo $loteId;
        die;
    }
}
