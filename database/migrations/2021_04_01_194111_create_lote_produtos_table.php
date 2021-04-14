<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoteProdutosTable extends Migration
{
    public function up()
    {
        Schema::create('lote_produtos', function (Blueprint $table) {
             
            $table->increments('id');
            $table->unsignedInteger('lote_id');
            $table->string('codigo_interno_do_cliente');
            $table->longText('descricao_do_produto');
            $table->string('ncm_importado',8);
            $table->timestamps();

            $table->foreign('lote_id')->references('id')->on('lotes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lote_produtos');
    }
}
