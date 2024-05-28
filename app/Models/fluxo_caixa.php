<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class fluxo_caixa extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'valor', 'nome','pagamento_id','situacao' , 'quantidade' ,'os_id', 'data', 'produto_id', 'cliente_id', 'desconto', 'tipo_id'];


    public static function getallMoney()
    {
        $startOfDay = Carbon::parse(date("Y-m-d"))->startOfDay();
        $endOfDay = Carbon::parse(date("Y-m-d"))->endOfDay();
        $receita = fluxo_caixa::where('tipo_id', 1)->where('user_id', Auth::id())->where('situacao', '<>', 6)
            ->whereBetween('data', [$startOfDay, $endOfDay])->sum('valor');
        $desconto = fluxo_caixa::where('tipo_id', 1)->where('user_id', Auth::id())->where('situacao', '<>', 6)
            ->whereBetween('data', [$startOfDay, $endOfDay])->sum('desconto');
        $despesa = fluxo_caixa::where('tipo_id', 2)->where('user_id', Auth::id())->where('situacao', '<>', 6)->whereBetween('data', [$startOfDay, $endOfDay])->sum('valor');
        return ($receita-$desconto) - $despesa;
    }
}
