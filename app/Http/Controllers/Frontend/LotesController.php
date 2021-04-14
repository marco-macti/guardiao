<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\LoteProduto;

class LotesController extends Controller
{
    public function index(){

        $lotes = Lote::paginate(15);

        return view('frontend.lotes.index')->with('lotes',$lotes);
        
    }

    public function edit(Lote $lote){


        $produtos = LoteProduto::where('lote_id',$lote->id)->paginate(15);


        return view('frontend.lotes.produtos')->with('produtos',$produtos);
    }

    public function store(Request $request){

        $arquivo = $request->file('file')->getRealPath();

    
        switch ($request->tipo_arquivo) {

            case 'SINTEGRA':
                foreach(file($arquivo) as $line) {
                    echo "<br/>";
                    print_r($line);
                }
                break;

            case 'SPEED':

                $arrProdutos = [];

                // Efetua Limpeza dos dados , pegando apenas Produtos ; 

                foreach(file($arquivo) as $line) {

                    $lineExp = explode('|',$line);

                    if(isset($lineExp[1]) && $lineExp[1] == '0200'){

                       array_push($arrProdutos,$lineExp);

                    }
                }

                $clienteId = 23;

                $qtdsLotes = Lote::where('cliente_id',$clienteId)->count();

                $proximoLote = ($qtdsLotes == 0) ? 1 : $qtdsLotes++;

                try {
                    
                    $lote = Lote::create([
                        'numero_do_lote'           => $proximoLote,
                        'cliente_id'               => $clienteId,
                        'quantidade_de_produtos'   => count($arrProdutos),
                        'tipo_documento'        => $request->tipo_arquivo,
                        'competencia_ou_numeracao' => date('m/Y') // TODO : Pegar por dentro do arquivo a competencia
                    ]);

                    foreach ($arrProdutos as $key => $produto) {
                        LoteProduto::create([
                            'lote_id'                   => $lote->id,
                            'codigo_interno_do_cliente' => $produto[2],
                            'descricao_do_produto'      => $produto[3],
                            'ncm_importado'             => $produto[8]
                        ]);
                    }

                } catch (\Throwable $th) {
                    dd("Erro:",$th->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'msg'     => count($arrProdutos).' importados com sucesso.'
                ]);

                break;

            case 'NFXML':

                $xml = simplexml_load_string(file_get_contents($arquivo));

                dd($xml);
                break;
        }

    }
    
}
