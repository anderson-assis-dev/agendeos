<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planos extends Model
{
    use HasFactory;
    protected $table = 'planos';
    protected $fillable = ['plano', 'descricao', 'valor', 'porcentagem_fixa_os', 'valor_fixo_os', 'situacao', 'gateway_plano_id'];
}
