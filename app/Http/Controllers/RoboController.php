<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BCProduto;
use App\BCProdutoAux;
use App\BCProdutoGtin;
use App\ClienteLote;
use App\LoteProduto;
use App\Ncm;
use App\Cliente;

class RoboController extends Controller
{
    public function index($pg, $parametro, $indice_produtos){

        $pg           = empty($pg) ? '' : $pg;
        $parametro    = empty($parametro) ? '' : $parametro;
        $offset       = 0;

        if($indice_produtos > 1000){

            $offset = $offset + 1000;

        }

        $produtos     = BCProduto::select('nome')->offset($offset)->limit('1000')->get();
        $produto_ex   = explode(" ", $produtos[$indice_produtos]->nome);
        $nome_produto = $produto_ex[0];
        $parametro    = $nome_produto;

        return view('robo.index')->with('parametro', $parametro)->with('pg', $pg)->with('indice_produtos', $indice_produtos)->with('produtos', $produtos);
        
    }

    public function paginaInterna(Request $request){
        $url = $request->get('url');
        $cosmos_pg_interna = file_get_contents($url);
        echo $cosmos_pg_interna;
        die;
    }
    public function importarProdutosCosmos(Request $request){

        $ncm_robo = str_replace(".", "", $request->get('ncm'));
        $ncm = Ncm::where('cod_ncm','=',$ncm_robo)->get();

        if(count($ncm) > 0){
            
            $gtin  = BCProdutoGtin::where('gtin','=',$request->gtin)->get();

            if(count($gtin) == 0){

                $newProdutoBC = BCProduto::create([
                    'status'        => "",
                    'nome'          => "$request->nome_produto",
                    'descricao'     => "$request->nome_produto",
                    'preco_medio'   => 0 ,
                    'preco_maximo'  => 0 ,
                    'thumbnail'     => "$request->img",
                    'altura'        => 0,
                    'largura'       => 0,
                    'comprimento'   => 0,
                    'peso_liquido'  => 0,
                    'cest_fk_id'    => 1 ,
                    'gpc_fk_id'     => 1,
                    'ncm_fk_id'     => $ncm_robo
                ]);
                
                $newProdutoBCGtin = BCProdutoGtin::create([
                    'gtin'             => $request->gtin,
                    'bc_produto_fk_id' => $newProdutoBC->id
                ]);

                return response()->json([
                    'success' => true,
                    'msg' => 'Produto inserido com sucesso!'
                ]);

            }
        }else{

            if(strlen($ncm_robo) == 8){
                try{

                    $ncm = Ncm::create([
                        'cod_ncm'            => $ncm_robo,
                        'descricao'          => $request->get('ncm_interno'),
                        'dt_inicio_vigencia' => date('Y-m-d')
                    ]);
                }catch(PDOException $e){
                    echo $e->getMessage();
                }    

                $gtin  = BCProdutoGtin::where('gtin','=',$request->gtin)->get();

                if(count($gtin) == 0){

                    $newProdutoBC = BCProduto::create([
                        'status'        => "",
                        'nome'          => "$request->nome_produto",
                        'descricao'     => "$request->nome_produto",
                        'preco_medio'   => 0 ,
                        'preco_maximo'  => 0 ,
                        'thumbnail'     => "$request->img",
                        'altura'        => 0,
                        'largura'       => 0,
                        'comprimento'   => 0,
                        'peso_liquido'  => 0,
                        'cest_fk_id'    => 1 ,
                        'gpc_fk_id'     => 1,
                        'ncm_fk_id'     => $ncm
                    ]);
        
                    $newProdutoBCGtin = BCProdutoGtin::create([
                        'gtin'             => $request->gtin,
                        'bc_produto_fk_id' => $newProdutoBC->id
                    ]);

                    return response()->json([
                        'success' => true,
                        'msg' => 'Produto inserido com sucesso.'
                    ]);

                }
                        
            }
        }

        /*
            $ncm = Ncm::where('cod_ncm','=',$ncmProduto)->get();
            $gtin  = BCProdutoGtin::where('gtin','=',$produto->gtin)->get();

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
        */
    }
}
