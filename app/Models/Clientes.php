<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
    protected $fillable =
    ['nome_f', 'cpf', 'rg', 'email_f', 'telefone_f', 'celular_f', 'sexo', 'nome_j', 'email_j', 'telefone_j', 'celular_j',
     'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'estado', 'cidade', 'observacoes', 'cnpj', 'ie', 'nome_rj', 'ativo',
     'email_rj', 'telefone_rj', 'celular_rj', 'tipo_cliente', 'ie', 'razao_social', 'user_id', 'gateway_assinante_id', 'data_aniversario'];
}
