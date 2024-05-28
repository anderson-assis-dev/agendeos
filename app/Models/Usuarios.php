<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $fillable = [
        'name', 'email',  'cep', 'logradouro', 'complemento', 'numero', 'cidade','estado',
        'bairro' ,'empresa_id',"funcionario_id" ,'use_terms', 'gateway_assinante_id'];
}
