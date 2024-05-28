<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            
            $table->string('razao_social', 500)->nullable();
            $table->string('cnpj')->nullable();
            $table->string('ie')->nullable();
            $table->string('nome_rj', 400)->nullable();
            $table->string('email_rj', 500)->nullable();
            $table->string('telefone_rj')->nullable();
            $table->string('celular_rj')->nullable();
            $table->enum('tipo_cliente', ['PJ', 'PF']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            //
        });
    }
}
