<?php
namespace App\Http\Controllers\IA;
ini_set('memory_limit', '1024M');

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\EAuditor;
use Maatwebsite\Excel\Facades\Excel;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;

class IaController extends Controller
{
    public $classifier;
    public $arrJsonAtual;

    public function __construct()
    {
        $smsJSON            = @file_get_contents(storage_path('trainingbkp.json'));
        $this->arrJsonAtual = json_decode($smsJSON);

        if(!empty($this->arrJsonAtual)){
            $this->classifier = new TNTClassifier();

            for ($i = 0; $i < count($this->arrJsonAtual) -1; $i++) {
                $this->classifier->learn($this->arrJsonAtual[$i]->nome, $this->arrJsonAtual[$i]->cod_ncm);
            }
        }
    }

    public function retornaDadosIa($produto, $ncm){
        if(!rtrim($produto) && !rtrim($ncm))
            return response()->json(['success' => false,'msg' => 'Necessário enviar produto e NCM']);

        $ret = ['success' => true];

        $predict = $this->classifier->predict($produto);

        $probailidade = $this->getProbability($predict);
        
        $ret['ncm_ia']           = $predict['label'];
        $ret['probabilidade_ia'] = $probailidade;

        $convenio_142  = new EAuditor();
        Excel::import($convenio_142, storage_path('icms_convenio_142_2018.xls'));

        $produtos_aliquota_42  = new EAuditor();
        Excel::import($produtos_aliquota_42, storage_path('produtos_com_aliquota_42.xlsx'));

        $tipi  = new EAuditor();
        Excel::import($tipi, storage_path('tipi.xlsx'));

        $ret['desc_ncm_cliente_capitulo']   = $this->buscaDescNcmClienteCapitulo($tipi, $predict['label']);
        $ret['desc_ncm_cliente_posicao']    = $this->buscaDescNcmClientePosicao($tipi, $predict['label']);
        $ret['desc_ncm_cliente_subposicao'] = $this->buscaDescNcmClienteSubPosicao($tipi, $predict['label']);
        $ret['desc_ncm_cliente_subitem']    = $this->buscaDescNcmClienteSubItem($tipi, $predict['label']);

        $ret['ret_convenio_142']            = $this->buscaCovenio142($convenio_142, $predict['label']);

        $ret['ret_aliquota_42']             = $this->buscaAliquota42($produtos_aliquota_42, $predict['label']);

        $ret['ret_monofasico']              = $this->buscaMonofasico($predict['label']);
        return $ret;
    }

    public function comparaNcm(Request $request){

        if(!$request->has('ia') || !$request->has('importado'))
            return response()->json(['success' => false,'msg' => 'Necessário NCM'], 400);

        $consulta = [
            'ncm' => $request->get('ia'),
            'importado' => $request->get('importado'),
        ];

        $convenio_142  = new EAuditor();
        Excel::import($convenio_142, storage_path('icms_convenio_142_2018.xls'));

        $produtos_aliquota_42  = new EAuditor();
        Excel::import($produtos_aliquota_42, storage_path('produtos_com_aliquota_42.xlsx'));

        $tipi  = new EAuditor();
        Excel::import($tipi, storage_path('tipi.xls'));

        foreach ($consulta as $tipo => $ncm) {
            $ret[$tipo]['desc_ncm_cliente_capitulo']   = $this->buscaDescNcmClienteCapitulo($tipi, $ncm);
            $ret[$tipo]['desc_ncm_cliente_posicao']    = $this->buscaDescNcmClientePosicao($tipi, $ncm);
            $ret[$tipo]['desc_ncm_cliente_subposicao'] = $this->buscaDescNcmClienteSubPosicao($tipi, $ncm);
            $ret[$tipo]['desc_ncm_cliente_subitem']    = $this->buscaDescNcmClienteSubItem($tipi, $ncm);
    
            $ret[$tipo]['ret_convenio_142']            = $this->buscaCovenio142($convenio_142, $ncm);
    
            $ret[$tipo]['ret_aliquota_42']             = $this->buscaAliquota42($produtos_aliquota_42, $ncm);
    
            $ret[$tipo]['ret_monofasico']              = $this->buscaMonofasico($ncm);
        }

        return response()->json($ret, 200);
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
}