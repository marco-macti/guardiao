<?php

namespace App\Http\Controllers;

use App\BCPerfilContabilCofins;
use App\BCPerfilContabilIcms;
use App\BCPerfilContabilPis;
use App\BCProduto;
use App\Cliente;
use App\ClienteLote;
use App\LoteProduto;
use App\BCProdutoNcm;
use App\BCPerfilContabil;
use App\Ncm;
use App\Cest;
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
        $ncmNaoLocalizados      = [];

        foreach ($rows as $key => $row) {

            $seu_codigo             = $row[0];
            $ncm                    = $row[4];
            

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

                    $ncmNaoLocalizados[$contErros] = $ncm;
                    
                    //array_push($ncmNaoLocalizados,$produto[0]->ncm);

                }
            }
        }


        if($contErros > 0){

            // Havendo mais que um erro , ele já exibe os erros
            dd($ncmNaoLocalizados);

        }else{

            foreach ($rows as $key => $row) {



                if(!empty($row[0])){

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
                        /*AND pc.trib_estab_origem_fk_id = {$lote->cliente->enquadramento_tributario_fk_id}*/
                        LIMIT 1 OFFSET 0");

                    } catch (\Throwable $th) {
                        echo $th->getMessage();
                        die;
                    }

                    if(isset($produtoBC[0])){


                        if(!empty($row[45])){
                            //verifica se existe o cest
                            $cest = DB::select("SELECT * FROM cest WHERE id = '".$row[45]."'");
                            
                            if(count($cest) > 0){
                                $cest_id = $cest[0]->id;
                            }
                            else{

                                DB::select("INSERT INTO cest (id, denominacao) VALUES ('".$row[45]."', 'Substituição tributária')");
                                $cest = DB::select("SELECT * FROM cest WHERE id = '".$row[45]."'");

                                $cest_id = $cest[0]->id;

                            }

                            if(!empty($produtoBC[0]->id)){
                                DB::select("UPDATE bc_produto SET cest_fk_id = ".$cest_id." WHERE id = ".$produtoBC[0]->id."");
                            }


                        // Atualiza o MVA no CEST
                        DB::select("UPDATE cest SET mva = ".$row[43]." WHERE id = ".$row[45]."");

                        }
                        else{
                            $cest_id = 1;
                        }

                        if(empty($produtoBC[0]->perfil_contabil_id)){
                            $cliente = Cliente::where('id',$cliente_fk_id)->first();
                            $bcPerfilContabil = BCPerfilContabil::create(
                                  [
                                        'dt_ult_atualizacao'       => date('Y-m-d'),
                                        'origem'                   => !empty($row[6])  ? $row[6]  : '',
                                        'tributado_4'              => !empty($row[7])  ? $row[7]  : '',
                                        'operacao'                 => !empty($row[8])  ? $row[8]  : '',
                                        'uf_origem_fk'             => !empty($row[9])  ? $row[9]  : '',
                                        'uf_dest_fk'               => !empty($row[10]) ? $row[10] : '',
                                        'cnae_classe_fk_id'        => $cliente->cnae_fk_id,
                                        'dest_mercadoria_fk_id'    => $cliente->destinacao_mercadoria_fk_id, //4,
                                        'estab_dest_fk_id'         => $cliente->estabelecimento_destino_fk_id,
                                        'estab_origem_fk_id'       => 2,
                                        'ncm_fk_id'                => !empty($row[4])  ? $row[4]  : '',
                                        'trib_estab_destino_fk_id' => $cliente->tributacao_estabelecimento_destino_fk_id,
                                        'trib_estab_origem_fk_id'  => 1,
                                        'pendencia'                => false,
                                        'id_operacao'              => '',
                                        'id_produto'               => ''
                                    ]
                                );  

                            $produtoBC[0]->perfil_contabil_id = $bcPerfilContabil->id;
                        }

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
                            if(!empty($row[64])){
                                $bcPerfilContabilPis->update([
                                    'cst'        => $row[64]
                                ]);
                            }
                            

                            // Verifica se a base legal está presente para o PIS
                            if(!empty($row[65])){
                                $bcPerfilContabilPis->update([
                                    'base_legal' => $row[65]
                                ]);
                            }
                            
                            // Verifica se a data inicio está presente para o PIS
                            if(!empty($row[66])){
                                $bcPerfilContabilPis->inicio = Carbon::createFromFormat('d/m/Y',$row[66])->format('Y-m-d');
                            }
                            else{
                                $bcPerfilContabilPis->inicio = Carbon::createFromFormat('d/m/Y',date('d/m/Y'))->format('Y-m-d');   
                            }

                            // Verifica se a data fim está presente para o PIS
                            if(!empty($row[67])){
                                $bcPerfilContabilPis->fim = Carbon::createFromFormat('d/m/Y',$row[67])->format('Y-m-d');
                            }

                            if(!empty($produtoBC->perfil_contabil_id)){
                                // Atualiza o objeto de instância com os dados imputados.
                                $bcPerfilContabilPis->save();
                            }

                        }else{
                            // Criar perfil contábil para PIS

                            $bcPerfilContabilPis = new BCPerfilContabilPis();
                            $bcPerfilContabilPis->bc_perfil_contabil_fk_id = $produtoBC->perfil_contabil_id;

                            if(empty($produtoBC->perfil_contabil_id)){
                                $bcPerfilContabilPis->bc_perfil_contabil_fk_id = null;
                            }

                            // Verifica se a aliquota está presente para o PIS
                            if(!empty($row[63])){
                                $bcPerfilContabilPis->aliquota  = $row[63];
                            }else{
                                $bcPerfilContabilPis->aliquota  = 0.0;
                            }

                            // Verifica se o CST está presente para o PIS

                            if(!empty($row[64])){
                                $bcPerfilContabilPis->cst  = $row[64];
                            }else{
                                $bcPerfilContabilPis->cst  = "";
                            }
                            

                            // Verifica se a base legal está presente para o PIS
                            if(!empty($row[64])){
                                $bcPerfilContabilPis->base_legal = $row[65];
                            }else{
                                $bcPerfilContabilPis->base_legal = "";
                            }
                            
                            // Verifica se a data inicio está presente para o PIS
                            if(!empty($row[66])){
                                $bcPerfilContabilPis->inicio = Carbon::createFromFormat('d/m/Y',$row[66])->format('Y-m-d');
                            }else{
                                $bcPerfilContabilPis->inicio = Carbon::createFromFormat('d/m/Y',date('d/m/Y'))->format('Y-m-d');   
                            }

                            // Verifica se a data fim está presente para o PIS
                            if(!empty($row[67])){
                                $bcPerfilContabilPis->fim = Carbon::createFromFormat('d/m/Y',$row[67])->format('Y-m-d');
                            }

                            if(!empty($produtoBC->perfil_contabil_id)){
                                // Atualiza o objeto de instância com os dados imputados.
                                $bcPerfilContabilPis->save();
                            }
                               
                        }

                        // Atualiza as aliqutoas de COFINS na base comparativa com os dados da planilha enviada

                        $bcPerfilContabilCofins = BCPerfilContabilCofins::where('bc_perfil_contabil_fk_id',$produtoBC->perfil_contabil_id)->first();

                        if(is_object($bcPerfilContabilCofins)){

                            if(isset($row[68])){
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
                            }else{
                                $bcPerfilContabilCofins->inicio = Carbon::createFromFormat('d/m/Y',date('d/m/Y'))->format('Y-m-d');   
                            }

                            // Verifica se a data fim está presente para o COFINS
                            if(!empty($row[72])){
                                $bcPerfilContabilCofins->fim = Carbon::createFromFormat('d/m/Y',$row[72])->format('Y-m-d');
                            }

                            if(!empty($produtoBC->perfil_contabil_id)){
                                // Atualiza o objeto de instância com os dados imputados.
                                $bcPerfilContabilCofins->save();
                            }

                        }else{

                            // Criar perfil contábil para COFINS 

                             $bcPerfilContabilCofins = new BCPerfilContabilCofins();

                             $bcPerfilContabilCofins->bc_perfil_contabil_fk_id = $produtoBC->perfil_contabil_id;

                            // Verifica se a aliquota está presente para o PIS
                            if(!empty($row[68])){
                                $bcPerfilContabilCofins->aliquota  = $row[68];
                            }else{
                                 $bcPerfilContabilCofins->aliquota  = 0.0;
                            }

                            // Verifica se o CST está presente para o PIS

                            if(!empty($row[69])){
                                $bcPerfilContabilCofins->cst  = $row[69];
                            }
                            else{
                                $bcPerfilContabilCofins->cst  = "";
                            }
                            

                            // Verifica se a base legal está presente para o PIS
                            if(!empty($row[70])){
                                $bcPerfilContabilCofins->base_legal = "{$row[70]}";
                            }else{
                                $bcPerfilContabilCofins->base_legal  = "";
                            }
                            
                            // Verifica se a data inicio está presente para o PIS
                            if(!empty($row[71])){
                                $bcPerfilContabilCofins->inicio = Carbon::createFromFormat('d/m/Y',$row[71])->format('Y-m-d');
                            }else{
                                $bcPerfilContabilCofins->inicio = Carbon::createFromFormat('d/m/Y',date('d/m/Y'))->format('Y-m-d');   
                            }
                            // Verifica se a data fim está presente para o PIS
                            if(!empty($row[72])){
                                $bcPerfilContabilCofins->fim = Carbon::createFromFormat('d/m/Y',$row[72])->format('Y-m-d');
                            }

                            if(!empty($produtoBC->perfil_contabil_id)){
                                // Atualiza o objeto de instância com os dados imputados.
                                
                                $bcPerfilContabilCofins->save(); 
                            }       
                            
                        }

                        // Atualiza as aliqutoas de ICMS na base comparativa com os dados da planilha enviada

                        $bcPerfilContabilIcms = BCPerfilContabilIcms::where('bc_perfil_contabil_fk_id',$produtoBC->perfil_contabil_id)->first();
                        
                        if(is_object($bcPerfilContabilIcms)){

                            // Verifica se a aliquota está presente para o ICMS
                            if(!isset($row[17])){
                                $bcPerfilContabilIcms->update([
                                    'aliquota'   => $row[17]
                                ]);
                            }

                            // Verifica se o CST está presente para o ICMS
                            if(!empty($row[54])){

                                $bcPerfilContabilIcms->update([
                                    'possui_st'        => "Sim",
                                ]);
                            }else{
                                $bcPerfilContabilIcms->update([
                                    'possui_st'        => "Não",
                                ]);
                            }

                            // Verifica se a base legal está presente para o ICMS
                            if(!empty($row[54])){
                                $bcPerfilContabilIcms->update([
                                    'base_legal_st' => $row[54]
                                ]);
                            }

                            // Verifica se a data inicio está presente para o ICMS
                            if(!empty($row[56])){
                                $bcPerfilContabilIcms->inicio = Carbon::createFromFormat('d/m/Y',$row[56])->format('Y-m-d');
                            }else{
                                $bcPerfilContabilIcms->inicio = Carbon::createFromFormat('d/m/Y',date('d/m/Y'))->format('Y-m-d');   
                            }

                            // Verifica se a data fim está presente para o ICMS
                            if(!empty($row[57])){
                                $bcPerfilContabilIcms->fim = Carbon::createFromFormat('d/m/Y',$row[57])->format('Y-m-d');
                            }

                            $bcPerfilContabilIcms->save();

                        }else{

                            // Criar Perfil Contabil para ICMS

                            $bcPerfilContabilIcms = new BCPerfilContabilIcms();
                            
                            $bcPerfilContabilIcms->bc_perfil_contabil_fk_id = $produtoBC->perfil_contabil_id;


                            // Verifica se a aliquota está presente para o PIS
                            if(!empty($row[17])){
                                $bcPerfilContabilIcms->aliquota  = $row[17];
                            }
                            else{
                                $bcPerfilContabilIcms->aliquota  = 0.0;
                            }


                            // Verifica se o CST está presente para o PIS

                            if(!empty($row[54])){
                                $bcPerfilContabilIcms->possui_st  = "Sim";
                            }else{
                                $bcPerfilContabilIcms->possui_st  = "Não";
                            }
                            

                            // Verifica se a base legal está presente para o PIS
                            if(!empty($row[54])){
                                $bcPerfilContabilIcms->base_legal_st = $row[54];
                            }else{
                                $bcPerfilContabilIcms->base_legal_st = " ";
                            }
                            
                            // Verifica se a data inicio está presente para o PIS
                            if(!empty($row[56])){
                                $bcPerfilContabilIcms->inicio = Carbon::createFromFormat('d/m/Y',$row[56])->format('Y-m-d');
                            }
                            else{
                                $bcPerfilContabilIcms->inicio = Carbon::createFromFormat('d/m/Y',date("d/m/Y"))->format('Y-m-d');
                            }
                            // Verifica se a data fim está presente para o PIS
                            if(!empty($row[57])){
                                $bcPerfilContabilIcms->fim = Carbon::createFromFormat('d/m/Y',$row[57])->format('Y-m-d');
                            }

                            if(!empty($produtoBC->perfil_contabil_id)){
                                
                                $bcPerfilContabilIcms->save();
                            }
                        }

                    }else{
                       
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

                        // Cria o produto na base comparativa com os dados da planilha enviada

                        try{

                            try{

                                if(!empty($row[45])){
                                        //verifica se existe o cest
                                        //$cest = Cest::where('id', $row[45])->first();
                                    $cest = DB::select("SELECT * FROM cest WHERE id = '".$row[45]."'");
                                    
                                        if(count($cest) > 0){
                                            $cest_id = $cest[0]->id;
                                         
                                        }
                                        else{

                                            DB::select("INSERT INTO cest (id, denominacao) VALUES ('".$row[45]."', 'Substituição tributária')");
                                            $cest = DB::select("SELECT * FROM cest WHERE id = '".$row[45]."'");
                                            $cest_id = $cest[0]->id;                                        

                                        }
                                        // Atualiza o MVA no CEST
                                        DB::select("UPDATE cest SET mva = ".$row[43]." WHERE id = ".$row[45]."");
                                    }
                                    else{
                                        $cest_id = 1;
                                    }

                                    $produtoBC = BCProduto::create([
                                                    'status' => '',
                                                    'nome'  => $row[1],
                                                    'descricao' => $row[1],
                                                    'preco_medio'=> '0',
                                                    'preco_maximo'=> '0',
                                                    'thumbnail'=> '',
                                                    'altura'=> '0',
                                                    'largura'=> '0',
                                                    'comprimento'=> '0',
                                                    'peso_liquido'=> '0',
                                                    'cest_fk_id' => $cest_id,
                                                    'gpc_fk_id' => 1,
                                                    'ncm_fk_id'    => $row[4]
                                                    ]);


                                $bcpNcm = BCProdutoNcm::where('bc_produto_fk_id',$produtoBC->id)
                                                   ->where('ncm_fk_id'          ,$produtoBC->ncm_fk_id)
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

                        $perfilContabil = BCPerfilContabil::where('ncm_fk_id',$row[4])->first();


                        // Verifica a existência do perfil contábil

                        if(is_object($perfilContabil)){
                           
                            $perfil_contabil_id = $perfilContabil->id;
                        }else{

                            $cliente = Cliente::where('id',$cliente_fk_id)->first();
                           
                            try{

                                $bcPerfilContabil = BCPerfilContabil::create(
                                  [
                                        'dt_ult_atualizacao'       => date('Y-m-d'),
                                        'origem'                   => !empty($row[6])  ? $row[6]  : '',
                                        'tributado_4'              => !empty($row[7])  ? $row[7]  : '',
                                        'operacao'                 => !empty($row[8])  ? $row[8]  : '',
                                        'uf_origem_fk'             => !empty($row[9])  ? $row[9]  : '',
                                        'uf_dest_fk'               => !empty($row[10]) ? $row[10] : '',
                                        'cnae_classe_fk_id'        => $cliente->cnae_fk_id,
                                        'dest_mercadoria_fk_id'    => $cliente->destinacao_mercadoria_fk_id, //4,
                                        'estab_dest_fk_id'         => $cliente->estabelecimento_destino_fk_id,
                                        'estab_origem_fk_id'       => 2,
                                        'ncm_fk_id'                => !empty($row[4])  ? $row[4]  : '',
                                        'trib_estab_destino_fk_id' => $cliente->tributacao_estabelecimento_destino_fk_id,
                                        'trib_estab_origem_fk_id'  => 1,
                                        'pendencia'                => false,
                                        'id_operacao'              => '',
                                        'id_produto'               => ''
                                    ]
                                );  

                                $perfil_contabil_id = $bcPerfilContabil->id;

                            }catch(\PDOException $e){
                                dd($e->getMessage());
                            }
                              
                        }

                        // Atualiza as aliqutoas de PIS na base comparativa com os dados da planilha enviada
                        $bcPerfilContabilPis = BCPerfilContabilPis::where('bc_perfil_contabil_fk_id',$perfil_contabil_id)->first();

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
                            }else{
                                $bcPerfilContabilPis->inicio = Carbon::createFromFormat('d/m/Y',date("d/m/Y"))->format('Y-m-d');
                            }

                            // Verifica se a data fim está presente para o PIS
                            if(!empty($row[67])){
                                $bcPerfilContabilPis->fim = Carbon::createFromFormat('d/m/Y',$row[67])->format('Y-m-d');
                            }

                            // Atualiza o objeto de instância com os dados imputados.
                            $bcPerfilContabilPis->save();


                        }else{

                                if(!empty($row[66])){
                                    $bcPerfilContabilPisinicio = Carbon::createFromFormat('d/m/Y',$row[66])->format('Y-m-d');
                                }else{
                                    $bcPerfilContabilPisinicio = Carbon::createFromFormat('d/m/Y',date("d/m/Y"))->format('Y-m-d');
                                }

                                if(!empty($row[63])){

                                    DB::select("INSERT INTO public.bc_perfilcontabil_pis(
                                                aliquota, cst, base_legal, inicio, bc_perfil_contabil_fk_id)
                                                VALUES (".$row[63].", '".$row[64]."', '".$row[65]."', '".$bcPerfilContabilPisinicio."', ".$perfil_contabil_id.")");

                                    /*BCPerfilContabilPis::create([
                                        'aliquota' => $row[63],
                                        'cst'      => $row[64],
                                        'base_legal' => $row[65],
                                        'inicio' => $bcPerfilContabilPisinicio,
                                        'bc_perfil_contabil_fk_id' => $perfil_contabil_id
                                    ]);*/
                                }

                            }

                        // Atualiza as aliqutoas de COFINS na base comparativa com os dados da planilha enviada

                        $bcPerfilContabilCofins = BCPerfilContabilCofins::where('bc_perfil_contabil_fk_id',$perfil_contabil_id)->first();

                        if(is_object($bcPerfilContabilCofins)){

                            if(isset($row[68])){
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
                            else{
                                $bcPerfilContabilCofins->inicio = Carbon::createFromFormat('d/m/Y',date('d/m/Y'))->format('Y-m-d');
                            }

                            // Verifica se a data fim está presente para o COFINS
                            if(!empty($row[72])){
                                $bcPerfilContabilCofins->fim = Carbon::createFromFormat('d/m/Y',$row[72])->format('Y-m-d');
                            }


                            // Atualiza o objeto de instância com os dados imputados.
                            $bcPerfilContabilCofins->save();

                        }else{

                                if(!empty($row[71])){
                                    $bcPerfilContabilCofinsinicio = Carbon::createFromFormat('d/m/Y',$row[71])->format('Y-m-d');
                                }else{
                                    $bcPerfilContabilCofinsinicio = Carbon::createFromFormat('d/m/Y',date("d/m/Y"))->format('Y-m-d');
                                }

                                DB::select("INSERT INTO public.bc_perfilcontabil_cofins(
                                                aliquota, cst, base_legal, inicio, bc_perfil_contabil_fk_id)
                                                VALUES (".$row[68].", '".$row[69]."', '".$row[70]."', '".$bcPerfilContabilCofinsinicio."', ".$perfil_contabil_id.")");

                                /*BCPerfilContabilCofins::create([
                                    'aliquota' => $row[68],
                                    'cst'      => $row[69],
                                    'base_legal' => $row[70],
                                    'inicio' => $bcPerfilContabilCofinsinicio,
                                    'bc_perfil_contabil_fk_id' => $perfil_contabil_id
                                ]);*/
                                
                            }

                        // Atualiza as aliqutoas de ICMS na base comparativa com os dados da planilha enviada

                        $bcPerfilContabilIcms = BCPerfilContabilIcms::where('bc_perfil_contabil_fk_id',$perfil_contabil_id)->first();

                        if(is_object($bcPerfilContabilIcms)){

                            // Verifica se a aliquota está presente para o ICMS
                            if(!isset($row[17])){
                                $bcPerfilContabilIcms->update([
                                    'aliquota'   => $row[17]
                                ]);
                            }

                            // Verifica se o ST está presente para o ICMS
                            if(!empty($row[54])){

                                $bcPerfilContabilIcms->update([
                                    'possui_st'        => "Sim",
                                ]);
                            }else{
                                $bcPerfilContabilIcms->update([
                                    'possui_st'        => "Não",
                                ]);
                                
                            }

                            // Verifica se a base legal está presente para o ICMS
                            if(!empty($row[54])){
                                $bcPerfilContabilIcms->update([
                                    'base_legal_st' => $row[54]
                                ]);
                            }

                            // Verifica se a data inicio está presente para o ICMS
                            if(!empty($row[56])){
                                $bcPerfilContabilIcms->inicio = Carbon::createFromFormat('d/m/Y',$row[56])->format('Y-m-d');
                            }

                            // Verifica se a data fim está presente para o ICMS
                            if(!empty($row[57])){
                                $bcPerfilContabilIcms->fim = Carbon::createFromFormat('d/m/Y',$row[57])->format('Y-m-d');
                            }

                            $bcPerfilContabilIcms->save();
                        }else{

                                if(!empty($row[56])){
                                    $bcPerfilContabilicmsinicio = Carbon::createFromFormat('d/m/Y',$row[56])->format('Y-m-d');
                                }else{
                                    $bcPerfilContabilicmsinicio = Carbon::createFromFormat('d/m/Y',date("d/m/Y"))->format('Y-m-d');
                                }

                                if(!empty($row[54])){

                                   $possui_st_c = 'Sim';
                                }else{
                                    $possui_st_c = 'Não';
                                    
                                }

                                DB::select("INSERT INTO public.bc_perfil_contabil_icms(
                                                aliquota, possui_st, base_legal_st, inicio, bc_perfil_contabil_fk_id)
                                                VALUES (".$row[17].", '".$possui_st_c."', '".$row[54]."', '".$bcPerfilContabilicmsinicio."', ".$perfil_contabil_id.")");

                                /*BCPerfilContabilIcms::create([
                                    'aliquota' => $row[17],
                                    'possui_st' => $possui_st_c,
                                    'cst'      => $row[18],
                                    'base_legal' => $row[54],
                                    'inicio' => $bcPerfilContabilicmsinicio,
                                    'bc_perfil_contabil_fk_id' => $perfil_contabil_id
                                ]);*/
                                
                            }

                    }


                    // Atualiza o NCM do produto no LOTE

                    $nome = trim($row[1]);
                    

                    //$produtoLoteX = DB::select("SELECT * from public.lote_produto where seu_nome = '{$nome}' and lote_fk_id = {$lote->id}");
                    $produtoLoteX = LoteProduto::where('seu_nome',$nome)
                                               ->where('lote_fk_id',$lote->id)
                                               ->first();

                    if(is_object($produtoLoteX)){                        
                        
                        $produtoLoteX->ncm = $row[4];

                        if(empty($row[54])){
                            $produtoLoteX->possui_st = 'Não';
                        }            
                        else{
                            $produtoLoteX->possui_st = 'Sim';
                        }    
                            
                        $produtoLoteX->save();

                    }

                }

            
                $totalProdutosAtualizados++;
            }
        }

        echo "Total de Produtos : {$totalProdutos} <br/>";
        echo "Total de Produtos atualizados: {$totalProdutosAtualizados} <br/>";

    }
}
