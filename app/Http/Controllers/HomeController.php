<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

    public function index(){
        return response()->json([
            'APP_NAME' => 'Guardiao Tributário',
            'API_V'    => '0.0.1'
        ]);
    }

    public function importarBCProdutoAux(){

        $path = "app".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."bc_produto_aux".DIRECTORY_SEPARATOR."tbl_eans_780.json";

        if(file_exists(storage_path($path))){
            $file    = json_decode(file_get_contents(storage_path($path)));
            $records = $file->{"RECORDS"};

            foreach ($records as $index => $record) {
                echo $record['gtin'];
            }

            echo "Processo finalizado";
        }else{
            echo "File or path not found";
        }

    }
}
