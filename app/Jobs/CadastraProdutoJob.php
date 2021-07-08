<?php
namespace App\Jobs;
ini_set('memory_limit', '1024M');

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Controllers\IA\IaController;
use App\Mail\LoteImportado;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\LoteProduto;
use App\Models\LoteProdutoAuditoria;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Mail;

class CadastraProdutoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $produto;
    protected $lote_id;
    protected $tipo;
    protected $data;
    public $tries = 2;
    public $timeout = 0;

    public function __construct($lote_id,$data,$tipo)
    {
        $this->lote_id = $lote_id;
        $this->data    = $data;
        $this->tipo    = $tipo;
    }


    public function boot()
    {
        /*Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });*/

        Queue::after(function (JobProcessed $event) {

            $lote    = Lote::find($this->lote_id);
            $cliente = Cliente::find($lote->cliente_id);

            Mail::to($cliente->email_cliente)->send(new LoteImportado($lote));

        });
    }

    public function handle()
    {
        $ia_instance = new IaController();

        if($this->tipo == "CSV"){

            foreach ($this->data as $item) {

                $reponse = $ia_instance->retornaDadosIa($item['DESCRICAO_DO_PRODUTO'], $item['NCM_NO_CLIENTE']);

                $loteProduto = LoteProduto::create([
                    'lote_id'                   => $this->lote_id,
                    'codigo_interno_do_cliente' => $item['CODIGO_NO_CLIENTE'],
                    'descricao_do_produto'      => $item['DESCRICAO_DO_PRODUTO'],
                    'ncm_importado'             => $item['NCM_NO_CLIENTE'],
                    'ia_ncm'                    => $reponse['ncm_ia'],
                    'acuracia'                  => $reponse['probabilidade_ia'],
                ]);

                if($item['NCM_NO_CLIENTE'] == $reponse['ncm_ia'] ){

                    LoteProdutoAuditoria::create([
                        'lote_id'         => $this->lote_id,
                        'lote_produto_id' => $loteProduto->id,
                        'ncm_importado'   => $reponse['ncm_ia'],
                        'ncm_auditado'    => $reponse['ncm_ia'],
                        'pre_auditado'    => 'S'
                    ]);

                }


            }

        }elseif($this->tipo == "NFXML"){

            foreach ($this->data as $key => $obj) {

                $reponse = $ia_instance->retornaDadosIa($obj['prod']['xProd'], $obj['prod']['NCM']);

                $loteProduto =  LoteProduto::create([
                    'lote_id'                   => $this->lote_id,
                    'codigo_interno_do_cliente' => $obj['prod']['cProd'],
                    'descricao_do_produto'      => $obj['prod']['xProd'],
                    'ean_gtin'                  => $obj['prod']['cEAN'],
                    'cest'                      => $obj['prod']['CEST'],
                    'cfop'                      => $obj['prod']['CFOP'],
                    'quantidade'                => $obj['prod']['qTrib'],
                    'valor'                     => $obj['prod']['vUnTrib'],
                    'valor_desconto'            => $obj['prod']['vDesc'],
                    'ncm_importado'             => $obj['prod']['NCM'],
                    'ia_ncm'                    => $reponse['ncm_ia'],
                    'acuracia'                  => round($reponse['probabilidade_ia'])
                ]);

                if($obj['prod']['NCM'] == $reponse['ncm_ia'] ){

                    LoteProdutoAuditoria::create([
                        'lote_id'         => $this->lote_id,
                        'lote_produto_id' => $loteProduto->id,
                        'ncm_importado'   => $reponse['ncm_ia'],
                        'ncm_auditado'    => $reponse['ncm_ia'],
                        'pre_auditado'    => 'S'
                    ]);

                }
            }

        }elseif($this->tipo == "SPEED"){

            foreach ($this->data as $key => $produto) {

                 $reponse = $ia_instance->retornaDadosIa($produto[3], $produto[8]);

                 $loteProduto = LoteProduto::create([
                     'lote_id'                   => $this->lote_id,
                     'codigo_interno_do_cliente' => $produto[2],
                     'descricao_do_produto'      => $produto[3],
                     'ean_gtin'                  => $produto[4],
                     'ncm_importado'             => $produto[8],
                     'ia_ncm'                    => $reponse['ncm_ia'],
                     'acuracia'                  => round($reponse['probabilidade_ia']),
                 ]);

                 if($produto[8] == $reponse['ncm_ia'] ){

                    LoteProdutoAuditoria::create([
                        'lote_id'         => $this->lote_id,
                        'lote_produto_id' => $loteProduto->id,
                        'ncm_importado'   => $reponse['ncm_ia'],
                        'ncm_auditado'    => $reponse['ncm_ia'],
                        'pre_auditado'    => 'S'
                    ]);

                }

            }

        }

        /*$lote    = Lote::find($this->lote_id);
        $cliente = Cliente::find($lote->cliente_id);

        Mail::to($cliente->email_cliente)->send(new LoteImportado($lote));
        */
    }
}
