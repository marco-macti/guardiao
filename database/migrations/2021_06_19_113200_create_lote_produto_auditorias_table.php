<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoteProdutoAuditoriasTable extends Migration
{
    public function up()
    {
        Schema::create('lote_produtos_auditorias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lote_id');
            $table->unsignedInteger('lote_produto_id');
            $table->string('ncm_importado');
            $table->string('ncm_auditado');
            $table->enum('pre_auditado',['S','N'])->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lote_produto_auditorias');
    }
}
