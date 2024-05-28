<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class token extends Model
{
    use HasFactory;
    protected $table = 'tokens';
    protected $fillable = ['tipo', 'token'];
    public static function token()
    {
        $token = token::where('tipo', 'whatsapp')->first();

        return $token->token;
    }
}
