<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RunJobsLotes extends Command
{
    protected $signature = 'runjobslotes';
    protected $description = 'Procura no banco de dados por lotes em aberto e os roda.';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $filas = DB::select('SELECT queue FROM jobs WHERE attempts = 0');

        foreach ($filas as $fila) {

            $command  = "queue:work --queue=$fila->queue --memory=256 --timeout=0";

            Artisan::call($command);

        }

        die;
    }
}
