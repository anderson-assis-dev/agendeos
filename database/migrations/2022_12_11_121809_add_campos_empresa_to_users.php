<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposEmpresaToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('logradouro', 400)->nullable();
            $table->integer('numero')->nullable();
            $table->string('complemento', 400)->nullable();
            $table->string('bairro', 400)->nullable();
            $table->string('estado', 400)->nullable();
            $table->string('cidade', 400)->nullable();
            $table->string('cep')->nullable();
            $table->string('nome_fantasia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
