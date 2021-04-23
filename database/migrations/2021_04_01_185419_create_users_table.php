<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cliente_id');
            $table->enum('is_superuser',['Y','N'])->default('N');
            $table->enum('is_staff',['Y','N'])->default('N');
            $table->enum('is_active',['Y','N'])->default('N');
            $table->enum('confirmed',['Y','N'])->default('N');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');

        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
