<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLotesProdutosCreateColumnsTributacaoMonofarisco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lote_produtos', function(Blueprint $table){
            $table->enum('tipo_tributacao',['SUBSTITUIÇÃO TRIBUITÁRIA', 'MONOFÁSICO', 'TRIBUTAÇÃO'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lote_produtos', function(Blueprint $table){
            $table->dropColumn('tipo_tributacao');
        });
    }
}
