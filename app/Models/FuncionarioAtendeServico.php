<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionarioAtendeServico extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;

    protected $table = 'funcionario_atende_servico';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'servico_id', 'funcionario_id', 'user_id'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function funcionario()
    {
        return $this->belongsTo(funcionarios::class, 'funcionario_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'servico_id');
    }
}
