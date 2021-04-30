<?php

namespace App\Helpers;

class Sanitize
{

    public static function sanitizeValueForMask($string)
    {
        $string = trim($string);
        $string = str_replace(".", "", $string);
        $string = str_replace(",", "", $string);
        $string = str_replace(";", "", $string);
        $string = str_replace("-", "", $string);
        $string = str_replace("_", "", $string);
        $string = str_replace("/", "", $string);
        $string = str_replace(" ", "", $string);

        return $string;
    }
}