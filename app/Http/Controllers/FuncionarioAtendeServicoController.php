<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioAtendeServico;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FuncionarioAtendeServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  string|null  $filter
     * @return \Illuminate\Http\Response
     */
    public function getAll($id, $filter = null)
    {
        $user = Auth::user();
        $query = FuncionarioAtendeServico::where('user_id', $user->id)->where("funcionario_id", $id);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->orWhereHas('servico', function ($query) use ($filter) {
                    $query->where('servico_id', 'like', '%' . $filter . '%')
                        ->orWhere('nome', 'like', '%' . $filter . '%');
                });
            });
        }
        $result = $query->with('servico')->orderBy('id', 'desc')->get();
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
        $user = Auth::user();
        FuncionarioAtendeServico::where('funcionario_id', $request->funcionario_id)->delete();
        $servicos = $request->input('servicos');
        foreach ($servicos as $servico){
            $data = [
                "servico_id" => $servico["id"],
                "funcionario_id" => $request->funcionario_id,
                "user_id" => $user->id
            ];
            FuncionarioAtendeServico::create($data);
        }

        return response()->json([
            "erro" => false,
            "mensagem" => "Cadastrado com sucesso",
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agendamento  $agendamento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $funcionarioAtendeServico = FuncionarioAtendeServico::where('id', $id)
            ->with('servico')->orderBy('id', 'desc')
            ->first();

        return response()->json($funcionarioAtendeServico, 200);
    }

    public function delete($id)
    {
        $funcionarioAtendeServico = FuncionarioAtendeServico::where('id', $id)->delete();

        return response()->json([
            "erro" => false,
            "mensagem" => "Excluido com sucesso",
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $user = Auth::user();
        $funcionarioAtendeServico =  FuncionarioAtendeServico::where('id', $id)->first();
        if(!$funcionarioAtendeServico){
           return [
               "erro" => true,
               "mensagem" => "Não encontrado"
           ];
        }
        $post = $request->all();
        $funcionarioAtendeServico->servico_id = $post["servico_id"];
        $funcionarioAtendeServico->funcionario_id = $post["funcionario_id"];
        $funcionarioAtendeServico->save();

        return [
            "erro" => false,
            "mensagem" => "Editado com sucesso!"
        ];

    }
}
