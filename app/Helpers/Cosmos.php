<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Cosmos extends Model
{

    protected $apiBase = "https://api.cosmos.bluesoft.com.br/";
    protected $callUrl;
    protected $curl;

    public static function getByDescricao($descricao){

        $descricao = str_replace(' ','%20',$descricao);

        $url = "https://api.cosmos.bluesoft.com.br/products?query=$descricao";
        //$url = 'https://api.cosmos.bluesoft.com.br/products?query=Abridor%20de%20Garrafa';

        $cosmosApiKey = env('COSMOS_API_KEY');

        $headers = array(
            "Content-Type: application/json",
            "X-Cosmos-Token: $cosmosApiKey"
          );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);

        $data = curl_exec($curl);

        if ($data === false || $data == NULL) {

          dd(curl_error($curl));

        } else {
          $object = json_decode($data);

          dd($object->products);
        }

        curl_close($curl);

        die;

    }
}
