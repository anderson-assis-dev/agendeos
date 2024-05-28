<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\whatsapp;
use App\Models\Empresas;
use App\Models\token;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll($id, $filter = null)
    {
        $user = Auth::user();
        $query = Clientes::where('user_id', $id)->where("ativo", 1);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('email', 'like', '%'.$filter.'%')
                    ->orWhere('cpf', 'like', '%'.$filter.'%')
                    ->orWhere('telefone_f', 'like', '%'.$filter.'%')
                    ->orWhere('celular_f', 'like', '%'.$filter.'%')
                    ->orWhere('telefone_j', 'like', '%'.$filter.'%')
                    ->orWhere('celular_j', 'like', '%'.$filter.'%')
                    ->orWhere('name', 'like', '%'.$filter.'%');
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate();

        return response()->json($result, 200);
    }

    public function getAllclientByType($type = "PF", $id, $filter = null)
    {


        $query = Clientes::where('tipo_cliente',$type)->where('user_id',$id)->where("ativo", 1);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('email_f', 'like', '%'.$filter.'%')
                    ->orWhere('cpf', 'like', '%'.$filter.'%')
                    ->orWhere('email_j', 'like', '%'.$filter.'%')
                    ->orWhere('nome_j', 'like', '%'.$filter.'%')
                    ->orWhere('telefone_j', 'like', '%'.$filter.'%')
                    ->orWhere('telefone_f', 'like', '%'.$filter.'%')
                    ->orWhere('nome_f', 'like', '%'.$filter.'%');
            });
        }

        $result =  $query->orderBy('id', 'desc')->paginate();

        return response()->json($result, 200);
    }
    public function viewToken()
    {
        return  response()->json(csrf_token());
    }
    public function store(Request $request)
    {
        //
        $user = Auth::user();
        $post = $request->all();

        if(isset($post['email_f']) and  !empty($post['email_f'])){
            $validar = Clientes::where('email_f', $post['email_f'])->first();
            if($validar and !empty($post['email_f'])){
                return response()->json(["erro" => true, "mensagem" => "Já possui um cliente com o e-mail informado!",], 200);
            }
        }

        if(isset($post['celular_f'])  and !empty($post['celular_f'])){
            $telefone  = "55".str_replace(array("(", ")", ".", "-", " "), "", $post['celular_f']);
        }
        elseif(isset($post['celular_rj']) and  !empty($post['celular_rj'])){
            $telefone  = "55".str_replace(array("(", ")", ".", "-", " "), "", $post['celular_rj']);
        }
        if(isset($post["data_aniversario"]) and !empty($post["data_aniversario"]))
        {
            $date = Carbon::createFromFormat('d/m/Y', $post["data_aniversario"]);
            $post["data_aniversario"] = $date->format('Y/m/d');
        }
        $clientes = Clientes::create( $post);
        $empresa = Empresas::query()->where("id", $user->empresa_id)->first();
        $vetor = [
            "messaging_product" => "whatsapp",
            "to" => $telefone,
            "type" => "template",
            "template" => [
                "name" => "bem_vindo",
                "language" => [
                    "code" => "pt_BR",
                    "policy" => "deterministic"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $post["nome_f"]
                            ],
                            [
                                "type" => "text",
                                "text" => $empresa->telefone ?? " "
                            ]
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $empresa->slug
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $zap = whatsapp::sendMessage($vetor, token::token());
        return response()->json([
            "erro" => false,
            "mensagem" => "Cliente cadastrado com  sucesso!",
            'id' => $clientes->id,
            'zap' => $zap
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function exibirCliente($id)
    {

        $registro = Clientes::where("id",$id)->first();
        if( $registro->data_aniversario){
            $registro->data_aniversario = date("d/m/Y", strtotime($registro->data_aniversario));
        }

        return response()->json(
            $registro
        , 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dados = $request->all();

        $cliente = Clientes::find($id);

        if (!$cliente) {
            return [
                "erro" => true,
                "mensagem" => "Cliente não encontrado!"
            ];
        }
        if(isset($dados["data_aniversario"]) and !empty($dados["data_aniversario"]))
        {
            $date = Carbon::createFromFormat('d/m/Y', $dados["data_aniversario"]);
            $dados["data_aniversario"] = $date->format('Y/m/d');
        }
        $cliente->fill($dados);
        $cliente->save();

        return [
            "erro" => false,
            "mensagem" => "Cliente editado com sucesso!"
        ];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clientes $clientes)
    {
        //
        Clientes::find($clientes)->delete();
        $response = [
            "erro" => false,
            "mensagem" => "Cliente apagado com sucesso!"
        ];
        return  $response;
    }
}
