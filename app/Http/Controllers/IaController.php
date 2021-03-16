<?php

namespace App\Http\Controllers;

use App\Imports\EAuditor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;

class IaController extends Controller
{
    public $classifier;
    public $arrJsonAtual;
    public function __construct()
    {
        $smsJSON            = file_get_contents('trainingbkp.json');
        $this->arrJsonAtual = json_decode($smsJSON);

        $this->classifier = new TNTClassifier();

        for ($i = 0; $i < count($this->arrJsonAtual) -1; $i++) {
            $this->classifier->learn($this->arrJsonAtual[$i]->nome, $this->arrJsonAtual[$i]->cod_ncm);
        }
    }

    public function index(){
        return view('ia.index');
    }

    public function getProbability($probability){
        $bestMatch = [];

        foreach ($probability as $key => $classified) {
        if (count($bestMatch) == 0 && $classified <= 1) {
            $bestMatch['nome'] = $key;
            $bestMatch['acuracia'] = $classified;
        } elseif ($classified <= 1 && ((1 - $classified) < (1 - $bestMatch['acuracia']))) {
            $bestMatch['nome'] = $key;
            $bestMatch['acuracia'] = $classified;
        }
        }
        $bestMatch['acuracia'] = (float) number_format($bestMatch['acuracia'] * 100, 2);
        return $bestMatch['acuracia'];
    }

    public function preditar(){
        $predict = $this->classifier->predict('ABRACADEIRA DE FERRO DIANT.MC1950');
        dd($predict);
    }

    public function trainamentoBase(Request $request){
        $produtosBc = json_decode(file_get_contents('export-json.json'));
        $treinamento = 0.1;
        $contAcertos = 0;
        $totalItens  = 0;
        $json = [];
        $contErros = 0;
        $contOitenta = 0;
        echo'<table border="1">
                <tr>
                    <td align="center">Nome produto</td>
                    <td align="center">NCM Atual</td>
                    <td align="center">NCM Classificado</td>
                    <td align="center">Acurácia</td>
                    <td align="center">ACERTOU?</td>
                </tr>';
        
        for ($i=1000; $i < 3000; $i++) { 
            $ncmBc        = $produtosBc[$i]->cod_ncm;
            $produtoBc    = $produtosBc[$i]->nome;
            $predict      = $this->classifier->predict($produtoBc);
            $probailidade = $this->getProbability($predict['probability']);

            echo'<tr>
                        <td align="center">'.$produtoBc.'</td>
                        <td align="center">'.$ncmBc.'</td>
                        <td align="center">'.$predict['label'].'</td>
            ';

            if($probailidade > 70 && $probailidade < 90){
                echo '<td align="center" style="background-color:yellow">'.$probailidade.'%</td>';
            }elseif($probailidade > 90){
                echo '<td align="center" style="background-color:green">'.$probailidade.'%</td>';
            }else{
                echo '<td align="center" style="background-color:red">'.$probailidade.'%</td>';
            }

            if($probailidade >= 80){
                $contOitenta++;
            }

            if($ncmBc != $predict['label']){
                echo '<td align="center" style="background-color:red">NÃO</td>';
                /*$json[$contErros]['cod_ncm']   = $ncmBc;
                $json[$contErros]['descricao'] = $produto;
                $json[$contErros]['nome']      = $produto;*/
                $contErros++;
            }else{
                echo '<td align="center" style="background-color:green">SIM</td>';
                $contAcertos++;
            }

            $totalItens++;
        }

        echo'</table>';

        $calcAcuraciaGlobal = $contAcertos / $totalItens * 100;
        echo'<h1>Acertou '.$contAcertos.' de '.$totalItens.' - média de acurácia '.number_format($calcAcuraciaGlobal,2).'</h1>';
        echo '<h1>Itens acima de 80% '.$contOitenta.'</h1>';

                
    }


