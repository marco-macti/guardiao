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

class AuditarLoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lote_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lote_id)
    {
        $this->lote_id = $lote_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $output = new Symfony\Component\Console\Output\ConsoleOutput();
        // $output->writeln("Iniciou Auditar");

        $ia_instance = new IaController();

        $produtos = LoteProduto::where('lote_id' , $this->lote_id)->get();

        foreach ($produtos as $key => $produto)
        {
            // $output->writeln("ncm = ".$produto->ncm_importado);

            $response = $ia_instance->retornaDadosIa($produto->descricao_do_produto, $produto->ncm_importado);

            if($produto->ncm_importado == $response['ncm_ia'] )
            {
                LoteProdutoAuditoria::create([
                    'lote_id'         => $this->lote_id,
                    'lote_produto_id' => $produto->id,
                    'ncm_importado'   => $response['ncm_ia'],
                    'ncm_auditado'    => $response['ncm_ia'],
                    'pre_auditado'    => 'S'
                ]);

                if($this->monofasico($produto->ncm_importado)){
                    $produto->tipo_tributacao = 'MONOFÁSICO';
                }else{
                    if($this->st($produto->ncm_importado)){
                        $produto->tipo_tributacao = 'SUBSTITUIÇÃO TRIBUITÁRIA';
                    }else{
                        $produto->tipo_tributacao = 'TRIBUTAÇÃO';
                    }
                }

                $produto->ia_ncm    = $response['ncm_ia'];
                $produto->acuracia  = $response['probabilidade_ia'];
                $produto->update();
            }
        }
    }

    public function monofasico($ncm)
    {
        $pathFileMonofasico = public_path("monofasico.csv");
        
        $dados = fopen($pathFileMonofasico, "r");

        while(($data = fgetcsv($dados, 1000, ",")) !== FALSE){
            if($data[0] == $ncm)
                return true;
        }
        
        return false;
    }

    public function st($ncmConsulta)
    {
        $pathFileMonofasico = public_path("st.csv");
        
        $dados = fopen($pathFileMonofasico, "r");

        while(($data = fgetcsv($dados, 1000, ",")) !== FALSE){
            $ncms = explode(' ', $data[0]);

            foreach ($ncms as $ncm) {
                if($ncmConsulta == preg_replace( '/[^0-9]/', '', $ncm))
                    return true;
            }
        }
        
        return false;
    }
}
