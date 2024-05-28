<?php

namespace App\Http\Controllers;

use App\Models\Segmento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class SegmentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll($filter = null)
    {
        //
        $user = Auth::user();

        $query = Segmento::where('situacao', 1);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('segmento', 'like', '%'.$filter.'%')
                    ->orWhere('id', 'like', '%'.$filter.'%')
                    ->orWhere('situacao', 'like', '%'.$filter.'%');
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate();

        return response()->json($result, 200);

    }

    public function store(Request $request)
    {
        //
        $user = Auth::user();

        $post = $request->all();

        $Segmento = Segmento::create( $post);
        return response()->json([
            "erro" => false,
            "mensagem" => "Plano cadastrada com  sucesso!",
            'id' => $Segmento->id
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Segmento  $Segmento
     * @return \Illuminate\Http\Response
     */
    public function show(Segmento $Segmento)
    {
        //
        $registro = Segmento::where('id',$Segmento)->first();
        return response()->json(
            $Segmento
        , 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Segmento  $Segmento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Segmento)
    {
        $dados = $request->all();
        $fluxo_caixa = Segmento::find($Segmento);

        if (!$fluxo_caixa) {
            return [
                "erro" => true,
                "mensagem" => "Segmento não encontrado!"
            ];
        }

        $fluxo_caixa->fill($dados);
        $fluxo_caixa->save();
        return [
            "erro" => false,
            "mensagem" => "Segmento editada com  sucesso!"
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Segmento  $Segmento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Segmento $Segmento)
    {
        //
        Segmento::find($Segmento)->delete();
        $response = [
            "erro" => false,
            "mensagem" => "Segmento apagada com sucesso!"
        ];
        return  $response;
    }
}