    public function treinar(Request $request){
        $import = new EAuditor();
        Excel::import($import, request()->file('file'));
        
        echo'<html>
            <body>
            <table border="1">
             <tr>
                <td align="center">Nome produto</td>
                <td align="center">NCM Cliente</td>
                <td align="center">NCM Inteligência Artificial</td>
                <td align="center">Acurácia</td>
                <td align="center">ACERTOU?</td>
             </tr>';
        $contAcertos = 0;
        $totalItens  = 0;
        $json = [];
        $contErros = 0;
        $linha = 0;
        foreach ($import->sheetData[0] as $key => $value) {
            if(!empty($value[0]) && $key > 0 && $key < 7413){
                $produto = $value[0];
                $predict = $this->classifier->predict($produto);
                $probailidade = $this->getProbability($predict['probability']);
                echo'<tr>
                        <td align="center">'.$produto.'</td>
                        <td align="center">'.$value[1].'</td>
                        <td align="center">'.$predict['label'].'</td>
            ';
                if($probailidade > 70 && $probailidade < 90){
                    echo '<td align="center" style="background-color:yellow">'.$probailidade.'%</td>';
                }elseif($probailidade > 90){
                    echo '<td align="center" style="background-color:green">'.$probailidade.'%</td>';
                }else{
                    echo '<td align="center" style="background-color:red">'.$probailidade.'%</td>';
                }

                if($value[1] != $predict['label']){
                    echo '<td align="center" style="background-color:red">AUDITAR</td>';
                    $json[$contErros]['cod_ncm']   = $value[1];
                    $json[$contErros]['descricao'] = $produto;
                    $json[$contErros]['nome']      = $produto;
                    $contErros++;
                }else{
                    echo '<td align="center" style="background-color:green">NCM CORRETO</td>';
                    $contAcertos++;
                }


                $linha++;
            echo'</tr>';
               /* $json[$totalItens]['cod_ncm']   = $value[2];
                $json[$totalItens]['descricao'] = $produto;
                $json[$totalItens]['nome']      = $produto;*/
                $totalItens++;
            }
        }
        $calcAcuraciaGlobal = $contAcertos / $totalItens * 100;

        if($calcAcuraciaGlobal <= 100){
            $json  = json_encode($json);
            $json  = substr($json, 1);
            $json  = substr($json, 0, -1);
            $json  = $json.',';
            $atual = json_encode($this->arrJsonAtual);
            $novo  = substr_replace($atual, $json, 1, 0);
            file_put_contents('trainingbkp.json', $novo, JSON_UNESCAPED_UNICODE); 
        }
        echo'</table>';
        //echo json_encode($json);
        echo'<h1>Acertou '.$contAcertos.' de '.$totalItens.' - média de acurácia '.number_format($calcAcuraciaGlobal,2).'</h1>

        
        
        </body></html>';

        //$smsJSON            = file_get_contents('training.json');
        //$arrJsonAtualNew    = json_decode($smsJSON);
        /*foreach ($json as $key => $value) {
            array_unshift($this->arrJsonAtual, $value);    
        }*/
        
        //file_put_contents('training.json', json_encode($this->arrJsonAtual, JSON_UNESCAPED_UNICODE));        
        
                

        /*if($calcAcuraciaGlobal < 99){
            //$this->importEAuditor($request);
        }else{
            echo'<h1>Acertou '.$contAcertos.' de '.$totalItens.' - média de acurácia '.number_format($calcAcuraciaGlobal,2).'</h1>';
        }*/

        
    }

