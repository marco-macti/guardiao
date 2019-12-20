<?php

namespace App\Http\Controllers;

use App\BCProduto;
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

    public function update(Request $request,BCProduto $produto){

       $ncmProduto = BCProdutoNcm::where('bc_produto_fk_id',613633)->get();

    }
}
