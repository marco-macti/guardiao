<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotesTable extends Migration
{
    public function up()
    {
        
        Schema::create('lotes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('numero_do_lote');
            $table->unsignedInteger('cliente_id');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('lotes');
    }
}
