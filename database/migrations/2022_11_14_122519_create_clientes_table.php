<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome_f', 400)->nullable();
            $table->string('cpf')->nullable()->nullable();
            $table->string('rg')->nullable()->nullable();
            $table->string('email_f', 500)->nullable();
            $table->string('telefone_f')->nullable();
            $table->string('celular_f')->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->string('nome_j', 400)->nullable();
            $table->string('email_j', 500)->nullable();
            $table->string('telefone_j')->nullable();
            $table->string('celular_j')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro', 400)->nullable();
            $table->integer('numero')->nullable();
            $table->string('complemento', 400)->nullable();
            $table->string('bairro', 400)->nullable();
            $table->string('estado', 400)->nullable();
            $table->string('cidade', 400)->nullable();
            $table->longText('observacoes')->nullable();
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
        Schema::dropIfExists('clientes');
    }
}
