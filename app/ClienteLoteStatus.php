<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClienteLoteStatus extends Model
{
    const ABERTO             = 1;
    const VALIDANDO_PRODUTOS_= 2;
    const PRODUTOS_VALIDADOS = 3;
    const EM_MONITORAMENTO   = 4;
    const CANCELADO          = 5;
}
