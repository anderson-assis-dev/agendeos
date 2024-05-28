<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculos extends Model
{
    use HasFactory;
    protected $fillable = [
        'placa', 'marca', 'modelo', 'cor', 'id_cliente','ativo', 'observacoes'
    ];
    protected $table = 'veiculos';
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente');
    }
}
