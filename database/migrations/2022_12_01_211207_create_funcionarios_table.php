<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuncionariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 400)->nullable();
            $table->string('nome_mae', 400)->nullable();
            $table->string('nome_pai', 400)->nullable();
            $table->string('cpf')->nullable()->nullable();
            $table->string('rg')->nullable()->nullable();
            $table->string('email', 500)->nullable();
            $table->string('telefone', 400)->nullable();
            $table->string('celular', 400)->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->date('emissao_rg')->nullable();
            $table->date('emissao_ctps')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->date('data_admissao')->nullable();
            $table->date('data_exame_adimissional')->nullable();
            $table->string('registro_militar', 400)->nullable();
            $table->string('registro_habilitacao', 400)->nullable();
            $table->string('categoria_habilitacao', 400)->nullable();
            $table->string('registro_eleitoral', 400)->nullable();
            $table->string('zona', 400)->nullable();
            $table->string('secao', 400)->nullable();
            $table->integer('numero_pis')->nullable();
            $table->integer('numero_dependentes')->nullable();
            $table->integer('numero_camisa')->nullable();
            $table->integer('numero_calca')->nullable();
            $table->integer('numero_sapato')->nullable();
            $table->integer('quantidade_trechos_transporte')->nullable();
            $table->integer('funcao_id')->nullable();
            $table->integer('tipo_contrato_id')->nullable();
            $table->string('orgao_emissor_rg', 500)->nullable();
            $table->string('n_ctps')->nullable();
            $table->string('serie_ctps')->nullable();
            $table->string('cep')->nullable();
            $table->string('local_nascimento')->nullable();
            $table->string('conta')->nullable();
            $table->float('salario_contratual', 8, 2);
            $table->string('agencia')->nullable();
            $table->string('tipo_chave_pix')->nullable();
            $table->string('chave_pix')->nullable();
            $table->string('banco')->nullable();
            $table->string('nacionalidade')->nullable();
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
        Schema::dropIfExists('funcionarios');
    }
}
