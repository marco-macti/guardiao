<?php

namespace App\Http\Controllers;

use App\BCProduto;
use App\BCProdutoAux;
use App\BCProdutoGtin;
use App\ClienteLote;
use App\LoteProduto;
use App\Ncm;
use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ClienteLoteController extends Controller
{
    public function relatorioLote($loteId){

        // Busca pelo Cliente do Lote

        $lote     = ClienteLote::find($loteId);
        $produtos = $lote->produtos;

        $corretos = array();
        $incorretos = array();


        foreach ($produtos as $index => $produto) {

            $nomeEx      = explode(" ", $produto->seu_nome);
            if(count($nomeEx) > 1){
                $nomeReplace = $nomeEx[0]."|".$nomeEx[1];
            }
            else{
                $nomeReplace = $nomeEx[0];
            }
            
            $produtoBC = DB::select("SELECT
                                            bcp.*,
                                            bcp.nome as base_comparativa_nome,
                                            bcgtin.gtin as base_comparativa_gtin,
                                            pcicms.aliquota as base_comparativa_icms_aliquota,
                                            pcicms.possui_st as base_comparativa_icms_possui_st,
                                            pcicms.base_legal_st as base_comparativa_icms_base_legal,
                                            pccofins.aliquota as base_comparativa_cofins_aliquota,
                                            pccofins.cst as base_comparativa_cofins_cst,
                                            pccofins.base_legal as base_comparativa_cofins_base_legal,
                                            pcpis.aliquota as base_comparativa_pis_aliquota,
                                            pcpis.cst as base_comparativa_pis_cst,
                                            pcpis.base_legal as base_comparativa_pis_base_legal
                                        FROM bc_produto_gtin AS bcgtin
                                            INNER JOIN bc_produto AS bcp ON bcp.id = bcgtin.bc_produto_fk_id
                                            LEFT JOIN bc_perfil_contabil pc ON pc.ncm_fk_id = bcp.ncm_fk_id
                                            LEFT JOIN bc_perfil_contabil_icms pcicms ON pcicms.bc_perfil_contabil_fk_id = pc.id
                                            LEFT JOIN bc_perfilcontabil_cofins pccofins ON pccofins.bc_perfil_contabil_fk_id = pc.id
                                            LEFT JOIN bc_perfilcontabil_pis pcpis ON pcpis.bc_perfil_contabil_fk_id = pc.id
                                        WHERE
                                        (bcp.ncm_fk_id = '{$produto->ncm}' AND bcp.nome SIMILAR TO '%($nomeReplace)%' AND pc.trib_estab_origem_fk_id = {$lote->cliente->enquadramento_tributario_fk_id}) LIMIT 1 OFFSET 0");


            if(count($produtoBC) > 0){

                // Verificação se o NCM da Base Comparativa é igual ao produto do lote

                if($produto->ncm != $produtoBC[0]->ncm_fk_id){
                    $produtos[$index]->ncm_correto = 'N';
                }else{
                    $produtos[$index]->ncm_correto = 'S';
                }

                // Verificação se aliquota de ICMS da Base Comparativa é igual ao produto do lote
                if(is_null($produtoBC[0]->base_comparativa_icms_aliquota)){
                    $produtoBC[0]->base_comparativa_icms_aliquota = 0;
                }

                if($produto->aliquota_icm != $produtoBC[0]->base_comparativa_icms_aliquota){
                    $produtos[$index]->icms_correto = 'N';
                }else{
                    $produtos[$index]->icms_correto = 'S';
                }

                // Verificação se aliquota de PIS da Base Comparativa é igual ao produto do lote

                if(is_null($produtoBC[0]->base_comparativa_pis_aliquota)){
                    $produtoBC[0]->base_comparativa_pis_aliquota = 0;
                }

                if($produto->aliquota_pis != $produtoBC[0]->base_comparativa_pis_aliquota ){
                    $produtos[$index]->pis_correto = 'N';
                }else{
                    $produtos[$index]->pis_correto = 'S';
                }


                // Verificação se aliquota de COFINS da Base Comparativa é igual ao produto do lote

                if(is_null($produtoBC[0]->base_comparativa_cofins_aliquota)){
                    $produtoBC[0]->base_comparativa_cofins_aliquota = 0;
                }

                if($produto->aliquota_cofins != $produtoBC[0]->base_comparativa_cofins_aliquota){
                    $produtos[$index]->cofins_correto = 'N';
                }else{
                    $produtos[$index]->cofins_correto = 'S';
                }

            }else{
                $produtos[$index]->ncm_correto    = 'N/A';
                $produtos[$index]->icms_correto   = 'N/A';
                $produtos[$index]->pis_correto    = 'N/A';
                $produtos[$index]->cofins_correto = 'N/A';
            }

            try {

                $produtos[$index]->base_comparativa_nome = empty($produtoBC[0]->base_comparativa_nome) ? 'N/A' : $produtoBC[0]->base_comparativa_nome;

                $produtos[$index]->base_comparativa_gtin = empty($produtoBC[0]->base_comparativa_gtin) ? 'N/A' : $produtoBC[0]->base_comparativa_gtin;

                $produtos[$index]->base_comparativa_ncm = empty($produtoBC[0]->ncm_fk_id) ? 'N/A' : $produtoBC[0]->ncm_fk_id;

                $produtos[$index]->base_comparativa_tributado_4 = empty($produtoBC[0]->tributado_4) ? 'N/A' : $produtoBC[0]->tributado_4;

                $produtos[$index]->base_comparativa_cnae_clase = empty($produtoBC[0]->cnae_classe_fk_id) ? 'N/A' : $produtoBC[0]->cnae_classe_fk_id;

                $produtos[$index]->base_comparativa_cnae = empty($produtoBC[0]->ncm_fk_id) ? 'N/A' : $produtoBC[0]->ncm_fk_id;

                $produtos[$index]->base_comparativa_icms_aliquota = (empty($produtoBC[0]->base_comparativa_icms_aliquota) || is_null($produtoBC[0]->base_comparativa_icms_aliquota)) ? 0 : $produtoBC[0]->base_comparativa_icms_aliquota;

                $produtos[$index]->base_comparativa_icms_base_legal = (empty($produtoBC[0]->base_comparativa_icms_base_legal) || is_null($produtoBC[0]->base_comparativa_icms_base_legal)) ? 'N/A' : $produtoBC[0]->base_comparativa_icms_base_legal;

                $produtos[$index]->base_comparativa_icms_possui_st = empty($produtoBC[0]->base_comparativa_icms_possui_st) ? 'N/A' : $produtoBC[0]->base_comparativa_icms_possui_st;

                $produtos[$index]->base_comparativa_cofins_aliquota = empty($produtoBC[0]->base_comparativa_cofins_aliquota) ? 'N/A' : $produtoBC[0]->base_comparativa_cofins_aliquota;

                $produtos[$index]->base_comparativa_cofins_cst = empty($produtoBC[0]->base_comparativa_cofins_cst) ? 'N/A' : $produtoBC[0]->base_comparativa_cofins_cst;

                $produtos[$index]->base_comparativa_cofins_base_legal = empty($produtoBC[0]->base_comparativa_cofins_base_legal) ? 'N/A' : $produtoBC[0]->base_comparativa_cofins_base_legal;

                $produtos[$index]->base_comparativa_pis_aliquota = empty($produtoBC[0]->base_comparativa_pis_aliquota) ? 'N/A' : $produtoBC[0]->base_comparativa_pis_aliquota;
                $produtos[$index]->base_comparativa_pis_cst = empty($produtoBC[0]->base_comparativa_pis_cst) ? 'N/A' : $produtoBC[0]->base_comparativa_pis_cst;

                $produtos[$index]->base_comparativa_pis_base_legal = empty($produtoBC[0]->base_comparativa_pis_base_legal) ? 'N/A' : $produtoBC[0]->base_comparativa_pis_base_legal;

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        try{

            $data = array('CODIGO_DO_PRODUTO_NO_CLIENTE;NOME_DO_PRODUTO_NO_CLIENTE;NOME_PRODUTO_NA_BASE_COMPARATIVA;GTIN_NO_CLIENTE;GTIN_NA_BASE_COMPARATIVA;NCM_NO_CLIENTE;NCM_NA_BASE_COMPARATIVA;ALIQUOTA_ICMS_NO_CLIENTE;ALIQUOTA_ICMS_NA_BASE_COMPARATIVA;ALIQUOTA_PIS_NO_CLIENTE;ALIQUOTA_PIS_NA_BASE_COMPARATIVA;ALIQUOTA_COFINS_NO_CLIENTE;ALIQUOTA_COFINS_NA_BASE_COMPARATIVA;POSSUI_ST_NO_CLIENTE;POSSUI_ST_NA_BASE_COMPARATIVA;BASE_COMPARATIVA_PIS_CST;BASE_COMPARATIVA_COFINS_CST;ICMS_BASE_LEGAL;COFINS_BASE_LEGAL;PIS_BASE_LEGAL;NCM_CORRETO;ICMS_CORRETO;PIS_CORRETO;COFINS_CORRETO;');

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
                        $itemLote->base_comparativa_cofins_cst;
                        $itemLote->base_comparativa_icms_base_legal;
                        $itemLote->base_comparativa_cofins_base_legal;
                        $itemLote->base_comparativa_pis_base_legal;
                        $itemLote->ncm_correto;
                        $itemLote->icms_correto;
                        $itemLote->pis_correto;
                        $itemLote->cofins_correto";

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

        $lote     = ClienteLote::find($loteId);
        $produtos = $lote->produtos;//->where('gtin','=','7896110007373');

        // Fix: Resolve o problema de produtos sem bc_perfil_contaabil_id

        $cliente  = Cliente::find($lote->cliente_fk_id);
        $produtosAudit =  DB::select("SELECT
                                    cl.id as NUMERO_DO_LOTE,
                                    cl.cliente_lote_status_fk_id as STATUS_DO_LOTE,
                                    lp.bc_perfilcontabil_fk_id as LOTE_PRODUTO_PERFIL_CONTABIL_ID,
                                    lp.id as LOTE_PRODUTO_ID,
                                    lp.gtin as LOTE_PRODUTO_GTIN,
                                    lp.ncm as LOTE_PRODUTO_NCM,
                                    bcpc.id as BC_PEFIL_CONTABIL_ID,
                                    bcpc.ncm_fk_id AS BC_PERFIL_CONTABIL_NCM
                                    FROM
                                    public.cliente_lote cl
                                        INNER JOIN lote_produto lp ON lp.lote_fk_id = cl.id
                                        INNER JOIN bc_perfil_contabil bcpc ON bcpc.ncm_fk_id = lp.ncm
                                    WHERE cl.cliente_fk_id = $cliente->id
                                      AND cl.cliente_lote_status_fk_id = 4
                                      AND lp.status_fk_id = 5
                                      AND bcpc.trib_estab_destino_fk_id = $cliente->tributacao_estabelecimento_destino_fk_id
                                      AND bcpc.trib_estab_origem_fk_id = $cliente->enquadramento_tributario_fk_id");

        //EndFix

        foreach ($produtosAudit as $index => $prodAudit) {

            try {
                LoteProduto::find($prodAudit->lote_produto_id)->update([
                    'bc_perfilcontabil_fk_id' => $prodAudit->bc_pefil_contabil_id
                ]);
            }catch (\PDOException $e){
                echo $e->getMessage();
                die;
            }

        }

        $produtosNaoEncontrados = "<table style='width:100%'>
                                      <tr>
                                        <th>GTIN</th>
                                        <th>NCM</th>
                                        <th>Nome do Produto</th>
                                        <th>Origem</th>
                                        <th>Está cadastrado na base?</th>
                                      </tr>";

        foreach ($produtos as $index => $produto) {

            $nomeEx      = explode(" ", $produto->seu_nome);

            if(count($nomeEx) > 1){
                $nomeReplace = $nomeEx[0]."|".$nomeEx[1];
            }
            else{
                $nomeReplace = $nomeEx[0];
            }
            

            $produtoBC = DB::select("SELECT DISTINCT
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
                                            WHERE (bcp.ncm_fk_id = '{$produto->ncm}' AND bcp.nome SIMILAR TO '%($nomeReplace)%' AND pc.trib_estab_origem_fk_id = {$lote->cliente->enquadramento_tributario_fk_id}) LIMIT 1 OFFSET 0");

            // Verifica se o produto está na base comparativa
            if(!isset($produtoBC[0])){

                // Se caso o produto não existir na base comparativa , tenta uma consulta no cosmos

                $url = 'https://api.cosmos.bluesoft.com.br/gtins/'.$produto->gtin.'.json';


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

                    try {
                        if(strlen($produto->ncm) < 8){
                            $ncmProduto = '0'.$produto->ncm;
                        }else{
                            $ncmProduto = $produto->ncm;
                        }

                        $ncm = Ncm::where('cod_ncm','=',$ncmProduto)->get();

                        if(count($ncm) > 0){

                            $gtin  = BCProdutoGtin::where('gtin','=',$produto->gtin)->get();

                            if(count($gtin) == 0){

                                $newProdutoBC = BCProduto::create([
                                    'status'        => "",
                                    'nome'          => "$produto->seu_nome",
                                    'descricao'     => "$produto->seu_nome",
                                    'preco_medio'   => 0 ,
                                    'preco_maximo'  => 0 ,
                                    'thumbnail'     => "",
                                    'altura'        => 0,
                                    'largura'       => 0,
                                    'comprimento'   => 0,
                                    'peso_liquido'  => 0,
                                    'cest_fk_id'    => 1 ,
                                    'gpc_fk_id'     => 1,
                                    'ncm_fk_id'     => $ncmProduto
                                ]);

                                $newProdutoBCGtin = BCProdutoGtin::create([
                                    'gtin'             => $produto->gtin,
                                    'bc_produto_fk_id' => $newProdutoBC->id
                                ]);

                                $produtosNaoEncontrados.= "<tr>
                                                            <td>{$produto->gtin}</td>
                                                            <td>{$ncmProduto}</td>
                                                            <td>{$produto->seu_nome}</td>
                                                            <td>Base do Cliente</td>
                                                            <td>Sim</td>
                                                        </tr>";
                            }else{

                                $produtosNaoEncontrados.= "<tr>
                                                <td>{$produto->gtin}</td>
                                                <td>{$ncmProduto}</td>
                                                <td>{$produto->seu_nome}</td>
                                                <td>Base Comparativa</td>
                                                <td>Sim</td>
                                            </tr>";

                            }
                        }else{
                            $produtosNaoEncontrados.= "<tr>
                                                        <td>{$produto->gtin}</td>
                                                        <td>{$ncmProduto}</td>
                                                        <td>{$produto->seu_nome}</td>
                                                        <td>Nenhuma</td>
                                                        <td>Não - NCM Inexistente</td>
                                                    </tr>";

                        }

                    }catch (\PDOException $e){
                        echo $e->getMessage();
                        die;
                    }

                } else {

                    $object = json_decode($data);

                    if(is_object($object)){

                        try {

                            if(isset($object->ncm->code)) {

                                $ncm = Ncm::where('cod_ncm', '=', $object->ncm->code)->get();

                                if (count($ncm) > 0) {

                                    $ncmProduto = $object->ncm->code;

                                    $gtin = BCProdutoGtin::where('gtin', '=', $produto->gtin)->get();

                                    if (count($gtin) == 0) {

                                        $newProdutoBC = BCProduto::create([
                                            'status'       => "",
                                            'nome'         => "$object->description",
                                            'descricao'    => "$object->description",
                                            'preco_medio'  => isset($object->avg_price) ? $object->avg_price : 0,
                                            'preco_maximo' => isset($object->max_price) ? $object->max_price : 0,
                                            'thumbnail'    => "$object->thumbnail",
                                            'altura'       => isset($object->height) ? $object->height : 0,
                                            'largura'      => isset($object->width) ? $object->width : 0,
                                            'comprimento'  => isset($object->length) ? $object->length : 0,
                                            'peso_liquido' => isset($object->net_weight) ? $object->net_weight : 0,
                                            'cest_fk_id'   => 1,//isset($object->cest->code) ? $object->cest->code : 1,
                                            'gpc_fk_id'    => 1, //isset($object->gpc->code) ? $object->gpc->code : 1,
                                            'ncm_fk_id'    => $object->ncm->code
                                        ]);

                                        $newProdutoBCGtin = BCProdutoGtin::create([
                                            'gtin' => $object->gtin,
                                            'bc_produto_fk_id' => $newProdutoBC->id
                                        ]);

                                        $ncm = isset($object->ncm->code) ? $object->ncm->code : "$produto->ncm";

                                        $produtosNaoEncontrados .= "<tr>
                                                    <td>{$object->gtin}</td>
                                                    <td>{$ncmProduto}</td>
                                                    <td>{$object->description}</td>
                                                    <td>Cosmos</td>
                                                    <td>Sim</td>
                                                </tr>";
                                    }
                                } else {
                                    $produtosNaoEncontrados .= "<tr>
                                                            <td>{$produto->gtin}</td>
                                                            <td>{$object->ncm->code}</td>
                                                            <td>{$produto->seu_nome}</td>
                                                            <td>Base do Cliente</td>
                                                            <td>Não - NCM Inexistente</td>
                                                        </tr>";
                                }
                            }else{

                                if(strlen($produto->ncm) < 8){
                                    $ncmProduto = '0'.$produto->ncm;
                                }else{
                                    $ncmProduto = $produto->ncm;
                                }

                                $ncm   = Ncm::where('cod_ncm','=',$ncmProduto)->get();

                                if(count($ncm) > 0){

                                    $gtin  = BCProdutoGtin::where('gtin','=',$produto->gtin)->get();

                                    if(count($gtin) == 0){

                                        $newProdutoBC = BCProduto::create([
                                            'status'        => "",
                                            'nome'          => "$object->description",
                                            'descricao'     => "$object->description",
                                            'preco_medio'   => isset($object->avg_price) ? $object->avg_price : 0 ,
                                            'preco_maximo'  => isset($object->max_price) ? $object->max_price : 0 ,
                                            'thumbnail'     => "$object->thumbnail",
                                            'altura'        => isset($object->height) ? $object->height : 0,
                                            'largura'       => isset($object->width) ? $object->width : 0,
                                            'comprimento'   => isset($object->length) ? $object->length : 0,
                                            'peso_liquido'  => isset($object->net_weight) ? $object->net_weight : 0,
                                            'cest_fk_id'    => 1, //isset($object->cest->code) ? $object->cest->code : 1 ,
                                            'gpc_fk_id'     => 1, //isset($object->gpc->code) ? $object->gpc->code : 1,
                                            'ncm_fk_id'     => isset($object->ncm->code) ? $object->ncm->code : $ncmProduto
                                        ]);

                                        $newProdutoBCGtin = BCProdutoGtin::create([
                                            'gtin'             => $object->gtin,
                                            'bc_produto_fk_id' => $newProdutoBC->id
                                        ]);

                                        $ncm = isset($object->ncm->code) ? $object->ncm->code : "$produto->ncm";

                                        $produtosNaoEncontrados.= "<tr>
                                                    <td>{$object->gtin}</td>
                                                    <td>{$ncmProduto}</td>
                                                    <td>{$object->description}</td>
                                                    <td>Cosmos</td>
                                                    <td>Sim</td>
                                                </tr>";

                                    }else{

                                        $produtosNaoEncontrados.= "<tr>
                                                    <td>{$produto->gtin}</td>
                                                    <td>{$ncmProduto}</td>
                                                    <td>{$produto->seu_nome}</td>
                                                    <td>Base Comparativa</td>
                                                    <td>Sim</td>
                                                </tr>";

                                    }

                                }else{

                                    $produtosNaoEncontrados.= "<tr>
                                                            <td>{$produto->gtin}</td>
                                                            <td>{$ncmProduto}</td>
                                                            <td>{$produto->seu_nome}</td>
                                                            <td>Base do Cliente</td>
                                                            <td>Não - NCM Inexistente</td>
                                                        </tr>";
                                }
                            }
                        }catch (\PDOException $e){
                            echo $e->getMessage();
                            die;
                        }
                    }
                }

                curl_close($curl);

            }
        }

        $produtosNaoEncontrados.= "</table>";

        echo $produtosNaoEncontrados;
        die;
    }
    public function monitoramentoLote(Request $request){

        // Recupera o numero do lote
        $loteId    = Input::get('loteId');
        // Obtém o objeto do lote
        $lote      = ClienteLote::find($loteId);

        // Quantidade de itens contidos dentro do lote
        $qtdItensLote = count($lote->produtos);

        $file      = $request->file('file');
        $delimiter = ',';
        $rows       = 0;
        $ret = array();

        if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $num = count($data);
                $rows++;

                for ($c=0; $c < $num; $c++) {
                    $data[$c] = trim($data[$c]);
                }

                array_push($ret,$data);
            }
            fclose($handle);

        }

        $rows--;

        // Verifica se a quantidade de itens dentro do lote é igual a quantidade de itens dentro do arquivo.
        if($rows < $qtdItensLote){
           echo json_encode(array(
               'success' => false,
               'msg'     => 'Quantidade de itens dentro do arquivo inferior a quantidade de itens dentro do Lote.'
           ));
        }/*elseif($rows > $qtdItensLote){
            echo json_encode(array(
                'success' => false,
                'msg'     => 'Quantidade de itens dentro do arquivo superior a quantidade de itens dentro do Lote.'
            ));
        }*/elseif($rows == 1){
            echo json_encode(array(
                'success' => false,
                'msg'     => 'Não foram encontrados itens dentro do arquivo.'
            ));
        }else{


//            [
//                [0] => CODIGO_DO_PRODUTO_NO_CLIENTE
//                [1] => NOME_DO_PRODUTO_NO_CLIENTE
//                [2] => NOME_PRODUTO_NA_BASE_COMPARATIVA
//                [3] => GTIN_NO_CLIENTE
//                [4] => GTIN_NA_BASE_COMPARATIVA
//                [5] => NCM_NO_CLIENTE
//                [6] => NCM_NA_BASE_COMPARATIVA
//                [7] => ALIQUOTA_ICMS_NO_CLIENTE
//                [8] => ALIQUOTA_ICMS_NA_BASE_COMPARATIVA
//                [9] => ALIQUOTA_PIS_NO_CLIENTE
//                [10] => ALIQUOTA_PIS_NA_BASE_COMPARATIVA
//                [11] => ALIQUOTA_COFINS_NO_CLIENTE
//                [12] => ALIQUOTA_COFINS_NA_BASE_COMPARATIVA
//                [13] => POSSUI_ST_NO_CLIENTE
//                [14] => POSSUI_ST_NA_BASE_COMPARATIVA
//                [15] => BASE_COMPARATIVA_PIS_CST
//                [16] => BASE_COMPARATIVA_COFINS_CST
//                [17] => ICMS_BASE_LEGAL
//                [18] => COFINS_BASE_LEGAL
//                [19] => PIS_BASE_LEGAL
//                [20] => NCM_CORRETO
//                [21] => ICMS_CORRETO
//                [22] => PIS_CORRETO
//                [23] => COFINS_CORRETO
//            ]

            /*[
                'gtin',
                'seu_codigo',
                'seu_nome',
                'ncm',
                'origem',
                'tributado_4',
                'uf_origem_fk',
                'possui_st',
                'aliquota_icm',
                'aliquota_pis',
                'aliquota_cofins',
                'bc_perfilcontabil_fk_id',
                'estab_origem_fk_id',
                'lote_fk_id',
                'status_fk_id',
                'trib_estab_origem_fk_id'
            ];*/

            // Remove o cabeçalho.

            unset($ret[0]);

            foreach ($ret as $index => $item) {

                LoteProduto::where('gtin',$item[3])->where('lote_fk_id',$loteId)->update([
                    'seu_nome'        => $item[2],
                    'gtin'            => (($item[4] != 'N/A') ? $item[4] : $item[3]),
                    'ncm'             => (($item[6] != 'N/A') ? $item[6] : $item[5]),
                    'possui_st'       => $item[13],
                    'aliquota_icm'    => $item[7],
                    'aliquota_pis'    => $item[9],
                    'aliquota_cofins' => $item[11],
                    'status_fk_id'    => 5,
                ]);
            }

            $lote->cliente_lote_status_fk_id =  4;
            $lote->save();

            echo json_encode(array(
                'success' => true,
                'msg'     => $qtdItensLote. " itens atualizados com sucesso!"
            ));
        }

    }
    public function consultaCosmos($gtin){

        $url = 'https://api.cosmos.bluesoft.com.br/gtins/'.$gtin.'.json';

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
            $object = json_decode($data);
            echo "<pre>";
            print_r($object);

        }else{

            $object = json_decode($data);
            echo "<pre>";
            print_r($object);
        }


    }

    public function upload(Request $request){

        /*
         * Lib : https://github.com/shuchkin/simplexlsx
        */

        $loteId = $request->lote_fk_id;

        if($loteId){

            $lote  = ClienteLote::find($loteId);
            $file  = $request->files;

            $xlsx = \SimpleXLSX::parse( $request->id_excel_file->getPathname() );

            $linhas            = $xlsx->rows();
            $qtdLinhas         = count($linhas);
            $erros             = array();
            $qtdItensInseridos = 0;

            unset($linhas[0]);

            foreach ($linhas as $index => $linha) {

                $erros[$index] =  array();

                /*
                 *
                 * Gabarito de validações de dados:
                        EAN/GTIN                             = número válido , maior que 6 dígitos e o último digito é um verificador ou seja, tem que ser válido.
                        NCM                                  = Deve conter ao menos 8 caracteres.
                        Tributado 4%                         = Deve ser Sim ou Não.
                        Origem                               = Deve ser Nacional ou Importado.
                        Unidade federativa                   = Deve conter um valor válido de UF.
                        Estabelecimento de Origem            = Precisa estar com todas as primeiras letras das palavras em maiusculo
                        Tributação do Estabelecimento        = Deve ser Lucro Real ou Lucro Presumido
                        Seu nome                             = Tudo maiusculo


                    Gabarito Estrutural dos dados
                        0 => 55935 ( CODIGO  CLIENTE )
                        1 => "A/E DIRETOR LINHA EPS COSTURA" ( NOME DE PRODUTOS )
                        2 => 7896260000000 ( EAN/GTIN )
                        3 => 94019010 ( NCM )
                        4 => "Nacional" (NACIONAL OU IMPORTADO )
                        5 => "Não" ( Tributado 4%? )
                        6 => "MG" ( UF Origem )
                        7 => "Comércio Varejista" ( Estabelecimento Origem )
                        8 => "Lucro Real" ( Tributação Estabelecimento Origem )
                        9 => "Não" ( Possuí Alíquota PIS )
                        10 => 0.018 ( Alíquota ICMS )
                        11 => 1.65 ( Alíquota PIS )
                        12 => 0.076 ( Alíquota COFINS )
                 */

                    $tamanhoGtins = [6,8,12,13,14];
                    $estadosBrasileiros = array(
                        'AC', 'AL', 'AP', 'AM',
                        'BA','CE', 'DF', 'ES',
                        'GO','MA', 'MT', 'MS',
                        'MG','PA', 'PB', 'PR',
                        'PE','PI','RJ', 'RN',
                        'RS', 'RO', 'RR', 'SC',
                        'SP','SE', 'TO',
                    );
                    $tributacoesDosEstabelecimentos = array(
                        'Lucro Real'     ,'lucro real'     ,'LUCRO REAL'     ,'lucro Real'     ,'Lucro real',
                        'Lucro Presumido','lucro presumido','LUCRO PRESUMIDO','lucro Presumido','Lucro presumido'
                    );

                    if(!in_array(strlen($linha[2]),$tamanhoGtins)){                                                    // Validação para verificar o tamanho dos GTIN's
                        $erros[$index]['GTIN'] = 'O tamanho do GTIN é inválido.';
                    }elseif(!strlen($linha[3]) >= 8){                                                                    // Validação para verificar o tamanho mínimo de 8 dígitos do NCM
                        $erros[$index]['NCM'] = 'O tamanho do NCM é inválido por não conter no mínimo de 8 dígitos.';
                    }elseif(!in_array($linha[4],array('Nacional','Importado'))){                                        // Validação para verificar se o produto é Nacional ou Importado
                        $erros[$index]['NACIONAL_OU_IMPORTADO'] = 'O Valor informado para o campo não está no padrão .';
                    }elseif(!in_array($linha[5],array('Sim','Não'))){                                                  // Validação para verificar Tributado 4% ou Possui ST
                        $erros[$index]['TRIBUTADO_4'] = 'O Valor informado para o campo não está no padrão.';
                    }elseif(!in_array($linha[6],$estadosBrasileiros)){                                                 // Validação para verificar UF de Origem
                        $erros[$index]['UF_ORIGEM'] = 'O Valor informado para o campo não está no padrão.';
                    }elseif(!in_array($linha[7],array('Comércio Varejista'))){                                          // Validação para verificar o Estabelecimento de Origem
                        $erros[$index]['ESTABELECIMENTO_ORIGEM'] = 'O Valor informado para o campo não está no padrão.';
                    }elseif(!in_array($linha[8],$tributacoesDosEstabelecimentos)){                                      // Validação para verificar a Tributação do Estabelecimento
                        $erros[$index]['TRIBUTACAO_ESTABELECIMENTO'] = 'O Valor informado para o campo não está no padrão.';
                    }elseif(!in_array($linha[9],array('Sim','Não'))){                          // Validação para verificar se possui aliquota de PIS
                        $erros[$index]['POSSUI_ALIQUOTA_PIS'] = 'O Valor informado para o campo não está no padrão.';
                    }elseif(is_null($linha[10])){                                                                       // Validação do valor da alíquota de ICMS
                        $erros[$index]['ALIQUOTA_ICMS'] = 'O Valor informado para o campo não pode ser vazio.';
                    }elseif(is_null($linha[11])){                                                                       // Validação do valor da alíquota de PIS
                        $erros[$index]['ALIQUOTA_PIS'] = 'O Valor informado para o campo não pode ser vazio.';
                    }elseif(is_null($linha[12])){                                                                       // Validação do valor da alíquota de COFINS
                        $erros[$index]['ALIQUOTA_CONFINS'] = 'O Valor informado para o campo não pode ser vazio.';
                    }else{

                        // Criação dos registros de produtos do Lote
                        switch ($linha[7]){
                            case 'Comércio Varejista':
                                $estabelecimentoDeOrigem = 2;
                                break;
                            case 'Comércio Atacadista':
                                $estabelecimentoDeOrigem = 1;
                                break;
                            default:
                                $estabelecimentoDeOrigem = 2;
                                break;
                        }

//                        echo "<pre>";
//                        print_r([
//                            'gtin'                      => $linha[2],
//                            'seu_codigo'                => $linha[0],
//                            'seu_nome'                  => $linha[1],
//                            'ncm'                       => $linha[3],
//                            'origem'                    => $linha[4],
//                            'tributado_4'               => $linha[5],
//                            'uf_origem_fk'              => $linha[6],
//                            'possui_st'                 => $linha[9],
//                            'aliquota_icm'              => $linha[10],
//                            'aliquota_pis'              => $linha[11],
//                            'aliquota_cofins'           => $linha[12],
//                            'bc_perfilcontabil_fk_id'   => null,
//                            'estab_origem_fk_id'        => $estabelecimentoDeOrigem,
//                            'lote_fk_id'                => $loteId,
//                            'status_fk_id'              => 1,
//                            'trib_estab_origem_fk_id'   => 1 //@todo: Verificar como buscar esta informação no banco.
//                        ]);

                        $lastInsertId = DB::SELECT ("SELECT lp.id  FROM public.lote_produto lp ORDER BY lp.id DESC OFFSET 0 LIMIT 1");
                        $lastInsertId =  $lastInsertId[0]->id;
                        $lastInsertId++;

                        LoteProduto::create([
                            'id'                        =>  $lastInsertId,
                            'gtin'                      => "$linha[2]",
                            'seu_codigo'                => "$linha[0]",
                            'seu_nome'                  => "$linha[1]",
                            'ncm'                       => "$linha[3]",
                            'origem'                    => "$linha[4]",
                            'tributado_4'               => "$linha[5]",
                            'uf_origem_fk'              => "$linha[6]",
                            'possui_st'                 => "$linha[9]",
                            'aliquota_icm'              => "$linha[10]",
                            'aliquota_pis'              => "$linha[11]",
                            'aliquota_cofins'           => "$linha[12]",
                            'bc_perfilcontabil_fk_id'   => NULL,
                            'estab_origem_fk_id'        => "$estabelecimentoDeOrigem",
                            'lote_fk_id'                => "$loteId",
                            'status_fk_id'              => "6",
                            'trib_estab_origem_fk_id'   => "1" //@todo: Verificar como buscar esta informação no banco.
                        ]);

                        $qtdItensInseridos++;
                    }


            }

            dd($erros);

            return response()->json([
                'erros'     => $erros,
                'inseridos' => $qtdItensInseridos
            ]);

        }
    }
}
