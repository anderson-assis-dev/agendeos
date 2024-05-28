<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanosDescricao extends Model
{
    use HasFactory;
    protected $table = 'planos_descricao';
    protected $fillable = ['plano_id', 'item'];
}
