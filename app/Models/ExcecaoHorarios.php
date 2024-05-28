<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcecaoHorarios extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;

    protected $table = 'excecao_horarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'horario', 'funcionario_id', 'user_id'
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
}
