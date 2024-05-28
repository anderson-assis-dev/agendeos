<?php

namespace App\Http\Controllers;

use App\Models\Empresas;
use App\Models\token;
use App\Models\whatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\PasswordResetMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function showResetForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (!$user) {

            return response()->json(["error" => "true", "msg" => "O e-mail informado não encontra-se em nossa base de dados"],200);
        }

        $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);
        $empresaVinculada = Empresas::query()->where("id", $user->empresa_id)->first();
        if(!$empresaVinculada){
            return response()->json(["error" => true, "message" => "Usuário nåo possui empresa vinculada"],401);
        }
        if(empty($empresaVinculada->telefone)){
            return response()->json(["error" => true, "message" => "Usuário nåo possui Telefone Cadastro para envio do Token. Contate o Administrador!"],401);
        }
        $this->enviarToken($user, $token, $empresaVinculada);
        return response()->json(["error" => false, "message" => "Token enviado para Whatsapp", "token" => $token],200);
    }
    public function enviarToken(User  $user, $token, Empresas $empresas)
    {
        $empresas->telefone = str_replace(array("(", ")", ".", "-"), "", $empresas->telefone);
        $mensagem = array(
            "messaging_product" => "whatsapp",
            "to" => "55".$empresas->telefone,
            "type" => "template",
            "template" => array(
                "name" => "token_senha",
                "language" => array(
                    "code" => "pt_BR",
                    "policy" => "deterministic"
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $user->name
                            ),
                            array(
                                "type" => "text",
                                "text" => date("d/m/Y H:i")
                            ),
                            array(
                                "type" => "text",
                                "text" => $token
                            )
                        )
                    )
                )
            )
        );
        $data = whatsapp::sendMessage($mensagem, token::token());
        return  $data;
    }
    public function showResetPasswordForm(Request $request)
    {
        return view('auth.passwords.reset', ['token' => $request->route('token')]);
    }

    public function resetPassword(Request $request)
    {
        if(empty($request->password) or empty($request->password_confirmation) or empty($request->token) or empty($request->email))
        {
            return response()->json(["error" => "true","msg" => "Todos os dados precisam ser preenchidos"],200);
        }
        $token = PasswordReset::where('email', $request->email)->where('token', $request->token)->first();
        if (!$token) {

            return response()->json(["error" => "true", "msg" => "Token inválido"],401);
        }
        else{
            if($request->password <> $request->password_confirmation)
            {
                return response()->json(["error" => "true","msg" => "As senhas precisams er iguais"],200);
            }
            $user = User::where('email', $request->email)->first();
            $user->password = bcrypt($request->password);
            $user->remember_token = Str::random(60);
            $user->save();
            return response()->json(["error" => "false","msg" => "Senha alterada com sucesso"],200);
        }



    }
}
