<?php

namespace App\Http\Controllers\Frontend;
ini_set('max_execution_time', '500'); 
ini_set('memory_limit', '512M');

use App\Helpers\Cosmos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\LoteProduto;
use Illuminate\Support\Facades\Artisan;

use App\Helpers\FormatValue;
use App\Http\Controllers\IA\IaController;
use App\Models\LoteProdutoAuditoria;
use Illuminate\Support\Facades\DB;

use App\Jobs\CadastraProdutoJob;
use App\Jobs\AuditarLoteJob;

class LotesController extends Controller
{

    public function index(){
        $clienteId    = auth()->user()->cliente_id;
        $lotesCliente = Lote::select('id')->where('cliente_id',$clienteId)->get()->toArray();

        $totalProdutosImportados   = LoteProduto::whereIn('lote_id',$lotesCliente)->count();
        $totalDeProdutosAuditados  = LoteProdutoAuditoria::whereIn('lote_id',$lotesCliente)->count();
        $totalDeProdutosCorretos   = DB::select("SELECT COUNT(*) as ACERTOS FROM lote_produtos WHERE ncm_importado = ia_ncm AND lote_id IN(SELECT id FROM lotes WHERE cliente_id = $clienteId)");
        $totalDeProdutosIncorretos = DB::select("SELECT COUNT(*) as ERROS FROM lote_produtos WHERE ncm_importado <> ia_ncm AND lote_id IN(SELECT id FROM lotes WHERE cliente_id = $clienteId)");

        $lotes = Lote::where('cliente_id',auth()->user()->cliente_id)->paginate(30);

        return view('frontend.lotes.index')
                ->with('totalProdutosImportados',$totalProdutosImportados)
                ->with('totalDeProdutosAuditados',$totalDeProdutosAuditados)
                ->with('totalDeProdutosCorretos',$totalDeProdutosCorretos[0]->ACERTOS)
                ->with('totalDeProdutosIncorretos',$totalDeProdutosIncorretos[0]->ERROS)
                ->with('lotes',$lotes);

    }

    public function edit(Request $request, Lote $lote){

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

            switch ($dados['tipo_busca']) {
                case 'codigo_cliente':
                    $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('codigo_interno_do_cliente', $dados['valor'])->paginate($dados['itens_paginas']);
                    $dados['msg_filtro'] = "Filtro por codigo do cliente nº ".$dados['valor']."!";
                    break;

                case 'ncm_cliente':
                    $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('ncm_importado', $dados['valor'])->paginate($dados['itens_paginas']);
                    $dados['msg_filtro'] = "Filtro por NCM da IA nº ".$dados['valor']."!";
                    break;

                case 'ncm_ia':
                    $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->where('ia_ncm', $dados['valor'])->paginate($dados['itens_paginas']);
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
                            $dados['produtos'] = LoteProduto::where('lote_id',$lote->id)->paginate($dados['itens_paginas']);
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

        return view('frontend.lotes.produtos', $dados);
                // ->with('lote',$lote)
                // ->with('produtos',$produtos);
    }

    public function store(Request $request){

        $arquivo   = $request->file('file')->getRealPath();
        $clienteId = auth()->user()->cliente_id;

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
                        $data_inicio  = FormatValue::stringToDateBr($lineExp[4]);
                        $data_fim     = FormatValue::stringToDateBr($lineExp[5]);
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
                        'numero_do_lote'           => $proximoLote,
                        'cliente_id'               => $clienteId,
                        'quantidade_de_produtos'   => count($arrProdutos),
                        'tipo_documento'           => $request->tipo_arquivo,
                        'competencia_ou_numeracao' => $competencia,
                        'status_importacao'         => 0
                    ]);

                    $queue = "QUEUE_".$lote->id;

                    foreach(array_chunk($arrProdutos, 15000) as $produtos)
                    {

                        $job = (new CadastraProdutoJob($lote->id,$produtos,$request->tipo_arquivo))->onQueue('speed');
                        dispatch($job);
                    }

                    // $job = (new CadastraProdutoJob($lote->id,$arrProdutos,$request->tipo_arquivo))->onQueue("speed");