    public function importEAuditor(Request $request){
        $import = new EAuditor();
        Excel::import($import, request()->file('file'));

        $convenio_142  = new EAuditor();
        Excel::import($convenio_142, 'storage/icms_convenio_142_2018.xls');

        
        $produtos_aliquota_42  = new EAuditor();
        Excel::import($produtos_aliquota_42, 'storage/produtos_com_aliquota_42.xlsx');

        $tipi  = new EAuditor();
        Excel::import($tipi, 'storage/tipi.xls');
        
        
        echo'<html>
            <body>
            <table border="1">
             <tr>
                <td align="center">Nome produto</td>
                <td align="center">NCM Cliente</td>
                <td align="center">NCM Inteligência Artificial</td>
                <td align="center">Acurácia</td>
                <td align="center">ACERTOU?</td>
                <td align="center">Treinar</td>
                <td align="center">Desc. NCM Cliente</td>
                <td align="center">Desc. NCM IA</td>
                <td align="center">Item</td>
                <td align="center">Cest</td>
                <td align="center">Descrição</td>
                <td align="center">Tributação</td>
                <td align="center">Monofásico</td>
             </tr>';
        $contAcertos = 0;
        $totalItens  = 0;
        $json = [];
        $contErros = 0;
        $linha = 0;
        foreach ($import->sheetData[0] as $key => $value) {

            if(!empty($value[0]) && $key > 0 && $key < 100){
                $produto = $value[0];
                $predict = $this->classifier->predict($produto);
                $probailidade = $this->getProbability($predict['probability']);
                echo'<tr>
                        <td align="center">'.$produto.'</td>
                        <td align="center">'.$value[1].'</td>
                        <td align="center">'.$predict['label'].'</td>
            ';
                if($probailidade > 70 && $probailidade < 90){
                    echo '<td align="center" style="background-color:yellow">'.$probailidade.'%</td>';
                }elseif($probailidade > 90){
                    echo '<td align="center" style="background-color:green">'.$probailidade.'%</td>';
                }else{
                    echo '<td align="center" style="background-color:red">'.$probailidade.'%</td>';
                }

                if($value[1] != $predict['label']){
                    echo '<td align="center" style="background-color:red">AUDITAR</td>';
                    $json[$contErros]['cod_ncm']   = $value[1];
                    $json[$contErros]['descricao'] = $produto;
                    $json[$contErros]['nome']      = $produto;
                    $contErros++;
                }else{
                    echo '<td align="center" style="background-color:green">NCM CORRETO</td>';
                    $contAcertos++;
                }

                echo '<td align="center">
                        <input type="number" id="ncm-correto-'.$linha.'" />
                        <input type="hidden" id="nome-'.$linha.'" value="'.$produto.'" placeholder="NCM Correto" />
                        <button name="btn" class="btn-salva-ncm" data-id="'.$linha.'">Ok</button>
                      </td>';


                //busca desc cliente
                $desc_ncm_cliente_capitulo = $this->buscaDescNcmClienteCapitulo($tipi, $value[1]);
                $desc_ncm_cliente_posicao = $this->buscaDescNcmClientePosicao($tipi, $value[1]);
                $desc_ncm_cliente_subposicao = $this->buscaDescNcmClienteSubPosicao($tipi, $value[1]);
                $desc_ncm_cliente_subitem = $this->buscaDescNcmClienteSubItem($tipi, $value[1]);

                if(!empty($desc_ncm_cliente_capitulo)){
                    echo '<td align="center">
                            <strong>Capítulo:</strong> '.$desc_ncm_cliente_capitulo['ex_capitulo'].'<br />
                            
                            <strong>Posição:</strong> '.$desc_ncm_cliente_posicao['ex_posicao'].'<br />
                           
                            <strong>Subposição:</strong> '.$desc_ncm_cliente_subposicao['ex_subposicao'].'<br />
                            
                            <strong>Subitem:</strong> '.$desc_ncm_cliente_subitem['ex_sub_item'].'<br />
                            
                          </td>';
                }else{
                    echo '<td> - </td>';
                }

                //busca desc preditado
                $desc_ncm_cliente_capitulo = $this->buscaDescNcmClienteCapitulo($tipi, $predict['label']);
                $desc_ncm_cliente_posicao = $this->buscaDescNcmClientePosicao($tipi, $predict['label']);
                $desc_ncm_cliente_subposicao = $this->buscaDescNcmClienteSubPosicao($tipi, $predict['label']);
                $desc_ncm_cliente_subitem = $this->buscaDescNcmClienteSubItem($tipi, $predict['label']);

                if(!empty($desc_ncm_cliente_capitulo)){
                    echo '<td align="center">
                            <strong>Capítulo:</strong> '.$desc_ncm_cliente_capitulo['ex_capitulo'].'<br />
                            
                            <strong>Posição:</strong> '.$desc_ncm_cliente_posicao['ex_posicao'].'<br />
                            
                            <strong>Subposição:</strong> '.$desc_ncm_cliente_subposicao['ex_subposicao'].'<br />
                           
                            <strong>Subitem:</strong> '.$desc_ncm_cliente_subitem['ex_sub_item'].'<br />
                            
                          </td>';
                }else{
                    echo '<td> - </td>';
                }

                //busca convenio 142
                $ret_convenio_142 = $this->buscaCovenio142($convenio_142, $predict['label']);

                if(!empty($ret_convenio_142['item'])){
                    echo'<td align="center">
                        '.$ret_convenio_142['item'].'
                        </td>
                        <td align="center">
                        '.$ret_convenio_142['cest'].'
                        </td>
                        ';
                }else{
                    echo'<td align="center">
                         -
                        </td>
                        <td align="center">
                        -
                        </td>
                        ';
                }

                $ret_aliquota_42 = $this->buscaAliquota42($produtos_aliquota_42, $predict['label']);

                if(!empty($ret_convenio_142['cest'])){
                    echo '<td align="center">
                        '.$ret_convenio_142['descricao'].'
                        </td>';
                }else if(!empty($ret_aliquota_42['descricao'])){
                    echo '<td align="center">
                            '.$ret_aliquota_42['descricao'].'
                        </td>';
                }else{
                    echo '<td align="center">
                            -
                        </td>';
                }

                if(!empty($ret_aliquota_42['aliquota'])){
                    $aliquota = $ret_aliquota_42['aliquota'] * 100;
                    if(!empty($ret_convenio_142['cest'])){
                        echo'<td align="center">
                           ST
                        </td>';
                    }else{
                        echo'<td align="center">
                            '.$aliquota.' %
                        </td>';
                    }
                    
                }else{
                    if(!empty($ret_convenio_142['cest'])){
                        echo'<td align="center">
                           ST
                        </td>';
                    }else{
                        echo'<td align="center">
                            TRIBUTADO 
                        </td>';
                    }
                }

                $ret_monofasico = $this->buscaMonofasico($predict['label']);

                if($ret_monofasico){
                    echo'<td align="center">
                             SIM
                        </td>';
                }else{
                    echo'<td align="center">
                             NÃO
                        </td>';
                }

                $linha++;
            echo'</tr>';
               /* $json[$totalItens]['cod_ncm']   = $value[2];
                $json[$totalItens]['descricao'] = $produto;
                $json[$totalItens]['nome']      = $produto;*/
                $totalItens++;
            }
        }
        $calcAcuraciaGlobal = $contAcertos / $totalItens * 100;

        /*if($calcAcuraciaGlobal <= 100){
            $json  = json_encode($json);
            $json  = substr($json, 1);
            $json  = substr($json, 0, -1);
            $json  = $json.',';
            $atual = json_encode($this->arrJsonAtual);
            $novo  = substr_replace($atual, $json, 1, 0);
            file_put_contents('trainingbkp.json', $novo, JSON_UNESCAPED_UNICODE); 
        }*/
        echo'</table>';
        //echo json_encode($json);
        echo'<h1>Acertou '.$contAcertos.' de '.$totalItens.' - média de acurácia '.number_format($calcAcuraciaGlobal,2).'</h1>

        
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            $(document).ready(function(){
                $(".btn-salva-ncm").click(function(){
                    var id          = $(this).attr("data-id");
                    var produto     = $("#nome-"+id).val();
                    var ncm_correto = $("#ncm-correto-"+id).val();

                    Swal.fire({
                        title: "Deseja realmente salvar?",
                        showCancelButton: true,
                        confirmButtonText: "Salvar",
                        denyButtonText: "Cancelar",
                      }).then((result) => {
                        
                        if (result.isConfirmed) {
                            if(produto != "" && ncm_correto != ""){
                                $.ajax({
                                    type: "get",
                                    url: "/ia/registra-ia",
                                    data: {
                                        "produto":produto,
                                        "ncm_correto":ncm_correto
                                    },
                                    dataType: "json",
                                    success: function (response) {
                                        
                                    }
                                });
                            }
                        } 
                      })
                })
            })
        </script>
        </body></html>';

        //$smsJSON            = file_get_contents('training.json');
        //$arrJsonAtualNew    = json_decode($smsJSON);
        /*foreach ($json as $key => $value) {
            array_unshift($this->arrJsonAtual, $value);    
        }*/
        
        //file_put_contents('training.json', json_encode($this->arrJsonAtual, JSON_UNESCAPED_UNICODE));        
        
                

        /*if($calcAcuraciaGlobal < 99){
            //$this->importEAuditor($request);
        }else{
            echo'<h1>Acertou '.$contAcertos.' de '.$totalItens.' - média de acurácia '.number_format($calcAcuraciaGlobal,2).'</h1>';
        }*/

        
    }

