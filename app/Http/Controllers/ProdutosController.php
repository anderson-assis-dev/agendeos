<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll($id, $filter = null)
    {

        $query =   Produtos::where('user_id',$id)->select("id", "nome", "preco as valor", "descricao", "user_id", "estoque");

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('id', 'like', '%'.$filter.'%')
                    ->orWhere('nome', 'like', '%'.$filter.'%')
                    ->orWhere('preco', 'like', '%'.$filter.'%')
                    ->orWhere('descricao', 'like', '%'.$filter.'%');
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate();

        return response()->json($result, 200);

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Produtos::create( $request->all());
        return response()->json(["erro" => false, "mensagem" => "Produto cadastrado com  sucesso!"], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registro = Produtos::where('id',$id)->select("id", "nome", "preco as valor", "descricao", "user_id", "estoque")->first();
        return response()->json($registro, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        Produtos::find($id)->update($request->all());
        return response()->json(["erro" => false, "mensagem" => "Produto editado com  sucesso!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Servicos  $servicos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Produtos::find($id)->delete();
        return response()->json(["erro" => false, "mensagem" => "Produto apagado com  sucesso!"], 200);
    }
}
