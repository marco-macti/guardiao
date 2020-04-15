<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IobController extends Controller
{
    public function index(){
        return view('iob.form');
    }

    public function importSheet(Request $request){


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

        dd($rows);

    }
}
