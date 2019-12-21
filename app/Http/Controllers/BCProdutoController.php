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

       $ncmProduto = BCProdutoNcm::where('bc_produto_fk_id',$request->get('ncm'))->get();

       if(empty($ncmProduto)){

            $ncm = BCProdutoNcm::create([
                'inicio'           => '2020-01-01',
                'fim'              => '2020-01-01',
                'ncm_fk_id'        => $request->get('ncm'),
                'bc_produto_fk_id' => $request->get('bc_produto_fk_id')
            ]);

       }else{

           $produto = BCProduto::find($request->get('bc_produto_fk_id'))->update([
               'nome' => $request->get('nome')
           ]);

           $ncm = BCProdutoNcm::where('bc_produto_fk_id',$request->get('bc_produto_fk_id'))->update([
               'ncm_fk_id' => $request->get('ncm')
           ]);
       }

        return response()->json(['success' => true]);

    }
}