    public function buscaDescNcmCliente($tipi, $ncm){
        $ret = [];
        $ret['descricao_capitulo']         = '';
        $ret['ex_capitulo']                = '';
        $ret['aliquota_capitulo']          = '';
        $ret['descricao_posicao']          = '';
        $ret['ex_posicao']                 = '';
        $ret['aliquota_posicao']           = '';
        $ret['descricao_subposicao']       = '';
        $ret['ex_subposicao']              = '';
        $ret['aliquota_subposicao']        = '';
        $ret['descricao_sub_item']         = '';
        $ret['ex_sub_item']                = '';
        $ret['aliquota_sub_item']          = '';
        foreach ($tipi->sheetData[0] as $key => $value) {
            if($key > 0 && !empty($value[0])){
                $capitulo   = substr($value[0], 0, 2);
                $posicao    = substr($value[0], 0, 4);
                $subposicao = substr($value[0], 0, 6);
                $sub_item   = $value[0];
                $ncm        = (strlen($ncm) <= 7) ? '0'.$ncm : $ncm;

                if($capitulo == substr($ncm, 0, 2)){
                    $ret['descricao_capitulo']          = $value[2];
                    $ret['ex_capitulo']                 = $value[1];
                    $ret['aliquota_capitulo']           = $value[3];
                }
                if($posicao == substr($ncm, 0, 4)){
                    $ret['descricao_posicao']          = $value[2];
                    $ret['ex_posicao']                 = $value[1];
                    $ret['aliquota_posicao']           = $value[3];
                }
                if($subposicao == substr($ncm, 0, 6)){
                    $ret['descricao_subposicao']          = $value[2];
                    $ret['ex_subposicao']                 = $value[1];
                    $ret['aliquota_subposicao']           = $value[3];
                }
                if($sub_item == $ncm){
                    $ret['descricao_sub_item']          = $value[2];
                    $ret['ex_sub_item']                 = $value[1];
                    $ret['aliquota_sub_item']           = $value[3];
                }
            }
        }

        return $ret;
    }

