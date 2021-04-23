<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\LoteProduto;


class LotesController extends Controller
{
    public function index(){

        $clientes = Cliente::all();

        return view('admin.lotes.index')->with('clientes',$clientes);
    }

    public function edit($lote){
        return view('admin.lotes.produtos');
    }

    public function store(Request $request){

        $arquivo   = $request->file('file')->getRealPath();
        $clienteId = $request->cliente_id;

        $ret = [
            'success'      => false,
            'msg'          => 'Ops! nenhum produto foi importado.',
            'url_redirect' => URL("/lotes")
        ];

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
                    
                    $ret['success']      = true;
                    $ret['msg']          = count($arrProdutos).' importados com sucesso.';
                    $ret['url_redirect'] = URL("/lotes/$lote->id/edit");

                } catch (\Throwable $th) {
                    
                    $ret['success']      = false;
                    $ret['msg']          = "Erro: ".$th->getMessage();
                    $ret['url_redirect'] = URL("/lotes");

                }

                break;

            case 'NFXML':
                
                $xml = simplexml_load_string(file_get_contents($arquivo));

                $produtos = (array) $xml->NFe->infNFe;
                $produtos = !empty($produtos['det']) ? $produtos['det'] : [] ;

                if(is_array($produtos) && !empty($produtos)){

                    $qtdsLotes = Lote::where('cliente_id',$clienteId)->count();

                    $proximoLote = ($qtdsLotes == 0) ? 1 : $qtdsLotes++;

                    try {
                        
                        $lote = Lote::create([
                            'numero_do_lote'           => $proximoLote,
                            'cliente_id'               => $clienteId,
                            'quantidade_de_produtos'   => count($produtos),
                            'tipo_documento'           => $request->tipo_arquivo,
                            'competencia_ou_numeracao' => date('m/Y') // TODO : Pegar por dentro do arquivo a competencia
                        ]);

                        foreach ($produtos as $key => $obj) {

                            LoteProduto::create([
                                'lote_id'                   => $lote->id,
                                'codigo_interno_do_cliente' => $obj->prod->cProd,
                                'descricao_do_produto'      => $obj->prod->xProd,
                                'ncm_importado'             => $obj->prod->NCM
                            ]);

                        }

                        $ret['success']      = true;
                        $ret['msg']          = count($produtos).' importados com sucesso.';
                        $ret['url_redirect'] = URL("/lotes/$lote->id/edit");
                        
                    } catch (\Throwable $th) {

                        $ret['success']      = false;
                        $ret['msg']          = "Erro: ".$th->getMessage();
                        $ret['url_redirect'] = URL("/lotes");

                    }
                }else{

                    $qtdsLotes = Lote::where('cliente_id',$clienteId)->count();

                    $proximoLote = ($qtdsLotes == 0) ? 1 : $qtdsLotes++;


                    try {
                        
                        $lote = Lote::create([
                            'numero_do_lote'           => $proximoLote,
                            'cliente_id'               => $clienteId,
                            'quantidade_de_produtos'   => 1,
                            'tipo_documento'           => $request->tipo_arquivo,
                            'competencia_ou_numeracao' => date('m/Y') // TODO : Pegar por dentro do arquivo a competencia
                        ]);

                        LoteProduto::create([
                            'lote_id'                   => $lote->id,
                            'codigo_interno_do_cliente' => $produtos->prod->cProd,
                            'descricao_do_produto'      => $produtos->prod->xProd,
                            'ncm_importado'             => $produtos->prod->NCM
                        ]);

                        $ret['success']      = true;
                        $ret['msg']          = '1 produto importado com sucesso.';
                        $ret['url_redirect'] = URL("/lotes/$lote->id/edit");
                        
                    } catch (\Throwable $th) {

                        $ret['success']      = false;
                        $ret['msg']          = "Erro: ".$th->getMessage();
                        $ret['url_redirect'] = URL("/lotes");

                    }
                }

                break;

            case 'CSV':

                $qtdsLotes = Lote::where('cliente_id',$clienteId)->count();

                $proximoLote = ($qtdsLotes == 0) ? 1 : $qtdsLotes++;

                try {

                    $csv = array_map('str_getcsv', file($arquivo));
                    array_walk($csv, function(&$a) use ($csv) {
                        $a = array_combine($csv[0], $a);
                    });
                    
                    $lote = Lote::create([
                        'numero_do_lote'           => $proximoLote,
                        'cliente_id'               => $clienteId,
                        'quantidade_de_produtos'   => count($csv),
                        'tipo_documento'           => 'ARQUIVO DO CLIENTE',
                        'competencia_ou_numeracao' => date('m/Y') // TODO : Pegar por dentro do arquivo a competencia
                    ]);
 
                    unset($csv[0]);

                    foreach ($csv as $item) {

                        LoteProduto::create([
                            'lote_id'                   => $lote->id,
                            'codigo_interno_do_cliente' => $item['CODIGO_NO_CLIENTE'],
                            'descricao_do_produto'      => $item['DESCRICAO_DO_PRODUTO'],
                            'ncm_importado'             => $item['NCM_NO_CLIENTE']
                        ]);
                    }

                    $ret['success']      = true;
                    $ret['msg']          = count($csv).' importados com sucesso.';
                    $ret['url_redirect'] = URL("/lotes/$lote->id/edit");
                    
                } catch (\Throwable $th) {

                    $ret['success']      = false;
                    $ret['msg']          = "Erro: ".$th->getMessage();
                    $ret['url_redirect'] = URL("/lotes");

                }

                
                
        }

        return response()->json($ret);

    }
}
