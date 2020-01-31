<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaClienteController extends Controller
{
    public function meusProdutos(Cliente $cliente){

        $produtos = DB::select("SELECT
                                    lp.*
                                    FROM
                                    public.cliente_lote cl
                                        INNER JOIN lote_produto lp ON lp.lote_fk_id = cl.id
                                        INNER JOIN bc_perfil_contabil bcpc ON bcpc.ncm_fk_id = lp.ncm
                                    WHERE cl.cliente_fk_id = $cliente->id
                                      AND cl.cliente_lote_status_fk_id = 4
                                      AND lp.status_fk_id = 5
                                      AND bcpc.trib_estab_destino_fk_id = $cliente->tributacao_estabelecimento_destino_fk_id
                                      AND bcpc.trib_estab_origem_fk_id = $cliente->enquadramento_tributario_fk_id");

        return response()->json($produtos);

    }
}
