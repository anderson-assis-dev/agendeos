<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordem_servico_servico extends Model
{
    protected $fillable = ['os_id', 'id_servico', 'valor',"quantidade"];
}
