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
            $table->string('ncm_importado');
            $table->string('numero_do_documento_fiscal')->nullable()->comment('Usado para nfe');
            $table->string('ean_gtin')->nullable()->comment('Usado para nfe e SPEED');
            $table->string('cest')->nullable()->comment('Usado para nfe');
            $table->string('cfop')->nullable()->comment('Usado para nfe');
            $table->string('quantidade')->nullable()->comment('Usado para nfe');
            $table->float('valor')->nullable()->comment('Usado para nfe');
            $table->float('valor_frete')->nullable()->comment('Usado para nfe');
            $table->float('valor_desconto')->nullable()->comment('Usado para nfe');
            $table->timestamps();

            $table->foreign('lote_id')->references('id')->on('lotes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lote_produtos');
    }
}
