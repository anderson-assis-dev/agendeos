<?php

namespace App\Http\Controllers;

use App\Models\funcionarios;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Models\whatsapp;
use App\Models\token;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class FuncionariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll($id, $filter = null)
    {
        $query = funcionarios::where('user_id', $id)->where("ativo", 1);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('nome', 'like', '%'.$filter.'%')
                    ->orWhere('cpf', 'like', '%'.$filter.'%')
                    ->orWhere('telefone', 'like', '%'.$filter.'%');
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate();
        foreach ($result as $item){
            $item->id_conta_acesso = User::query()->where('funcionario_id', $item->id)->select('id', 'name', 'email')->first();
        }
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
        //
        $user = Auth::user();

        $post = $request->all();
        $telefone = "";
        if($post['celular']){
            $telefone  = "55".str_replace(array("(", ")", ".", "-", " "), "", $post['celular']);
        }

        $vetor = array(
            "messaging_product" => "whatsapp",
            "to" => $telefone,
            "type" => 'template',
            "template" => array(
                "name" => "bem_vindo",
                "language" => array(
                    "code" => "pt_BR"
                )
            )
        );
        whatsapp::sendMessage($vetor, token::token());
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);

            $url = URL::asset('uploads/' . $fileName);
            $post['foto'] = $url;
        }
        $clientes = funcionarios::create( $post);
        return response()->json([
            "erro" => false,
            "mensagem" => "Funcionário cadastrado com  sucesso!",
            'id' => $clientes->id
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\funcionarios  $funcionarios
     * @return \Illuminate\Http\Response
     */
    public function show($funcionarios)
    {
        //
        $registro = funcionarios::where('id', $funcionarios)->first();
        return response()->json(
            $registro
        , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\funcionarios  $funcionarios
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\funcionarios  $funcionarios
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dados = $request->all();
        $funcionarios = funcionarios::find($id);

        if (!$funcionarios) {
            return response()->json(["erro" => true, "mensagem" => "funcionarios não encontrado!"], 404);
        }
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);

            $url = URL::asset('uploads/' . $fileName);
            $dados['foto'] = $url;
        }
        $funcionarios->fill($dados);
        $funcionarios->save();
        $this->atualizarSenhaAcessoFuncionario($funcionarios);

        return response()->json(["erro" => false, "mensagem" => "funcionarios editado com sucesso!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\funcionarios  $funcionarios
     * @return \Illuminate\Http\Response
     */
    public function destroy(funcionarios $funcionarios)
    {
        //
        funcionarios::find($funcionarios)->delete();
        return response()->json(
            [
                "erro" => false,
                "mensagem" => "Funcionário deletado com  sucesso!"
            ]
        , 200);
    }
    private function atualizarSenhaAcessoFuncionario(funcionarios $funcionarios): void
    {
        $usuario = User::query()->where("funcionario_id", $funcionarios->id)->first();
        if($usuario){
            $usuario->password = bcrypt( str_replace(array(".", "-"), "",$funcionarios->cpf));
        }
    }
}
