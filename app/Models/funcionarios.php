<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class funcionarios extends Model
{
    use HasFactory;
    protected $fillable =
    ['nome', 'cpf', 'rg', 'email', 'telefone', 'celular', 'sexo','ativo',
     'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'estado', 'cidade', 'observacoes',
     'comissao', 'user_id', 'foto'];
}