                    $ret['success']      = true;
                    $ret['msg']          = count($arrProdutos).' enviados para fila de importação.';
                    $ret['url_redirect'] = URL("/lotes");

                } catch (\Throwable $th) {

                    $ret['success']      = false;
                    $ret['msg']          = "Erro: ".$th->getMessage();
                    $ret['url_redirect'] = URL("/lotes");

                }

                break;

            case 'NFXML':

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
                            'numero_do_lote'             => $proximoLote,
                            'cliente_id'                 => $clienteId,
                            'quantidade_de_produtos'     => count($produtos),
                            'tipo_documento'             => $request->tipo_arquivo,
                            'numero_do_documento_fiscal' => $xml['NFe']['infNFe']['ide']['nNF'],
                            'valor_frete'                => $xml['NFe']['infNFe']['total']['ICMSTot']['vFrete'],
                            'competencia_ou_numeracao'   => $competencia,
                            'status_importacao'          => 0
                        ]);

                        $queue = "QUEUE_".$lote->id;

                        foreach(array_chunk($produtos, 15000) as $produto)
                        {

                            $job = (new CadastraProdutoJob($lote->id,$produto,$request->tipo_arquivo))->onQueue('nfxml');
                            dispatch($job);
                        }

                        $ret['success']      = true;
                        $ret['msg']          = count($produtos).' enviados para fila de importação.';
                        $ret['url_redirect'] = URL("/lotes");

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
                        $ret['url_redirect'] = URL("/lotes");

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

                    $queue = "QUEUE_".$lote->id;

                    foreach(array_chunk($csv, 15000) as $produtos)
                    {
                        $job = (new CadastraProdutoJob($lote->id,$produtos,$request->tipo_arquivo))->onQueue('csv');
                        dispatch($job);
                    }

                    $ret['success']      = true;
                    $ret['msg']          = count($csv).' enviados para fila de importação.';
                    $ret['url_redirect'] = URL("/lotes");

                } catch (\Throwable $th) {

                    $ret['success']      = false;
                    $ret['msg']          = "Erro: ".$th->getMessage();
                    $ret['url_redirect'] = URL("/lotes");

                }
        }

        return response()->json($ret);

    }

    public function export(Lote $lote)
    {
        $produtos = LoteProduto::get();

        $headers = [
            'Código Interno',
            'EAN',
            'NCM Cliente',
            'NCM Auditado',
            'Descrição',
            'CFOP',
            'Quantidade',
            'Valor',
            'Valor Desconto',
            'Total',
            'Tipo Tributação',
            'Legislação'
        ];

        $fp = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="export.csv"');
        fputcsv($fp, $headers);
        
        foreach ($produtos as $key => $produto) {
            $legislacao = '';
            switch ($produto->tipo_tributacao) {
                case 'MONOFÁSICO':
                    $pathFileMonofasico = public_path("monofasico.csv");
                    
                    $dados = fopen($pathFileMonofasico, "r");
            
                    while(($data = fgetcsv($dados, 1000, ",")) !== FALSE){
                        if($data[0] == $produto->ncm_importado)
                            $legislacao = $data[2];
                    }
                break;
            }

            fputcsv($fp, array_values([
                'Código Interno' => $produto->codigo_interno_do_cliente,
                'EAN' => $produto->ean_gtin,
                'NCM Cliente' => $produto->ncm_importado,
                'NCM Auditado' => $produto->ia_ncm,
                'Descrição' => $produto->descricao_do_produto,
                'CFOP' => $produto->cfop,
                'Quantidade' => $produto->quantidade,
                'Valor' => $produto->valor,
                'Valor Desconto' => $produto->valor_desconto,
                'Total' => $produto->valor * $produto->quantidade,
                'Tipo Tributação' => $produto->tipo_tributacao,
                'Legislação' => $legislacao
            ]));
        }
    }

    public function buscaRelacionadosCosmosByDescricao(Request $request){

        $ret = [
            'success'=> false,
            'data'   => ''
        ];

        $ret['data'] = Cosmos::getByDescricao($request->descricao);

        return response()->json($ret);

    }

    public function assumirNcm(Request $request){


        if(empty($request->ncm_importado) ||
           empty($request->ncm_auditado)  ||
           empty($request->lote_id)       ||
           empty($request->lote_produto_id)) return false;

        try {
            $loteProdutoAuditoria = LoteProdutoAuditoria::where('lote_id', $request->lote_id)
                                                        ->where('lote_produto_id', $request->lote_produto_id)
                                                        ->first();

            if(!is_object($loteProdutoAuditoria)) $loteProdutoAuditoria = new LoteProdutoAuditoria();

            $loteProdutoAuditoria->lote_id         = $request->lote_id;
            $loteProdutoAuditoria->lote_produto_id = $request->lote_produto_id;
            $loteProdutoAuditoria->ncm_importado   = $request->ncm_importado;
            $loteProdutoAuditoria->ncm_auditado    = $request->ncm_auditado;
            $loteProdutoAuditoria->pre_auditado    = 'N';
            $loteProdutoAuditoria->save();

            $produto = LoteProduto::find($request->lote_produto_id);

            if($this->monofasico($request->ncm_auditado)){
                $produto->tipo_tributacao = 'MONOFÁSICO';
            }else{
                if($this->st($request->ncm_auditado)){
                    $produto->tipo_tributacao = 'SUBSTITUIÇÃO TRIBUITÁRIA';
                }else{
                    $produto->tipo_tributacao = 'TRIBUTAÇÃO';
                }
            }

            $produto->update();

            return json_encode(['success' => true]);

        } catch (\Throwable $th) {
            return json_encode(['success' => false]);
        }

        return json_encode(['success' => true]);
    }

    public function auditarLote($lote_id)
    {
        $lote = Lote::find($lote_id);

        if(!$lote)
            return back()->withErrors("Lote não localizado!");

        $job = (new AuditarLoteJob($lote->id))->onQueue('high');

        dispatch($job);

        return back()->withSuccess("Produtos em fila de processamento!");
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


    public function monofasico($ncm)
    {
        $pathFileMonofasico = public_path("monofasico.csv");
        
        $dados = fopen($pathFileMonofasico, "r");

        while(($data = fgetcsv($dados, 1000, ",")) !== FALSE){
            if($data[0] == $ncm)
                return true;
        }
        
        return false;
    }

    public function st($ncmConsulta)
    {
        $pathFileMonofasico = public_path("st.csv");
        
        $dados = fopen($pathFileMonofasico, "r");

        while(($data = fgetcsv($dados, 1000, ",")) !== FALSE){
            $ncms = explode(' ', $data[0]);

            foreach ($ncms as $ncm) {
                if($ncmConsulta == preg_replace( '/[^0-9]/', '', $ncm))
                    return true;
            }
        }
        
        return false;
    }
}
