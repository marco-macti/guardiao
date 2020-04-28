<?php

namespace App\Http\Controllers;

use App\BCPerfilContabilCofins;
use App\BCPerfilContabilIcms;
use App\BCPerfilContabilPis;
use App\BCProduto;
use App\ClienteLote;
use App\LoteProduto;
use App\BCProdutoNcm;
use App\Ncm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

class IobController extends Controller
{
    public function index(){

        return view('iob.form');

    }

    public function importSheet(Request $request){

        // Dados do request
        $num_lote_cliente  = $request->get('lote');
        $cliente_fk_id     = $request->get('cliente');

        $file = $request->file('sheet');

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load ($file->getRealPath() );
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = $cells;
        }

        // Remove as celulas inicias de 0 a 7

        for ($i = 0; $i <= 8; $i++){
            unset($rows[$i]);
        }

        $contErros = 0;

        $totalProdutos =  count($rows);
        $totalProdutosAtualizados = 0;

        foreach ($rows as $key => $row) {

            $seu_codigo             = $row[0];
            $ncm                    = $row[4];
            $ncmNaoLocalizados      = [];

            $nomeEx  = explode(" ", $row[1]);

            if(count($nomeEx) > 1){
                $nomeReplace = $nomeEx[0]."|".$nomeEx[1];
            }
            else{
                $nomeReplace = $nomeEx[0];
            }

            $lote = ClienteLote::where('num_lote_cliente',$num_lote_cliente)
                                ->where('cliente_fk_id',$cliente_fk_id)
                                ->first();

            // Usando Facade de DB
            $produto = DB::select("SELECT
                                    *
                                    FROM
                                        public.lote_produto
                                    WHERE
                                        seu_codigo = '{$row[0]}'
                                    AND
                                        lote_fk_id = {$lote->id} ");

            if(!empty($produto)){

                // Busca pelo NCM informado da planilha, se não encontrar ele cadastra.

                if(!is_object(Ncm::where('cod_ncm',$ncm)->first())){

                    $contErros++;

                    array_push($ncmNaoLocalizados,$ncm);

                }
            }
        }

        if($contErros > 0){

            // Havendo mais que um erro , ele já exibe os erros

            dd($ncmNaoLocalizados);

        }else{

            foreach ($rows as $key => $row) {

                $lote = ClienteLote::where('num_lote_cliente', $num_lote_cliente)
                                ->where('cliente_fk_id'  , $cliente_fk_id)
                                ->first();

                $nomeEx  = explode(" ", $row[1]);

                if(count($nomeEx) > 1){
                    $nomeReplace = $nomeEx[0]."|".$nomeEx[1];
                }
                else{
                    $nomeReplace = $nomeEx[0];
                }

                try {

                      $produtoBC = DB::select("SELECT
                                            bcp.*,
                                            pc.id AS perfil_contabil_id,
                                            pcicms.id AS perfil_contabil_icms_id,
                                            pccofins.id AS perfil_contabil_confins_id,
                                            pcpis.id AS perfil_contabil_pis_id
                                        FROM bc_produto_gtin AS bcgtin
                                            INNER JOIN bc_produto AS bcp ON bcp.id = bcgtin.bc_produto_fk_id
                                            LEFT JOIN bc_perfil_contabil pc ON pc.ncm_fk_id = bcp.ncm_fk_id
                                            LEFT JOIN bc_perfil_contabil_icms pcicms ON pcicms.bc_perfil_contabil_fk_id = pc.id
                                            LEFT JOIN bc_perfilcontabil_cofins pccofins ON pccofins.bc_perfil_contabil_fk_id = pc.id
                                            LEFT JOIN bc_perfilcontabil_pis pcpis ON pcpis.bc_perfil_contabil_fk_id = pc.id
                                        WHERE bcp.nome = '{$row[1]}'
                                        AND pc.trib_estab_origem_fk_id = {$lote->cliente->enquadramento_tributario_fk_id}
                                        LIMIT 1 OFFSET 0");
                    
                } catch (\Throwable $th) {
                    echo $th->getMessage();
                    die;
                }

                if(isset($produtoBC[0])){

                    $produtoBC = $produtoBC[0];

                     // Atualiza o NCM com os dados disponíveis

                    $ncm = Ncm::where('cod_ncm',$row[4])->first();

                    if(!empty($row[61])){
                        $ncm->update([
                            'dt_inicio_vigencia' => Carbon::createFromFormat('d/m/Y',$row[61])->format('Y-m-d')
                        ]);
                    }

                    if(!empty($row[62])){
                        $ncm->update([
                            'dt_fim_vigencia'    => Carbon::createFromFormat('d/m/Y',$row[62])->format('Y-m-d')
                        ]);
                    }

                    // Atualiza o produto na base comparativa com os dados da planilha enviada

                    try{

                        try{

                         $bcp    = BCProduto::where('id',$produtoBC->id)->first();
                         $bcp->ncm_fk_id = $row[4];
                         $bcp->save();

                        $bcpNcm = BCProdutoNcm::where('bc_produto_fk_id',$produtoBC->id)
                                               ->where('ncm_fk_id'       ,$produtoBC->ncm_fk_id)
                                               ->first();
                        if(!is_object($bcpNcm)){

                            BCProdutoNcm::create([
                                                     'inicio'           => date('Y-m-d'),
                                                     'ncm_fk_id'        => $row[4],
                                                     'bc_produto_fk_id' => $produtoBC->id
                                                 ]);

                        }else{
                            $bcpNcm->update([
                                                     'inicio'           => date('Y-m-d'),
                                                     'ncm_fk_id'        => $row[4],
                                                     'bc_produto_fk_id' => $produtoBC->id
                                                 ]);
                        }                       

                                               
                       }catch(\Exception $e){
                          echo $e->getMessage();
                          die;
                       }

                    }catch(PDOException $e){
                           echo $e->getMessage();
                           die;
                    }

                    // Atualiza as aliqutoas de PIS na base comparativa com os dados da planilha enviada
                    $bcPerfilContabilPis = BCPerfilContabilPis::where('bc_perfil_contabil_fk_id',$produtoBC->perfil_contabil_id)->first();

                    // Verifica se existe a consulta no banco.
                    if(is_object($bcPerfilContabilPis)){

                        // Verifica se a aliquota está presente para o PIS
                        if(!empty($row[63])){
                            $bcPerfilContabilPis->update([
                                'aliquota'   => $row[63]
                            ]);
                        }

                        // Verifica se o CST está presente para o PIS
                        $bcPerfilContabilPis->update([
                            'cst'        => $row[64]
                        ]);

                        // Verifica se a base legal está presente para o PIS
                        $bcPerfilContabilPis->update([
                            'base_legal' => $row[65]
                        ]);

                        // Verifica se a data inicio está presente para o PIS
                        if(!empty($row[66])){
                            $bcPerfilContabilPis->inicio = Carbon::createFromFormat('d/m/Y',$row[66])->format('Y-m-d');
                        }
                        // Verifica se a data fim está presente para o PIS
                        if(!empty($row[67])){
                            $bcPerfilContabilPis->fim = Carbon::createFromFormat('d/m/Y',$row[67])->format('Y-m-d');
                        }

                        // Atualiza o objeto de instância com os dados imputados.
                        $bcPerfilContabilPis->save();

                    }

                    // Atualiza as aliqutoas de COFINS na base comparativa com os dados da planilha enviada

                    $bcPerfilContabilCofins = BCPerfilContabilCofins::where('bc_perfil_contabil_fk_id',$produtoBC->perfil_contabil_id)->first();

                    if(is_object($bcPerfilContabilCofins)){

                        if(!empty($row[68])){
                            // Verifica se a aliquota está presente para o COFINS
                            $bcPerfilContabilCofins->update([
                                'aliquota' => $row[68]
                            ]);
                        }

                        // Verifica se o CST está presente para o COFINS
                        if(!empty($row[69])){
                            $bcPerfilContabilCofins->update([
                                'cst'  => $row[69]
                            ]);
                        }

                        // Verifica se a Base Legal está presente para o COFINS
                        if(!empty($row[70])){
                            $bcPerfilContabilCofins->update([
                                'base_legal' => $row[70]
                            ]);
                        }

                        // Verifica se a data inicio está presente para o COFINS
                        if(!empty($row[71])){
                            $bcPerfilContabilCofins->inicio = Carbon::createFromFormat('d/m/Y',$row[71])->format('Y-m-d');
                        }

                        // Verifica se a data fim está presente para o COFINS
                        if(!empty($row[72])){
                            $bcPerfilContabilCofins->fim = Carbon::createFromFormat('d/m/Y',$row[72])->format('Y-m-d');
                        }

                        // Atualiza o objeto de instância com os dados imputados.
                        $bcPerfilContabilCofins->save();

                    }

                    // Atualiza as aliqutoas de ICMS na base comparativa com os dados da planilha enviada

                    $bcPerfilContabilIcms = BCPerfilContabilIcms::where('bc_perfil_contabil_fk_id',$produtoBC->perfil_contabil_id)->first();

                    if(is_object($bcPerfilContabilIcms)){

                        // Verifica se a aliquota está presente para o ICMS
                        if(!empty($row[58])){
                            $bcPerfilContabilIcms->update([
                                'aliquota'   => $row[58]
                            ]);
                        }

                        // Verifica se o CST está presente para o ICMS
                        if(!empty($row[59])){

                            $bcPerfilContabilIcms->update([
                                'cst'        => $row[59],
                            ]);
                        }

                        // Verifica se a base legal está presente para o ICMS
                        if(!empty($row[60])){
                            $bcPerfilContabilIcms->update([
                                'base_legal' => $row[60]
                            ]);
                        }

                        // Verifica se a data inicio está presente para o ICMS
                        if(!empty($row[61])){
                            $bcPerfilContabilIcms->inicio = Carbon::createFromFormat('d/m/Y',$row[61])->format('Y-m-d');
                        }

                        // Verifica se a data fim está presente para o ICMS
                        if(!empty($row[62])){
                            $bcPerfilContabilIcms->fim = Carbon::createFromFormat('d/m/Y',$row[62])->format('Y-m-d');
                        }

                        $bcPerfilContabilIcms->save();
                    }

                }


                // Atualiza o NCM do produto no LOTE

                $produtoLoteX = LoteProduto::where('seu_nome',"{$row[1]}")
                                           ->where('lote_fk_id',$lote->id)
                                           ->first();

                if(is_object($produtoLoteX)){
                    $produtoLoteX->update([
                        'ncm' =>$row[4]
                    ]);
                }

                
                $totalProdutosAtualizados++;
            }
        }

        echo "Total de Produtos : {$totalProdutos} <br/>";
        echo "Total de Produtos atualizados: {$totalProdutosAtualizados} <br/>";

    }
}
