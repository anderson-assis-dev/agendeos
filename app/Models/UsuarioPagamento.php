<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioPagamento extends Model
{
    use SoftDeletes;

    protected $table = 'usuario_pagamento';
    protected $fillable = [
        'transacao_id',
        'data_transacao',
        'usuario_id',
        'situacao_id',
    ];

    protected $dates = ['deleted_at'];
}
