<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixasProdutos extends Model
{
    // Nome da tabela associada ao modelo
    protected $table = 'fluxo_caixas_produtos';

    // Nome da coluna que é a chave primária da tabela
    protected $primaryKey = 'id';

    // Colunas que podem ser preenchidas em massa (se for o caso)
    protected $fillable = ['fluxo_caixas_id', 'produto_id', 'valor', "quantidade"];

    // Campos de data
    protected $dates = ['created_at', 'updated_at'];

    // Define se as colunas de data devem ser registradas
    public $timestamps = true;
}
