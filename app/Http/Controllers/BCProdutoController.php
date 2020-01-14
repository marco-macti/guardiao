<?php

namespace App\Http\Controllers;

use App\BCProduto;
use App\BCProdutoGtin;
use App\BCProdutoNcm;
use App\Ncm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BCProdutoController extends Controller
{
    public function find($id){

        if(!empty($id)){

            $result = DB::select("SELECT
                                            bcp.id,
                                            bcp.nome,
                                            bcpgtin.gtin,
                                            bcpncm.ncm_fk_id
                                        FROM bc_produto bcp
                                           INNER JOIN bc_produto_gtin bcpgtin ON bcpgtin.bc_produto_fk_id = bcp.id
                                           LEFT JOIN bc_produto_ncm bcpncm ON bcpncm.bc_produto_fk_id = bcp.id
                                        WHERE bcp.id  = $id");

            return response()->json($result);

        }
    }

    public function update(Request $request){

       $ncm        = str_replace(".", "",$request->get('ncm'));
       $ncmProduto = BCProdutoNcm::where('bc_produto_fk_id',$request->get('bc_produto_fk_id'))->get();

       if(empty($ncmProduto)){

            $ncm = BCProdutoNcm::create([
                'inicio'           => '2020-12-12',
                'fim'              => '2020-12-12',
                'ncm_fk_id'        => $ncm,
                'bc_produto_fk_id' => $request->get('bc_produto_fk_id')
            ]);

       }else{

          try{

             $produto = BCProduto::find($request->get('bc_produto_fk_id'));

             $produto->nome      = $request->get('nome');
             $produto->ncm_fk_id = $ncm;
             $produto->save();

             $ncm = BCProdutoNcm::where('bc_produto_fk_id',$request->get('bc_produto_fk_id'))->updateOrCreate([
                 'inicio'           => date('Y-m-d'),
                 'ncm_fk_id'        => $ncm,
                 'bc_produto_fk_id' => $produto->id
             ]);

           }catch(\Exception $e){
              echo $e->getMessage();
              die;
           }


       }

        return response()->json(['success' => true]);

    }

    public function toJson(){

        $result = DB::select("SELECT
                                        bcp.id,
                                        bcp.nome,
                                        bcpgtin.gtin,
                                        bcpncm.ncm_fk_id
                                    FROM bc_produto bcp
                                       INNER JOIN bc_produto_gtin bcpgtin ON bcpgtin.bc_produto_fk_id = bcp.id
                                       LEFT JOIN bc_produto_ncm bcpncm ON bcpncm.bc_produto_fk_id = bcp.id
                                    OFFSET 0 LIMIT 10000");

        dd($result);

    }
}
