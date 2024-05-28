<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetornoPagamento extends Model
{
    // Nome da tabela associada ao modelo
    protected $table = 'retorno_pagamentos';

    // Nome da coluna que é a chave primária da tabela
    protected $primaryKey = 'id';

    // Colunas que podem ser preenchidas em massa (se for o caso)
    protected $fillable = ['retorno', 'os_id', 'status'];

    // Campos de data
    protected $dates = ['created_at', 'updated_at'];

    // Define se as colunas de data devem ser registradas
    public $timestamps = true;
}
