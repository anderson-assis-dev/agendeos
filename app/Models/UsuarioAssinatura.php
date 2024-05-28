<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioAssinatura extends Model
{
    use SoftDeletes;

    protected $table = 'usuario_assinatura';
    protected $fillable = [
        'plano_id',
        'user_id',
        'data_assinatura',
        'data_renovacao',
        'usuario_pagamento_id',
        'referencia_id',
        'teste',
        'inicio_teste',
        'data_pagamento',
        'fim_teste',
        'ativo'
    ];

    protected $dates = ['deleted_at'];
}
