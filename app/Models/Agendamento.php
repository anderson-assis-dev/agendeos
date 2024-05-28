<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamento';
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'observacao',
        'forma_pagamento_id',
        'data_agendamento',
        'hora_agendamento',
        'funcionario_id',
        'user_id',
        'situacao_id',
        'clientes_id',
    ];

    // Relacionamento com a tabela de formas de pagamento
    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }
    public function agendamentoItens()
    {
        return $this->hasMany(AgendamentoItem::class, 'agendamento_id');
    }
    public function usuarios()
    {
        return $this->belongsTo(Usuarios::class, 'user_id');
    }

    // Relacionamento com a tabela de situações
    public function situacao()
    {
        return $this->belongsTo(Situacao::class, 'situacao_id', 'referencia_id');
    }
    public function funcionario()
    {
        return $this->belongsTo(funcionarios::class, 'funcionario_id');
    }
    // Relacionamento com a tabela de clientes
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'clientes_id');
    }

    // Adicione outros métodos ou relacionamentos conforme necessário

    // Acesso ao timestamp customizado
    protected $dates = ['data_agendamento'];

    // Adicione outros métodos ou propriedades conforme necessário
}
