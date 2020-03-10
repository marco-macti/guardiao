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
    public function importarProdutosCosmos(Request $request){

        $ncm = Ncm::where('cod_ncm','=',$request->get('ncm'))->get();

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
                    'ncm_fk_id'     => $ncm
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

            try{
                $ncm = Ncm::create([
                    'cod_ncm'   => "$request->get('ncm')",
                    'descricao' => "$request->get('ncm_interno')"
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
