<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cliente');
            $table->integer('id_servico');
            $table->integer('id_veiculo');
            $table->integer('situacao');
            $table->dateTime('inicio_os');
            $table->dateTime('previsao_os');
            $table->longText('observacoes')->nullable();
            $table->integer('remarketing')->nullable();
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
        Schema::dropIfExists('ordem_servicos');
    }
}
