<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresas;
use App\Models\fluxo_caixa;
use App\Models\funcionarios;
use App\Models\OrdemServicos;
use App\Models\Produtos;
use App\Models\Servicos;
use App\Models\UsuarioAssinatura;
use Illuminate\Http\Request;
use App\Models\Usuarios;
class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['Usuarios'] = Usuarios::all();

        return $data;
    }


    public function store(Request $request)
    {
        $dados = $request->all();
        $user = Usuarios::where("email", $dados["email"])->first();
        if(!$user){
            $user = Usuarios::create($dados);
        }
        $assinatura = UsuarioAssinatura::where("user_id", $user->id)->first();
        if(!$assinatura){
            $assinatura = UsuarioAssinatura::create(
                [
                    "plano_id" => 1,
                    "user_id" => $user->id, "ativo" => 1,
                    "teste" => 1,
                    "data_assinatura" => date("Y-m-d")
                ]
            );
        }


        return [
            "erro" => false,
            "mensagem" => "Usuário cadastrado com  sucesso!"
        ];
    }
    public function show($id)
    {
        $registro = Usuarios::find($id);
        return $registro;
    }


    public function atualizarPerfil(Request $request, $id)
    {
        $dados = $request->all();
        Usuarios::find($id)->update($dados);
        return [
            "erro" => false,
            "mensagem" => "Usuário editado com  sucesso!"
        ];
    }

    public function delete($id)
    {
        Usuarios::where('funcionario_id',$id)->delete();
        $response = [
            "erro" => false,
            "mensagem" => "Usuário desativado com sucesso!"
        ];
        return response()->json($response, 200);
    }
    public function exlcuirTodosDados($id){
         $usuario =  Usuarios::query()->where("id", $id)->first();
         if($usuario){
             Empresas::query()->where("id", $usuario->empresa_id)->delete();
             $usuario->delete();
             Servicos::query()->where("user_id", $id)->delete();
             funcionarios::query()->where("user_id", $id)->delete();
             OrdemServicos::query()->where("user_id", $id)->delete();
             Produtos::query()->where("user_id", $id)->delete();
             fluxo_caixa::query()->where("user_id", $id)->delete();
         }
        $response = [
            "erro" => false,
            "mensagem" => "Conta excluida com sucesso!"
        ];
        return response()->json($response, 200);
    }
}
