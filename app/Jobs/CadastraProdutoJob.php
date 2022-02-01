<?php
namespace App\Jobs;
ini_set('memory_limit', '1024M');

//Illuminate
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

//Classes Envio de Email
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailUser;
use App\Mail\LoteImportado;

//Controllers
use App\Http\Controllers\IA\IaController;

//Models
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\LoteProduto;
use App\Models\LoteProdutoAuditoria;

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

        // Queue::after(function (JobProcessed $event) {

        //     $lote    = Lote::find($this->lote_id);
        //     $cliente = Cliente::find($lote->cliente_id);

        //     Mail::to($cliente->email_cliente)->send(new LoteImportado($lote));

        // });
    }

    public function handle()
    {
        // $ia_instance = new IaController();

        if($this->tipo == "CSV"){

            foreach ($this->data as $item) {

                // $reponse = $ia_instance->retornaDadosIa($item['DESCRICAO_DO_PRODUTO'], $item['NCM_NO_CLIENTE']);

                $verifica_produto = LoteProduto::where('codigo_interno_do_cliente', $item['CODIGO_NO_CLIENTE'])
                                                    ->where('descricao_do_produto', $item['DESCRICAO_DO_PRODUTO'])
                                                    ->where('ncm_importado', $item['NCM_NO_CLIENTE'])
                                                    ->where('lote_id', $this->lote_id)
                                                    ->first();

                if($verifica_produto)
                    continue;

                $loteProduto = LoteProduto::create([
                    'lote_id'                   => $this->lote_id,
                    'codigo_interno_do_cliente' => $item['CODIGO_NO_CLIENTE'],
                    'descricao_do_produto'      => $item['DESCRICAO_DO_PRODUTO'],
                    'ncm_importado'             => $item['NCM_NO_CLIENTE'],
                    'ia_ncm'                    => '',
                    'acuracia'                  => 0,
                ]);


            }

            $lote_instance = Lote::find($this->lote_id);
            $lote_count_produtos = LoteProduto::where('lote_id', $this->lote_id)->count();

            if($lote_instance->quantidade_de_produtos = $lote_count_produtos)
            {
                
            }

        }elseif($this->tipo == "NFXML"){

            foreach ($this->data as $key => $obj) {

                // $reponse = $ia_instance->retornaDadosIa($obj['prod']['xProd'], $obj['prod']['NCM']);

                $verifica_produto = LoteProduto::where('codigo_interno_do_cliente', $obj['prod']['cProd'])
                                                ->where('descricao_do_produto', $obj['prod']['xProd'])
                                                ->where('ncm_importado', $obj['prod']['NCM'])
                                                ->where('lote_id', $this->lote_id)
                                                ->first();

                if($verifica_produto)
                    continue;

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
                    'ia_ncm'                    => '',
                    'acuracia'                  => 0
                ]);

                // if($obj['prod']['NCM'] == $reponse['ncm_ia'] ){

                //     LoteProdutoAuditoria::create([
                //         'lote_id'         => $this->lote_id,
                //         'lote_produto_id' => $loteProduto->id,
                //         'ncm_importado'   => $reponse['ncm_ia'],
                //         'ncm_auditado'    => $reponse['ncm_ia'],
                //         'pre_auditado'    => 'S'
                //     ]);

                // }
            }

        }elseif($this->tipo == "SPEED"){

            foreach ($this->data as $key => $produto) {

                //  $reponse = $ia_instance->retornaDadosIa($produto[3], $produto[8]);

                $verifica_produto = LoteProduto::where('codigo_interno_do_cliente', $produto[2])
                                                ->where('descricao_do_produto', $produto[3])
                                                ->where('ncm_importado', $produto[8])
                                                ->where('lote_id', $this->lote_id)
                                                ->first();

                if($verifica_produto)
                    continue;

                 $loteProduto = LoteProduto::create([
                     'lote_id'                   => $this->lote_id,
                     'codigo_interno_do_cliente' => $produto[2],
                     'descricao_do_produto'      => $produto[3],
                     'ean_gtin'                  => $produto[4],
                     'ncm_importado'             => $produto[8],
                     'ia_ncm'                    => '',
                     'acuracia'                  => 0,
                 ]);

                //  if($produto[8] == $reponse['ncm_ia'] ){

                //     LoteProdutoAuditoria::create([
                //         'lote_id'         => $this->lote_id,
                //         'lote_produto_id' => $loteProduto->id,
                //         'ncm_importado'   => $reponse['ncm_ia'],
                //         'ncm_auditado'    => $reponse['ncm_ia'],
                //         'pre_auditado'    => 'S'
                //     ]);

                // }

            }

        }

        $lote    = Lote::find($this->lote_id);

        $lote->status_importacao = 1;
        $lote->save();

        $cliente = Cliente::find($lote->cliente_id);

        Mail::to($cliente->email_cliente)->send(new LoteImportado($lote));
    }

    public function failed(Throwable $exception)
    {
        $lote    = Lote::find($this->lote_id);

        $lote->status_importacao = 2;
        $lote->save();
    }
}
