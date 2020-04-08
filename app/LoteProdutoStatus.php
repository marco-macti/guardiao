<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoteProdutoStatus extends Model
{
    const NAO_ANALISADO                = 1;
    const LOTE_CANCELADO               = 2;
    const AGUARDANDO_RETORNO_DO_COSMOS = 3;
    const AGUARDANDO_RETORNO_DO_IOB    = 4;
    const EM_MONITORAMENTO             = 5;
    const PRODUTO_VALIDADO             = 6;
    const GTIN_INEXISTENTE_NO_COSMOS   = 7;
}
