<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('razao_social')->nullable();
            $table->string('inscricao_estadual')->nullable();
            $table->enum('ativo',['S','N'])->default('S');
            $table->string('nome_do_responsavel')->nullable();
            $table->string('tel1')->nullable();
            $table->string('tel2')->nullable();
            $table->string('email_cliente')->nullable();
            $table->date('dt_nascimento')->nullable();
            $table->string('operacao')->nullable();
            $table->string('enquadramento_tributario')->nullable();
            $table->string('estado_origem')->nullable();
            $table->string('estado_destino')->nullable();
            $table->longText('anotacoes')->nullable();
            $table->unsignedInteger('numero_de_lotes')->nullable();
            $table->enum('em_degustacao',['S','N'])->default('N');
            $table->date('dt_inicio_degustacao')->nullable();
            $table->date('qtd_ncms_degustacao')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}

