<?php

namespace App\Http\Controllers;

use App\Models\Veiculos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VeiculosController extends Controller
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

    public function getAll($id, $filter = null)
    {
        //
        $veiculos = DB::table('veiculos')->join('clientes', 'clientes.id', '=', 'veiculos.id_cliente')->where("veiculos.ativo", 1)->where("clientes.ativo", 1)->where('user_id',$id)->select('veiculos.*', 'clientes.nome_f', 'clientes.razao_social');


        if ($filter) {
            $veiculos->where(function ($q) use ($filter) {
                $q->where('placa', 'like', '%'.$filter.'%')
                    ->orWhere('marca', 'like', '%'.$filter.'%')
                    ->orWhere('razao_social', 'like', '%'.$filter.'%')
                    ->orWhere('nome_f', 'like', '%'.$filter.'%')
                    ->orWhere('modelo', 'like', '%'.$filter.'%');
            });
        }

              $result = $veiculos->orderBy('id', 'desc')->paginate();

        return response()->json($result, 200);
    }

    public function getallCliente($id)
    {
        //
        $veiculos = DB::table('veiculos')->join('clientes', 'clientes.id', '=', 'veiculos.id_cliente')->where("veiculos.ativo", 1)->where("clientes.ativo", 1)->where('id_cliente',$id)->select('veiculos.*', 'clientes.nome_f', 'clientes.razao_social')->get();
        return response()->json( $veiculos , 200);

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
        Veiculos::create( $post);
        return [
            "erro" => false,
            "mensagem" => "Veiculo cadastrado com  sucesso!"
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Veiculos  $veiculos
     * @return \Illuminate\Http\Response
     */
    public function show($veiculos)
    {
        //
        $registro = Veiculos::where('veiculos.id', $veiculos)->leftJoin('clientes', 'clientes.id', '=', 'veiculos.id_cliente')->select('veiculos.*', 'clientes.nome_f', 'clientes.razao_social')->first();

        return response()->json(
            $registro
        , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Veiculos  $veiculos
     * @return \Illuminate\Http\Response
     */
    public function edit(Veiculos $veiculos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Veiculos  $veiculos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $veiculos)
    {
        //

        $dados = $request->all();

        $veiculo = Veiculos::find($veiculos);

        if (!$veiculo) {
            return [
                "erro" => true,
                "mensagem" => "veiculo não encontrado!"
            ];
        }

        $veiculo->fill($dados);
        $veiculo->save();

        return [
            "erro" => false,
            "mensagem" => "veiculo editado com sucesso!"
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Veiculos  $veiculos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Veiculos $veiculos)
    {
        //
        Veiculos::find($veiculos)->delete();
        $response = [
            "erro" => false,
            "mensagem" => "Veiculo apagado com sucesso!"
        ];
    }
}
