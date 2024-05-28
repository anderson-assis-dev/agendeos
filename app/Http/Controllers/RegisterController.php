<?php

namespace App\Http\Controllers;
use App\Models\Empresas;
use App\Models\UsuarioAssinatura;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
// use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\fluxo_caixa;
class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function criarEmpresa(User $user): Empresas
    {
        $empresas = Empresas::create(["razao_social" => $user->name]);
        return $empresas;
    }

    public function vincularEmpresaUsuario( Empresas $empresas, User $user): void
    {
        $user->empresa_id = $empresas->id;
        $user->save();
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (User::where('email', $data['email'])->count())
        {
            return response()->json(["error" => "true", "msg" => "O e-mail informado está associado a outra conta"],500);
        }
        else
        {
            $data['password'] = bcrypt( $data['password']);
            $user = User::where("email", $data["email"])->first();
            if(!$user){
                $user = User::create($data);
            }
            $assinatura = UsuarioAssinatura::where("user_id", $user->id)->first();
            if(!$assinatura){
                $dataAtual = Carbon::now();
                $dataFutura = $dataAtual->addDays(14);
                $dataFuturaFormatada = $dataFutura->format('Y-m-d');
                $assinatura = UsuarioAssinatura::create(
                    [
                        "plano_id" => 1,
                        "user_id" => $user->id,
                        "ativo" => 0,
                        "teste" => 1,
                        "inicio_teste" => date("Y-m-d"),
                        "fim_teste" => $dataFuturaFormatada,
                    ]
                );
            }
            $empresa = $this->criarEmpresa($user);
            $this->vincularEmpresaUsuario($empresa, $user);
            Auth::attempt(['email' => $request->email, 'password' => $request->password]);
            $vetor  = User::leftJoin('empresas', 'empresas.id', '=', 'users.empresa_id')->leftJoin('planos', 'planos.id', '=', 'empresas.plano_id')
                ->where('users.id',Auth::id())
                ->select(['users.*', 'empresas.razao_social', 'empresas.plano_id', 'empresas.segmento_id', 'empresas.situacao', 'planos.recursos'])
                ->first();
            $data = $vetor;
            $data['recursos'] = json_decode( $data['recursos'] , true);
            $data['receita'] = fluxo_caixa::getAllMoney();
            $data['token_expiracao'] = now()->addMinutes(config('sanctum.expiration'));
            $data['token'] = $user->createToken('api-token')->plainTextToken;
            $data["assinatura"] = $assinatura;
            return response()->json($vetor, 200);

        }

    }
}
