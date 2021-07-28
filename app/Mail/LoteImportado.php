<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoteImportado extends Mailable
{
    use Queueable, SerializesModels;

    public $lote;

    public function __construct($lote)
    {
        $this->lote = $lote;
    }

    public function build()
    {
        return $this->from('projetos@greensignal.com.br')
                    ->view('frontend.mails.lote-importado')
                    ->with('lote',$this->lote);

    }
}
