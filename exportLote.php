<?php

error_reporting(E_ALL);
set_time_limit(0);

$db_host = 'guardiao.cfgsnjemvkdk.sa-east-1.rds.amazonaws.com';
$db_user = 'postgres';
$db_name = 'guardiao_bkp';
$db_pass = 'Gsv2019!';

$lote_fk_id = $_GET['lote'];

$dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;user=$db_user;password=$db_pass";

try{

    $myPDO = new PDO($dsn);

}catch (PDOException $e){

    echo $e->getMessage();
    die;
}

// Busca pelo Cliente

$stmtCliente = $myPDO->query("SELECT c.* FROM public.cliente_lote cl
                                    INNER JOIN public.cliente c ON c.id = cl.cliente_fk_id
                                WHERE cl.id = {$lote_fk_id}");

$cliente  = $stmtCliente->fetch(PDO::FETCH_OBJ);

//Busca todos os itens do lote do cliente

$stmtLoteProdutos = $myPDO->query("SELECT
                                            seu_nome as cliente_nome,
                                            seu_codigo as cliente_codigo,
                                            gtin as cliente_gtin,
                                            ncm as cliente_ncm,
                                            possui_st as cliente_possui_st,
                                            aliquota_icm as cliente_icms_aliquota,
                                            aliquota_pis as cliente_pis_aliquota,
                                            aliquota_cofins as cliente_cofins_aliquota
                                     FROM public.lote_produto
                                     WHERE lote_fk_id = {$lote_fk_id} ");



$itensLote  = $stmtLoteProdutos->fetchAll(PDO::FETCH_OBJ);

$qtdprodutosComparados = 0;

foreach ($itensLote as $index => $itemLote) {
    $sql = "SELECT
                    pc.*,
                    bcp.nome as base_comparativa_nome,
                    bcgtin.gtin as base_comparativa_gtin,
                    pcicms.aliquota as base_comparativa_icms_aliquota,
                    pcicms.possui_st as base_comparativa_icms_possui_st,
                    pccofins.aliquota as base_comparativa_cofins_aliquota,
                    pccofins.cst as base_comparativa_cofins_cst,
                    pcpis.aliquota as base_comparativa_pis_aliquota,
                    pcpis.cst as base_comparativa_pis_cst
                FROM public.bc_produto_gtin AS bcgtin
                    INNER JOIN public.bc_produto AS bcp ON bcp.id = bcgtin.bc_produto_fk_id
                    INNER JOIN public.bc_perfil_contabil pc ON pc.ncm_fk_id = bcp.ncm_fk_id AND pc.trib_estab_origem_fk_id = {$cliente->enquadramento_tributario_fk_id}
                    INNER JOIN public.bc_perfil_contabil_icms pcicms ON pcicms.bc_perfil_contabil_fk_id = pc.id
                    INNER JOIN public.bc_perfilcontabil_cofins pccofins ON pccofins.bc_perfil_contabil_fk_id = pc.id
                    INNER JOIN public.bc_perfilcontabil_pis pcpis ON pcpis.bc_perfil_contabil_fk_id = pc.id
                WHERE bcgtin.gtin = '{$itemLote->cliente_gtin}'";

    $stmtBuscaByGTIN = $myPDO->query($sql);

    //@TODO : Se o GTIN não existir

    if($stmtBuscaByGTIN->rowCount() == 0){


        // Faz codigo do cosmos

        $url = "https://api.cosmos.bluesoft.com.br/gtins/{$itemLote->cliente_gtin}.json";
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
            print_r($object);
            die;
        }

        curl_close($curl);
    }

    try{

        $retBuscaByGTIN = $stmtBuscaByGTIN->fetch(PDO::FETCH_OBJ);

        $itensLote[$index]->base_comparativa_nome            = empty($retBuscaByGTIN->base_comparativa_nome) ? 'N/A' : $retBuscaByGTIN->base_comparativa_nome ;
        $itensLote[$index]->base_comparativa_gtin            = empty($retBuscaByGTIN->base_comparativa_gtin) ? 'N/A' : $retBuscaByGTIN->base_comparativa_gtin;
        $itensLote[$index]->base_comparativa_ncm             = empty($retBuscaByGTIN->ncm_fk_id) ? 'N/A' : $retBuscaByGTIN->ncm_fk_id;
        $itensLote[$index]->base_comparativa_tributado_4     = empty($retBuscaByGTIN->tributado_4) ? 'N/A' : $retBuscaByGTIN->tributado_4;
        $itensLote[$index]->base_comparativa_cnae_clase      = empty($retBuscaByGTIN->cnae_classe_fk_id) ? 'N/A' : $retBuscaByGTIN->cnae_classe_fk_id;
        $itensLote[$index]->base_comparativa_cnae            = empty($retBuscaByGTIN->ncm_fk_id) ? 'N/A' : $retBuscaByGTIN->ncm_fk_id  ;
        $itensLote[$index]->base_comparativa_icms_aliquota   = (empty($retBuscaByGTIN->base_comparativa_icms_aliquota) || is_null($retBuscaByGTIN->base_comparativa_icms_aliquota)) ? 'N/A' : $retBuscaByGTIN->base_comparativa_icms_aliquota;
        $itensLote[$index]->base_comparativa_icms_possui_st  = empty($retBuscaByGTIN->base_comparativa_icms_possui_st) ? 'N/A' : $retBuscaByGTIN->base_comparativa_icms_possui_st;
        $itensLote[$index]->base_comparativa_cofins_aliquota = empty($retBuscaByGTIN->base_comparativa_cofins_aliquota) ? 'N/A' : $retBuscaByGTIN->base_comparativa_cofins_aliquota;
        $itensLote[$index]->base_comparativa_cofins_cst      = empty($retBuscaByGTIN->base_comparativa_cofins_cst) ? 'N/A' : $retBuscaByGTIN->base_comparativa_cofins_cst;
        $itensLote[$index]->base_comparativa_pis_aliquota    = empty($retBuscaByGTIN->base_comparativa_pis_aliquota) ? 'N/A' : $retBuscaByGTIN->base_comparativa_pis_aliquota;
        $itensLote[$index]->base_comparativa_pis_cst         = empty($retBuscaByGTIN->base_comparativa_pis_cst) ? 'N/A' : $retBuscaByGTIN->base_comparativa_pis_cst;

    }catch (PDOException $e){
        echo $e->getMessage();
    }

    $qtdprodutosComparados++;
}

try{

    $data = array('CODIGO_DO_PRODUTO_NO_CLIENTE;NOME_DO_PRODUTO_NO_CLIENTE;NOME_PRODUTO_NA_BASE_COMPARATIVA;GTIN_NO_CLIENTE;GTIN_NA_BASE_COMPARATIVA;NCM_NO_CLIENTE;NCM_NA_BASE_COMPARATIVA;ALIQUOTA_ICMS_NO_CLIENTE;ALIQUOTA_ICMS_NA_BASE_COMPARATIVA;ALIQUOTA_PIS_NO_CLIENTE;ALIQUOTA_PIS_NA_BASE_COMPARATIVA;ALIQUOTA_COFINS_NO_CLIENTE;ALIQUOTA_COFINS_NA_BASE_COMPARATIVA;POSSUI_ST_NO_CLIENTE;POSSUI_ST_NA_BASE_COMPARATIVA;BASE_COMPARATIVA_PIS_CST;BASE_COMPARATIVA_COFINS_CST');

    foreach ($itensLote as $index => $itemLote) {

        $gtinNoCliente = (string) $itemLote->cliente_gtin;

        $strItem = "{$itemLote->cliente_codigo};
                        $itemLote->cliente_nome;
                        $itemLote->base_comparativa_nome;
                        $gtinNoCliente;
                        $itemLote->base_comparativa_gtin;
                        $itemLote->cliente_ncm;
                        $itemLote->base_comparativa_ncm;
                        $itemLote->cliente_icms_aliquota;
                        $itemLote->base_comparativa_icms_aliquota;
                        $itemLote->cliente_pis_aliquota;
                        $itemLote->base_comparativa_pis_aliquota;
                        $itemLote->cliente_cofins_aliquota;
                        $itemLote->base_comparativa_cofins_aliquota;
                        $itemLote->cliente_possui_st;
                        $itemLote->base_comparativa_icms_possui_st;
                        $itemLote->base_comparativa_pis_cst;
                        $itemLote->base_comparativa_cofins_cst";

        array_push($data,$strItem);
    }

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=Relatorio_lote_{$lote_fk_id}.csv");

    $fp = fopen('php://output', 'wb');

    foreach ($data as $line ) {

        $val = explode(";", $line);
        fputcsv($fp, $val);
    }

    fclose($fp);

}catch (\Exception $e){

    echo $e->getMessage();

}
