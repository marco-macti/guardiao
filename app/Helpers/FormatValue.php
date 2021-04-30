<?php

namespace App\Helpers;

class FormatValue
{
    static function stringToDateBr($string)
    {
        $dia = substr($string, 0, 2);
        $mes = substr($string, 2, 2);
        $ano = substr($string, 4, 4);

        return $dia.'/'.$mes.'/'.$ano;
    }
}