<?php

namespace App\Http\Controllers;

use App\Models\Servicos;
use Illuminate\Http\Request;

class ServicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll($id, $filter = null)
    {

        $query =   Servicos::where('user_id',$id)->where("ativo", 1);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('id', 'like', '%'.$filter.'%')
                    ->orWhere('nome', 'like', '%'.$filter.'%')
                    ->orWhere('tempo_estimado', 'like', '%'.$filter.'%')
                    ->orWhere('valor', 'like', '%'.$filter.'%');
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate();

        return response()->json($result, 200);

    }
    public function terminoPrevisao($horario, $id)
    {
        //
        $servicos = Servicos::find($id);
        $previsao = gmdate('H:i:s', strtotime( $horario ) + strtotime( $servicos->tempo_estimado ) );

        return response()->json(
            $previsao
        , 200);

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $post = $request->all();
        Servicos::create( $post);
        return [
            "erro" => false,
            "mensagem" => "Serviço cadastrado com  sucesso!"
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registro = Servicos::where('id',$id)->first();
        return response()->json(
            $registro
        , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */
    public function edit(Servicos $servicos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $dados = $request->all();
        Servicos::find($id)->update($dados);
        return [
            "erro" => false,
            "mensagem" => "Serviço editado com  sucesso!"
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Servicos::find($id)->delete();
        $response = [
            "erro" => false,
            "mensagem" => "Serviço apagado com sucesso!"
        ];
        return  $response;
    }
}
