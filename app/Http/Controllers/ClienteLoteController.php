<?php

namespace App\Http\Controllers;

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
                                               INNER JOIN bc_perfil_contabil pc ON pc.ncm_fk_id = bcp.ncm_fk_id
                                               INNER JOIN bc_perfil_contabil_icms pcicms ON pcicms.bc_perfil_contabil_fk_id = pc.id
                                               INNER JOIN bc_perfilcontabil_cofins pccofins ON pccofins.bc_perfil_contabil_fk_id = pc.id
                                               INNER JOIN bc_perfilcontabil_pis pcpis ON pcpis.bc_perfil_contabil_fk_id = pc.id
                                            WHERE bcgtin.gtin = '{$produto->gtin}' AND pc.trib_estab_origem_fk_id = {$lote->cliente->enquadramento_tributario_fk_id} ");

            try{

                $produtos[$index]->base_comparativa_nome            = empty($produtoBC->base_comparativa_nome) ? 'N/A' : $produtoBC->base_comparativa_nome ;
                $produtos[$index]->base_comparativa_gtin            = empty($produtoBC->base_comparativa_gtin) ? 'N/A' : $produtoBC->base_comparativa_gtin;
                $produtos[$index]->base_comparativa_ncm             = empty($produtoBC->ncm_fk_id) ? 'N/A' : $produtoBC->ncm_fk_id;
                $produtos[$index]->base_comparativa_tributado_4     = empty($produtoBC->tributado_4) ? 'N/A' : $produtoBC->tributado_4;
                $produtos[$index]->base_comparativa_cnae_clase      = empty($produtoBC->cnae_classe_fk_id) ? 'N/A' : $produtoBC->cnae_classe_fk_id;
                $produtos[$index]->base_comparativa_cnae            = empty($produtoBC->ncm_fk_id) ? 'N/A' : $produtoBC->ncm_fk_id  ;
                $produtos[$index]->base_comparativa_icms_aliquota   = (empty($produtoBC->base_comparativa_icms_aliquota) || is_null($produtoBC->base_comparativa_icms_aliquota)) ? 'N/A' : $produtoBC->base_comparativa_icms_aliquota;
                $produtos[$index]->base_comparativa_icms_possui_st  = empty($produtoBC->base_comparativa_icms_possui_st) ? 'N/A' : $produtoBC->base_comparativa_icms_possui_st;
                $produtos[$index]->base_comparativa_cofins_aliquota = empty($produtoBC->base_comparativa_cofins_aliquota) ? 'N/A' : $produtoBC->base_comparativa_cofins_aliquota;
                $produtos[$index]->base_comparativa_cofins_cst      = empty($produtoBC->base_comparativa_cofins_cst) ? 'N/A' : $produtoBC->base_comparativa_cofins_cst;
                $produtos[$index]->base_comparativa_pis_aliquota    = empty($produtoBC->base_comparativa_pis_aliquota) ? 'N/A' : $produtoBC->base_comparativa_pis_aliquota;
                $produtos[$index]->base_comparativa_pis_cst         = empty($produtoBC->base_comparativa_pis_cst) ? 'N/A' : $produtoBC->base_comparativa_pis_cst;

            }catch (PDOException $e){
                echo $e->getMessage();
            }
        }

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

}
