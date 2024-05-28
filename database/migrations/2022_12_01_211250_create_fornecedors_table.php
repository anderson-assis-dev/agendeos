<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFornecedorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fornecedors', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social', 500)->nullable();
            $table->string('cnpj')->nullable();
            $table->string('ie')->nullable();
            $table->string('nome', 400)->nullable();
            $table->string('email', 500)->nullable();
            $table->string('telefone')->nullable();
            $table->string('celular')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro', 400)->nullable();
            $table->integer('numero')->nullable();
            $table->string('complemento', 400)->nullable();
            $table->string('bairro', 400)->nullable();
            $table->string('estado', 400)->nullable();
            $table->string('cidade', 400)->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('fornecedors');
    }
}
