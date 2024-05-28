<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CustomEnsureFrontendRequestsAreStateful
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $allowedRoutesWithoutToken = ['login', 'retornoPagamento', 'planosGetAll', 'agendamentosGetAll', 'planosShow', 'agendamentoShow','getHorariosDisponiveis',
            'empresacriar', 'empresaatualizar','token_senha' , 'adicionarAgendamento' ,'cadastrar', 'password.reset.resetPassword', 'segmentoAll', 'segmentoShow', 'trocar_senha', 'criarAssinatura'];
        if($request->header('X-Authorization')){
            $token = $request->bearerToken() ?: $request->header('X-Authorization');
        }
        else{
            $token = $request->bearerToken() ?: $request->header('Authorization');
        }

        // Verifica se a rota está na lista de rotas permitidas sem token
        if (!in_array($request->route()->getName(), $allowedRoutesWithoutToken)) {
            // Verifica se o token está presente no cabeçalho Authorization ou X-Authorization

            if (!$token) {
                return response()->json(['error' => true, 'message' => 'Token de autenticação ausente ou inválido.'], 401);
            }
        }

        // Verifica se o token expirou
        if ($token) {
            $tokenExpiration = Auth::guard('web')->user()->token->expires_at ?? null;
            if ($tokenExpiration && now()->gt($tokenExpiration)) {
                return response()->json(['error' => true, 'message' => 'Token expirado.'], 401);
            }
        }
        return $next($request);
    }
}
