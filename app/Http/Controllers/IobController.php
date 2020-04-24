<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IobController extends Controller
{
    public function index(){

        return view('iob.form');

    }

    public function importSheet(Request $request){

        /*array:3 [▼
          "cliente" => "8"
          "lote" => "95"
          "sheet" => "PLANILHA LAUTON ALIQUOTA TESTE.xlsx"
        ]*/

        dd($request->all());                                                                                                                                                       

        $file = $request->file('sheet');

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load ($file->getRealPath() );
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = $cells;
        }

        // Remove as celulas inicias de 0 a 7

        for ($i = 0; $i <= 7; $i++){
            unset($rows[$i]);
        }

        /*$base = array [
            0 => "SKU"
            1 => "Descrição-SKU"
            2 => "EAN/GTIN"
            3 => "Descrição-EAN"
            4 => "NCM"
            5 => "EX"
            6 => "Origem Produto"
            7 => "Tributado 4%"
            8 => "Operação"
            9 => "UF Origem"
            10 => "UF Destino"
            11 => "Estabelecimento Origem"
            12 => "Tributação Estabelecimento Origem"
            13 => "Tipo de destinatário"
            14 => "Tributação destinatário"
            15 => "Destinação da mercadoria"
            16 => "ICMS - Operação Interestadual - Alíquota"
            17 => "ICMS - Operação Interna - Alíquota"
            18 => "ICMS - Operação Interna - Base Legal"
            19 => "ICMS - Operação Interna - Início de Vigência"
            20 => "ICMS - Operação Interna - Fim de Vigência"
            21 => "ICMS - Alíquota ao Fundo - Alíquota ao Fundo"
            22 => "ICMS - Alíquota ao Fundo - Base Legal"
            23 => "ICMS - Alíquota ao Fundo - Início de Vigência"
            24 => "ICMS - Alíquota ao Fundo - Fim de Vigência"
            25 => "DIFAL"
            26 => "Redução de Base de Cálculo - Próprio - % de redução"
            27 => "Redução de Base de Cálculo - Próprio - % a tributar"
            28 => "Redução de Base de Cálculo - Próprio - Base Legal"
            29 => "Redução de Base de Cálculo - Próprio - Descrição"
            30 => "Redução de Base de Cálculo - Próprio - Descrição Condição"
            31 => "Redução de Base de Cálculo - Próprio - Informações Complementares"
            32 => "Redução de Base de Cálculo - Próprio - Início de Vigência"
            33 => "Redução de Base de Cálculo - Próprio - Fim de Vigência"
            34 => "Redução de Base de Cálculo - ST - % de redução"
            35 => "Redução de Base de Cálculo - ST - % a tributar"
            36 => "Redução de Base de Cálculo - ST - Base Legal"
            37 => "Redução de Base de Cálculo - ST - Descrição"
            38 => "Redução de Base de Cálculo - ST - Descrição Condição"
            39 => "Redução de Base de Cálculo - ST - Informações Complementares"
            40 => "Redução de Base de Cálculo - ST - Início de Vigência"
            41 => "Redução de Base de Cálculo - ST - Fim de Vigência"
            42 => "ICMS - Substituição Tributária - MVA Original"
            43 => "ICMS - Substituição Tributária - MVA Ajustado"
            44 => "ICMS - Substituição Tributária - Base Legal"
            45 => "ICMS - Substituição Tributária - CEST"
            46 => "ICMS - Substituição Tributária - Descrição Conforme Ato Normativo"
            47 => "ICMS - Substituição Tributária - Informação complementar"
            48 => "ICMS - Substituição Tributária - Início de Vigência"
            49 => "ICMS - Substituição Tributária - Fim de Vigência"
            50 => "ICMS - Substituição Tributária - Observações"
            51 => "ICMS – ST - % Carga Tributária Média"
            52 => "ICMS – ST - % Carga Tributária ao Fundo"
            53 => "ICMS – ST – Carga Total"
            54 => "ICMS – ST – Carga – Base Legal"
            55 => "ICMS – ST – Carga – CEST"
            56 => "ICMS – ST – Carga – Início de Vigência"
            57 => "ICMS – ST – Carga – Fim de Vigência"
            58 => "IPI - Aliquota"
            59 => "IPI - Informação Complementar"
            60 => "IPI - Base Legal"
            61 => "IPI - Início de Vigência"
            62 => "IPI - Fim deVigência"
            63 => "Alíquota Pis/Pasep"
            64 => "CST Pis-Pasep"
            65 => "Base Legal Pis/Pasep"
            66 => "Pis-Pasep - Início de Vigência"
            67 => "Pis-Pasep - Fim de Vigência"
            68 => "Alíquota Cofins"
            69 => "CST Cofins"
            70 => "Base Legal Cofins"
            71 => "Cofins - Início de Vigência"
            72 => "Cofins - Fim de Vigência"
        ];*/

        dd($rows);

    }
}
