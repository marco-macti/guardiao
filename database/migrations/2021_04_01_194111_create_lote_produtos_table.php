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
            $table->string('codigo_interno_do_cliente');
            $table->longText('descricao_do_produto');
            $table->string('ncm',8);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lote_produtos');
    }
}
