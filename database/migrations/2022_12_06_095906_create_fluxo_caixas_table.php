<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFluxoCaixasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fluxo_caixas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 400)->nullable();
            $table->integer('pagamento_id')->nullable();
            $table->integer('os_id')->nullable();
            $table->date('data')->nullable();
            $table->integer('produto_id')->nullable();
            $table->integer('cliente_id')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('fluxo_caixas');
    }
}