    public function buscaDescNcmClienteSubItem($tipi, $ncm){
        $ret = [];
        $ret['descricao_capitulo']         = '';
        $ret['ex_capitulo']                = '';
        $ret['aliquota_capitulo']          = '';
        $ret['descricao_posicao']          = '';
        $ret['ex_posicao']                 = '';
        $ret['aliquota_posicao']           = '';
        $ret['descricao_subposicao']       = '';
        $ret['ex_subposicao']              = '';
        $ret['aliquota_subposicao']        = '';
        $ret['descricao_sub_item']         = '';
        $ret['ex_sub_item']                = '';
        $ret['aliquota_sub_item']          = '';
        $rodou = false;
        foreach ($tipi->sheetData[0] as $key => $value) {
            if($key > 0 && !empty($value[0])){
                $capitulo   = substr($value[0], 0, 2);
                $posicao    = substr($value[0], 0, 4);
                $subposicao = substr($value[0], 0, 6);
                $sub_item   = str_replace(".","", $value[0]);
                $ncm        = (strlen($ncm) <= 7) ? '0'.$ncm : $ncm;

                
                if($sub_item == $ncm && $rodou == false){
                    $ret['descricao_sub_item']          = $value[2];
                    $ret['ex_sub_item']                 = $value[1];
                    $ret['aliquota_sub_item']           = $value[3];
                    $rodou = true;
                }
            }
        }

        return $ret;
    }

