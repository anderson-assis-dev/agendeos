<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedRoutesWithoutToken = ['login', 'retornoPagamento', 'planosGetAll', 'agendamentosGetAll', 'planosShow', 'agendamentoShow','getHorariosDisponiveis',
            'empresacriar', 'empresaatualizar','token_senha' ,'cadastrar', 'adicionarAgendamento' ,'password.reset.resetPassword', 'segmentoAll', 'segmentoShow', 'trocar_senha', 'criarAssinatura'];
        if($request->header('X-Authorization')){
            $token = $request->bearerToken() ?: $request->header('X-Authorization');
        }
        else{
            $token = $request->bearerToken() ?: $request->header('Authorization');
        }
        if (!in_array($request->route()->getName(), $allowedRoutesWithoutToken)) {
            if (!$token) {
                return response()->json(['error' => true, 'message' => 'Token de autenticação ausente ou inválido.'], 401);
            }
        }
        if ($token) {
            $tokenExpiration = Auth::guard('web')->user()->token->expires_at ?? null;
            if ($tokenExpiration && now()->gt($tokenExpiration)) {
                return response()->json(['error' => true, 'message' => 'Token expirado.'], 401);
            }
        }
        return $next($request);
    }
}
