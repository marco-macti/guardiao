<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\LoteProduto;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function index(){
        return response()->json([
            'APP_NAME' => 'Guardiao Tributário',
            'API_V'    => '0.0.1'
        ]);
    }

    public function importarBCProdutoAux(){

        $path = "app".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."bc_produto_aux".DIRECTORY_SEPARATOR."tbl_eans_780.json";

        if(file_exists(storage_path($path))){
            $file    = json_decode(file_get_contents(storage_path($path)));
            $records = $file->{"RECORDS"};

            foreach ($records as $index => $record) {
                echo $record['gtin'];
            }

            echo "Processo finalizado";
        }else{
            echo "File or path not found";
        }

    }

    public function updateProdutosLoteCliente(){

        $cliente = Cliente::find(8);

        $produtos =  DB::select("SELECT
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

        foreach ($produtos as $index => $produto) {

            echo "Atualizando produto". $index. " de ". count($produtos);
            echo "<br/>";

            try {
                LoteProduto::find($produto->lote_produto_id)->update([
                    'bc_perfilcontabil_fk_id' => $produto->bc_pefil_contabil_id
                ]);
            }catch (\PDOException $e){
                echo $e->getMessage();
                die;
            }

        }
    }

    public function getClientes(){

        $clientes = Cliente::all()->pluck('razao_social','id');

        return response()->json($clientes);
    }

    public function getClienteLotes(Cliente $cliente){

        $lotes  = $cliente->lotes();
        $result = [];

        foreach ($lotes as $key => $lote) {

            $result[$key]['numero']     = $lote->num_lote_cliente;
            $result[$key]['observacao'] = $lote->anotacoes;

        }

        return response()->json($result);

    }
}
