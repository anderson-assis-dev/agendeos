<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayPagamento extends Model
{
    use HasFactory;
    protected $table = 'gateway_pagamento';
    protected $fillable = ['nome', 'endpoint_producao', 'endpoint_homologacao','token_homologacao', 'token_producao'];
}