    public function buscaDescNcmClienteSubPosicao($tipi, $ncm){
        $ret = [];
        $ret['descricao_capitulo']         = '';
        $ret['ex_capitulo']                = '';
        $ret['aliquota_capitulo']          = '';
        $ret['descricao_posicao']          = '';
        $ret['ex_posicao']                 = '';
        $ret['aliquota_posicao']           = '';
        $ret['descricao_subposicao']       = '';
        $ret['ex_subposicao']              = '';
        $ret['aliquota_subposicao']        = '';
        $ret['descricao_sub_item']         = '';
        $ret['ex_sub_item']                = '';
        $ret['aliquota_sub_item']          = '';
        $rodou = false;
        foreach ($tipi->sheetData[0] as $key => $value) {
            if($key > 0 && !empty($value[0])){
                $capitulo   = substr($value[0], 0, 2);
                $posicao    = substr($value[0], 0, 4);
                $subposicao = substr(str_replace(".","",$value[0]), 0, 6);
                $sub_item   = $value[0];
                $ncm        = (strlen($ncm) <= 7) ? '0'.$ncm : $ncm;

                
                if($subposicao == substr($ncm, 0, 6) && $rodou == false){
                    $ret['descricao_subposicao']          = $value[2];
                    $ret['ex_subposicao']                 = $value[1];
                    $ret['aliquota_subposicao']           = $value[3];
                    $rodou = true;
                }
            }
        }

        return $ret;
    }

    public function buscaDescNcmClientePosicao($tipi, $ncm){
        $ret = [];
        $ret['descricao_capitulo']         = '';
        $ret['ex_capitulo']                = '';
        $ret['aliquota_capitulo']          = '';
        $ret['descricao_posicao']          = '';
        $ret['ex_posicao']                 = '';
        $ret['aliquota_posicao']           = '';
        $ret['descricao_subposicao']       = '';
        $ret['ex_subposicao']              = '';
        $ret['aliquota_subposicao']        = '';
        $ret['descricao_sub_item']         = '';
        $ret['ex_sub_item']                = '';
        $ret['aliquota_sub_item']          = '';
        $rodou = false;
        foreach ($tipi->sheetData[0] as $key => $value) {
            if($key > 0 && !empty($value[0])){
                $capitulo   = substr($value[0], 0, 2);
                $posicao    = substr(str_replace(".","",$value[0]), 0, 4);
                $subposicao = substr($value[0], 0, 6);
                $sub_item   = $value[0];
                $ncm        = (strlen($ncm) <= 7) ? '0'.$ncm : $ncm;

                
                if($posicao == substr($ncm, 0, 4) && $rodou == false){
                    $ret['descricao_posicao']          = $value[2];
                    $ret['ex_posicao']                 = $value[1];
                    $ret['aliquota_posicao']           = $value[3];
                    $rodou = true;
                }
            }
        }

        return $ret;
    }

    public function buscaDescNcmClienteCapitulo($tipi, $ncm){
        $ret = [];
        $ret['descricao_capitulo']         = '';
        $ret['ex_capitulo']                = '';
        $ret['aliquota_capitulo']          = '';
        $ret['descricao_posicao']          = '';
        $ret['ex_posicao']                 = '';
        $ret['aliquota_posicao']           = '';
        $ret['descricao_subposicao']       = '';
        $ret['ex_subposicao']              = '';
        $ret['aliquota_subposicao']        = '';
        $ret['descricao_sub_item']         = '';
        $ret['ex_sub_item']                = '';
        $ret['aliquota_sub_item']          = '';
        $rodou = false;
        foreach ($tipi->sheetData[0] as $key => $value) {
            if($key > 0 && !empty($value[0])){
                $capitulo   = substr($value[0], 0, 2);
                $ncm        = (strlen($ncm) <= 7) ? '0'.$ncm : $ncm;

                if($capitulo == substr($ncm, 0, 2) && $rodou == false){
                    $ret['descricao_capitulo']          = $value[2];
                    $ret['ex_capitulo']                 = $value[1];
                    $ret['aliquota_capitulo']           = $value[3];
                    $rodou = true;
                }
            }
        }

        return $ret;
    }

