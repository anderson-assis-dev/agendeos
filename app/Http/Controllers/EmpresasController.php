<?php

namespace App\Http\Controllers;

use App\Models\Empresas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class EmpresasController extends Controller
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
        $query = Empresas::where('situacao', 1);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('cnpj', 'like', '%'.$filter.'%')
                    ->orWhere('razao_social', 'like', '%'.$filter.'%')
                    ->orWhere('telefone', 'like', '%'.$filter.'%');
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
        $post["slug"] = trim(strtolower($post["razao_social"]));
        $Empresas = Empresas::create( $post);
        return response()->json([
            "erro" => false,
            "mensagem" => "Empresas cadastrada com  sucesso!",
            'id' => $Empresas->id
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Empresas  $empresas
     * @return \Illuminate\Http\Response
     */
    public function show(Empresas $empresas)
    {
        //
        if($empresas->somar_tempo_servicos){
            $empresas->somar_tempo_servicos = [
                "id" => $empresas->somar_tempo_servicos,
                "nome" => "Somar Tempo dos Serviços"
            ];
        }
        else {
            $empresas->somar_tempo_servicos = [
                "id" => $empresas->somar_tempo_servicos,
                "nome" => "Calcular com base em horas fixas"
            ];
        }
        return response()->json(
            $empresas
        , 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empresas  $Empresas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Empresas)
    {
        $dados = $request->all();

        $Empresas = Empresas::find($Empresas);
        if(isset($dados["somar_tempo_servicos"])){
            if (is_numeric($dados["somar_tempo_servicos"])) {
                $dados["somar_tempo_servicos"] = $dados["somar_tempo_servicos"];
            } elseif (is_array($dados["somar_tempo_servicos"])) {
                $dados["somar_tempo_servicos"] = $dados["somar_tempo_servicos"]["id"];
            } else {
                $dados["somar_tempo_servicos"] = 1;
            }
        }

        if (!$Empresas) {
            return [
                "erro" => true,
                "mensagem" => "Empresas não encontrado!"
            ];
        }
        $Empresas->fill($dados);
        $Empresas->save();

        return [
            "erro" => false,
            "mensagem" => "Empresas editado com sucesso!"
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Empresas  $Empresas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empresas $Empresas)
    {
        //
        Empresas::find($Empresas)->delete();
        $response = [
            "erro" => false,
            "mensagem" => "Empresa apagada com sucesso!"
        ];
        return  $response;
    }
}
