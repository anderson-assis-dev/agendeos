<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServicos extends Model
{
    use HasFactory;

    protected $fillable = ['id_cliente', 'id_servico', 'id_veiculo', 'remarketing', 'id_funcionario', 'id_forma_pagamento', 'situacao', 'inicio_os', 'previsao_os', 'observacoes', 'user_id'];

    // Define the relationships

    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'id_servico');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }

    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class, 'id_forma_pagamento');
    }

    public function situacao()
    {
        return $this->belongsTo(Situacao::class, 'situacao');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionarios::class, 'id_funcionario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'user_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculos::class, 'id_veiculo');
    }

    public function ordemServicoServico()
    {
        return $this->hasMany(OrdemServicoServico::class, 'ordem_servico_id');
    }
}