    public function buscaDescNcmPreditado($tipi, $ncm){
        $ret = [];
        $ret['descricao_capitulo']         = '';
        $ret['ex_capitulo']                = '';
        $ret['aliquota_capitulo']          = '';
        $ret['descricao_posicao']          = '';
        $ret['ex_posicao']                 = '';
        $ret['aliquota_posicao']           = '';
        $ret['descricao_subposicao']       = '';
        $ret['ex_subposicao']              = '';
        $ret['aliquota_subposicao']        = '';
        $ret['descricao_sub_item']         = '';
        $ret['ex_sub_item']                = '';
        $ret['aliquota_sub_item']          = '';
        foreach ($tipi->sheetData[0] as $key => $value) {
            if($key > 0 && !empty($value[0])){
                $capitulo   = substr($value[0], 0, 2);
                $posicao    = substr($value[0], 0, 4);
                $subposicao = substr($value[0], 0, 6);
                $sub_item   = $value[0];
                $ncm        = (strlen($ncm) <= 7) ? '0'.$ncm : $ncm;

                if($capitulo == substr($ncm, 0, 2)){
                    $ret['descricao_capitulo'] = $value[2];
                    $ret['ex_capitulo']                 = $value[1];
                    $ret['aliquota_capitulo']           = $value[3];
                }
                if($posicao == substr($ncm, 0, 4)){
                    $ret['descricao_posicao']  = $value[2];
                    $ret['ex_posicao']                 = $value[1];
                    $ret['aliquota_posicao']           = $value[3];
                }
                if($subposicao == substr($ncm, 0, 6)){
                    $ret['descricao_subposicao']  = $value[2];
                    $ret['ex_subposicao']                 = $value[1];
                    $ret['aliquota_subposicao']           = $value[3];
                }
                if($sub_item == $ncm){
                    $ret['descricao_sub_item']           = $value[2];
                    $ret['ex_sub_item']                 = $value[1];
                    $ret['aliquota_sub_item']           = $value[3];
                }
            }
        }

        return $ret;
    }

    public function buscaCovenio142($convenio_142, $ncm){
        $ret = [];
        foreach ($convenio_142->sheetData[0] as $key => $value) {

            if(!empty($value[2])){
                $ncm_planiha = str_replace(".", "", $value[2]);
                if($ncm_planiha == $ncm){
                    $ret['item']      = $value[0];
                    $ret['cest']      = $value[1];   
                    $ret['descricao'] = $value[3];   
                }
            }
        }

        return $ret;

    }

    public function buscaMonofasico($ncm){
        $arrNcm = array("27101159",
                                "27101259",
                                "27101921",
                                "27111910",
                                "27101911",
                                "38249029",
                                "38249029",
                                "38260000",
                                "38260000",
                                "22071000",
                                "22072010",
                                "22089000",
                                "220710",
                                "2207201",
                                "22021000",
                                "22021000",
                                "22029000",
                                "22029000",
                                "22030000",
                                "22030000",
                                "70109021",
                                "39233000",
                                "73102110",
                                "76129019",
                                "39233000",
                                "22011000",
                                "22011000",
                                "22011000",
                                "21069010");
        if(in_array($ncm, $arrNcm)){ return true; }else{ return false; }
    }

    public function buscaAliquota42($produtos_aliquota_42, $ncm){
        $ret = [];
        foreach ($produtos_aliquota_42->sheetData[0] as $key => $value) {

            if(!empty($value[2])){

                $ncm_planiha = str_replace(".", "", $value[2]);

                if(strlen($ncm_planiha) < 8){
                    $ncm = substr($ncm, 0, strlen($ncm_planiha) + 1); 
                } 
                
                if($ncm_planiha == $ncm){
                    $ret['descricao'] = $value[1];
                    $ret['aliquota'] = $value[3];
                } 
            }
        }

        return $ret;

    }

    public function registraIa(Request $request){

        $produto     = $request->produto;
        $ncm_correto = $request->ncm_correto;

        $json  = '{"cod_ncm":'.$ncm_correto.',"descricao":"'.$produto.'", "nome":"'.$produto.'"},{"cod_ncm":'.$ncm_correto.',"descricao":"'.$produto.'", "nome":"'.$produto.'"},{"cod_ncm":'.$ncm_correto.',"descricao":"'.$produto.'", "nome":"'.$produto.'"},';
        $atual = json_encode($this->arrJsonAtual);
        $novo  = substr_replace($atual, $json, 1, 0);
        file_put_contents('trainingbkp.json', $novo); 

    }
}
