<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\LoteProduto;

use App\Helpers\FormatValue;
use App\Jobs\CadastraProdutoJob;
use App\Http\Controllers\IA\IaController;
use App\Models\LoteProdutoAuditoria;

class LotesController extends Controller
{
    public function index(){

        $data['clientes']      = Cliente::all();
        $data['lotes']         = Lote::paginate(10);

        $data['lotes_status']  = Lote::STATUSLOTES;
        
        return view('admin.lotes.index', $data);
    }

    public function edit(Request $request, $lote){
        $lote = Lote::find($lote);
        $dados['lote'] = $lote;

        $dados['tipo_busca'] = null;
        $dados['valor'] = null;
        $dados['itens_paginas'] = 30;
        $dados['produtos_auditados'] = LoteProdutoAuditoria::where('lote_id', $lote->id)->count();
        $dados['produtos_total'] = LoteProduto::where('lote_id', $lote->id)->count();
        $dados['acertos_total'] = LoteProduto::where('lote_id',$lote->id)->whereRaw('ncm_importado = ia_ncm')->count();
        $dados['erros_total'] = LoteProduto::where('lote_id',$lote->id)->whereRaw('ncm_importado != ia_ncm')->count();

        if($request->has('tipo_busca'))
        {
            $dados['tipo_busca'] = $request->get('tipo_busca');
            $dados['valor'] = $request->get('valor');
            $dados['itens_paginas'] = $request->get('itens_paginas');

            if($dados['valor'])
            {
                switch ($dados['tipo_busca']) {
                    case 'codigo_cliente':
                        $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('codigo_interno_do_cliente', $dados['valor'])->paginate($dados['itens_paginas']);
                        $dados['msg_filtro'] = "Filtro por codigo do cliente nº ".$dados['valor']."!";
                        break;
                    
                    case 'ncm_ia':
                        $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('ia_ncm', $dados['valor'])->paginate($dados['itens_paginas']);
                        $dados['msg_filtro'] = "Filtro por NCM da IA nº ".$dados['valor']."!";
                        break;
                
                    case 'ncm_cliente':
                        $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('ncm_importado', $dados['valor'])->paginate($dados['itens_paginas']);
                        $dados['msg_filtro'] = "Filtro por NCM da IA nº ".$dados['valor']."!";
                        break;
                    
                    case 'situacao':
                        if($dados['valor']=='acerto')
                        {
                            $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->whereRaw('ia_ncm = ncm_importado')->paginate($dados['itens_paginas']);
                            $dados['msg_filtro'] = "Filtro por situação ".strtoupper($dados['valor'])."!";
                        }else{
                            $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->whereRaw('ia_ncm != ncm_importado')->paginate($dados['itens_paginas']);
                            $dados['msg_filtro'] = "Filtro por situação ".strtoupper($dados['valor'])."!";
                        }
                        
                        break;
                    
                    case 'acuracia':
                        switch ($dados['valor']) {
                            case '1':
                                $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('acuracia', '<=', '80')->orderBy('acuracia', 'desc')->paginate($dados['itens_paginas']);
                                $dados['msg_filtro'] = "Filtro por acuracia menor que 80% !";
                                break;
                            case '2':
                                $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('acuracia', '>=', '80')->where('acuracia', '<=', '90')->orderBy('acuracia', 'desc')->paginate($dados['itens_paginas']);
                                $dados['msg_filtro'] = "Filtro por acuracia entre 80% e 90% !";
                                break;
                            case '3':
                                $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('acuracia', '>=', '90')->orderBy('acuracia', 'desc')->paginate($dados['itens_paginas']);
                                $dados['msg_filtro'] = "Filtro por acuracia maior que 90% !";
                                break;
                            
                            default:
                                $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->orderBy('acuracia', 'desc')->paginate($dados['itens_paginas']);
                                break;
                        }
                        
                        break;
                        
                    default:
                        $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->paginate($dados['itens_paginas']);
                        break;
                }
            }else{
                $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->paginate($dados['itens_paginas']);
            }
        }else{
            $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->paginate($dados['itens_paginas']);
        }

        return view('admin.lotes.produtos',$dados);
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

                $competencia = '';

                foreach(file($arquivo) as $line) {

                    $lineExp = explode('|',$line);

                    if(isset($lineExp[1]) && $lineExp[1] == '0000')
                    {
                        $data_inicio = FormatValue::stringToDateBr($lineExp[4]);
                        $data_fim = FormatValue::stringToDateBr($lineExp[5]);
                        $competencia .= $data_inicio.' - '.$data_fim;
                    }

                    if(isset($lineExp[1]) && $lineExp[1] == '0200'){

                       array_push($arrProdutos,$lineExp);

                    }
                }

                $qtdsLotes = Lote::where('cliente_id',$clienteId)->count();

                $proximoLote = ($qtdsLotes == 0) ? 1 : $qtdsLotes++;

                try {

                    $lote = Lote::create([
                        'numero_do_lote'            => $proximoLote,
                        'cliente_id'                => $clienteId,
                        'quantidade_de_produtos'    => count($arrProdutos),
                        'tipo_documento'            => $request->tipo_arquivo,
                        'competencia_ou_numeracao'  => $competencia, // TODO : Pegar por dentro do arquivo a competencia
                        'status_importacao'         => 0 
                    ]);
                    foreach(array_chunk($arrProdutos, 15000) as $produtos)
                    {

                        $job = (new CadastraProdutoJob($lote->id,$produtos,$request->tipo_arquivo))->onQueue('speed');
                        dispatch($job);
                    }

                    // $job = (new CadastraProdutoJob($lote->id,$arrProdutos,$request->tipo_arquivo))->onQueue('speed');
                    // dispatch($job);

                    $ret['success']      = true;
                    $ret['msg']          = count($arrProdutos).' enviados para fila de importação.';
                    $ret['url_redirect'] = URL("/lotes/$lote->id/edit");

                } catch (\Throwable $th) {

                    $ret['success']      = false;
                    $ret['msg']          = "Erro: ".$th->getMessage();
                    $ret['url_redirect'] = URL("/lotes");

                }

                break;

            case 'NFXML':

                $ia_instance = new IaController();

                $xml = simplexml_load_file($arquivo);
                $xml = json_encode($xml);
                $xml = json_decode($xml, true);

                $competencia = substr($xml['NFe']['infNFe']['ide']['dhEmi'], 0, 10);
                $produtos = !empty($xml['NFe']['infNFe']['det']) ? $xml['NFe']['infNFe']['det'] : [] ;

                if(is_array($produtos) && !empty($produtos)){

                    $qtdsLotes = Lote::where('cliente_id',$clienteId)->count();

                    $proximoLote = ($qtdsLotes == 0) ? 1 : $qtdsLotes++;

                    try {

                        $lote = Lote::create([
                            'numero_do_lote'           => $proximoLote,
                            'cliente_id'               => $clienteId,
                            'quantidade_de_produtos'   => count($produtos),
                            'tipo_documento'           => $request->tipo_arquivo,
                            'competencia_ou_numeracao' => $competencia, // TODO : Pegar por dentro do arquivo a competencia
                            'status_importacao'         => 0 
                        ]);

                        foreach(array_chunk($produtos, 15000) as $produto)
                        {
    
                            $job = (new CadastraProdutoJob($lote->id,$produto,$request->tipo_arquivo))->onQueue('nfxml');
                            dispatch($job);
                        }

                        // $job = (new CadastraProdutoJob($lote->id,$produtos,$request->tipo_arquivo))->onQueue('nfxml');
                        // dispatch($job);

                        $ret['success']      = true;
                        $ret['msg']          = count($produtos).' enviados para fila de importação.';
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
                            'competencia_ou_numeracao' => date('m/Y'), // TODO : Pegar por dentro do arquivo a competencia
                            'status_importacao'         => 0 
                        ]);

                        LoteProduto::create([
                            'lote_id'                   => $lote->id,
                            'codigo_interno_do_cliente' => $produtos['prod']['cProd'],
                            'descricao_do_produto'      => $produtos['prod']['xProd'],
                            'ncm_importado'             => $produtos['prod']['NCM']
                        ]);

                        $ret['success']      = true;
                        $ret['msg']          = '1 produto importado com sucesso.';
                        $ret['url_redirect'] = URL("/lotes/$lote->id/edit");

                        $lote->status_importacao = 1;
                        $lote->save();

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
                    $padrao_cabecalho_csv = [
                        'CODIGO_NO_CLIENTE',
                        'DESCRICAO_DO_PRODUTO',
                        'NCM_NO_CLIENTE'
                    ];

                    foreach ($csv[0] as $key => $cabecalho) {
                        if($padrao_cabecalho_csv[$key] != $cabecalho)
                        {

                            $ret['success']      = false;
                            $ret['msg']          = 'Falha na validação do cabeçalho. Verifique o arquivo e tente novamente!';
                            $ret['url_redirect'] = URL("/lotes/$lote->id/edit");

                            return response()->json($ret);
                        }
                    }

                    array_walk($csv, function(&$a) use ($csv) {
                        $a = array_combine($csv[0], $a);
                    });

                    unset($csv[0]);

                    $lote = Lote::create([
                        'numero_do_lote'           => $proximoLote,
                        'cliente_id'               => $clienteId,
                        'quantidade_de_produtos'   => count($csv),
                        'tipo_documento'           => 'ARQUIVO DO CLIENTE',
                        'competencia_ou_numeracao' => date('m/Y'), // TODO : Pegar por dentro do arquivo a competencia
                        'status_importacao'         => 0 
                    ]);

                    foreach(array_chunk($csv, 15000) as $key => $produtos)
                    {
                            
                        $job = (new CadastraProdutoJob($lote->id,$produtos,$request->tipo_arquivo))->onQueue('csv');
                        dispatch($job);
                    }

                    $ret['success']      = true;
                    $ret['msg']          = count($csv).' enviados para fila de importação.';
                    $ret['url_redirect'] = URL("/lotes/$lote->id/edit");

                } catch (\Throwable $th) {

                    $ret['success']      = false;
                    $ret['msg']          = "Erro: ".$th->getMessage();
                    $ret['url_redirect'] = URL("/lotes");

                }
        }

        return response()->json($ret);

    }

    function validaCsv($csv)
    {
        dd($csv);
    }

    public function destroy(Lote $lote)
    {
        try {
            LoteProdutoAuditoria::where('lote_id',$lote->id)->delete();
            LoteProduto::where('lote_id',$lote->id)->delete();
            $lote->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false,'msg' => $th->getMessage()]);
        }

    }
}
