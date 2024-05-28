<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    use HasFactory;
    protected $table = 'empresas';
    protected $fillable = [
        'razao_social', 'cnpj', 'situacao', 'plano_id', 'segmento_id', 'telefone',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'estado', 'cidade',
        'use_terms',
        'segunda_horario_inicio', 'segunda_horario_fim',
        'terca_horario_inicio', 'terca_horario_fim',
        'quarta_horario_inicio', 'quarta_horario_fim',
        'quinta_horario_inicio', 'quinta_horario_fim',
        'sexta_horario_inicio', 'sexta_horario_fim',
        'sabado_horario_inicio', 'sabado_horario_fim',
        'domingo_horario_inicio', 'domingo_horario_fim', "somar_tempo_servicos", "intervalo_tempo_agendamento",
        "segunda", "terca", "quarta", "quinta","sexta","sabado", "domingo", "instagram", "slug", "token"
    ];
}
