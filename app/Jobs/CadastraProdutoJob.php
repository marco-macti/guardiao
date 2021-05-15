<?php
namespace App\Jobs;
ini_set('memory_limit', '1024M');

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\IA\IaController;
use App\Models\LoteProduto;

class CadastraProdutoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $produto;
    protected $lote_id;
    public $tries = 2;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($produto, $lote_id)
    {
        $this->produto = $produto;
        $this->lote_id = $lote_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ia_instance = new IaController();
        $reponse = $ia_instance->retornaDadosIa($this->produto[1], $this->produto[2]);
        
        $create = LoteProduto::create([
            'lote_id'                   => $this->lote_id,
            'codigo_interno_do_cliente' => $this->produto[0],
            'descricao_do_produto'      => $this->produto[1],
            'ncm_importado'             => $this->produto[2],
            'ia_ncm'                    => $reponse['ncm_ia'],
            'acuracia'                  => $reponse['probabilidade_ia'],
        ]);
    }
}
