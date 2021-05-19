<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLotesProdutosAddColumnsNcmIaAcuraciaIa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lote_produtos', function (Blueprint $table) {
            $table->string('ia_ncm')->nullable();
            $table->float('acuracia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lote_produtos', function (Blueprint $table) {
            $table->dropColumn('ia_ncm');
            $table->dropColumn('acuracia');
        });
    }
}
