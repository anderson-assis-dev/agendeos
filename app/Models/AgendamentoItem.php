<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendamentoItem extends Model
{
    use HasFactory;

    protected $table = 'agendamento_itens';
    protected $fillable = ['servicos_id', 'funcionarios_id', 'agendamento_id', 'quantidade', 'valor'];

    // Relacionamento com a tabela de serviços
    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'servicos_id');
    }

    // Relacionamento com a tabela de funcionários
    public function funcionario()
    {
        return $this->belongsTo(funcionarios::class, 'funcionarios_id');
    }

    // Relacionamento com a tabela de agendamentos
    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }

    // Adicione outros métodos ou relacionamentos conforme necessário
}
