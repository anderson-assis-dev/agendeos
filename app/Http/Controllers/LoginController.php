<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\funcionarios;
use App\Models\UsuarioAssinatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\fluxo_caixa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;
            $vetor  = User::leftJoin('empresas', 'empresas.id', '=', 'users.empresa_id')->leftJoin('planos', 'planos.id', '=', 'empresas.plano_id')
            ->where('users.id',Auth::id())->select(['users.*', 'empresas.razao_social', 'empresas.plano_id', 'empresas.segmento_id', 'empresas.situacao',
                    'planos.recursos', 'empresas.slug'])->first();
            $empresa = Empresas::query()->where("id", $vetor->empresa_id)->first();
            $data = $vetor;
            $data["link_agendamento"] = "https://agendos.com.br/agendamento/".$vetor->slug;
            $data['recursos'] = json_decode( $data['recursos'] , true);
            $data["horarios_funcionamento"] = $this->formatarHorariosFuncionamento($empresa);
            $data["totalClientes"] = $this->totalClientes();
            $data["totalFuncionarios"] = $this->totalFuncionarios();
            $data["comissaoPorFuncionario"] = $this->comissaoFuncionarios();
            $data["totalAgendamentos"] = $this->totalAgendamentos();
            $data["faturamento"] = $this->faturamento();
            $data['receita'] = fluxo_caixa::getAllMoney();
            $data['token_expiracao'] = now()->addMinutes(config('sanctum.expiration'));
            $data["assinatura"] = $this->assinatura();

            $data['token'] =  $token ;
            $data['atualizacao'] =  1 ;
            return response()->json($data, 200);
        } else {
            return response()->json(["error" => "true", "msg" => "Dados inválidos"],401);
        }



        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function assinatura()
    {
        $assinatura =  UsuarioAssinatura::query()->where("user_id", Auth::id())->first();
        $inicioTeste = Carbon::parse($assinatura->inicio_teste);
        $fimTeste = Carbon::parse($assinatura->fim_teste);
        $data = [];
        $diasRestantes = $fimTeste->diffInDays($inicioTeste);
        $assinaturaTeste = [
            "ativo" => $assinatura->teste,
            "inicio_teste" => $assinatura->inicio_teste,
            "diasRestantes" => $diasRestantes,
            "fim_teste" => $assinatura->fim_teste
        ];
        $inicioPlano = Carbon::parse($assinatura->data_assinatura);
        $fimPlano = Carbon::parse($assinatura->data_renovacao);
        $diasRestantesPlano = $fimPlano->diffInDays($inicioPlano);
        $assinaturaPlano = [
            "ativo" => $assinatura->ativo,
            "data_pagamento" => $assinatura->data_pagamento,
            "inicio_plano" => $assinatura->data_assinatura,
            "diasRestantes" => $diasRestantesPlano,
            "fim_plano" => $assinatura->data_renovacao
        ];

        return [
            "teste" => $assinaturaTeste,
            "plano" => $assinaturaPlano
        ];

    }
    public function formatarHorariosFuncionamento($empresas){
        $data = [
            "domingo" => [
                "disponivel" => $empresas->domingo,
                "horario_fim" => $empresas->domingo_horario_fim,
                "horario_inicio" => $empresas->domingo_horario_inicio
            ],
            "segunda" => [
                "disponivel" => $empresas->segunda,
                "horario_fim" => $empresas->segunda_horario_fim,
                "horario_inicio" => $empresas->segunda_horario_inicio
            ],
            "terca" => [
                "disponivel" => $empresas->terca,
                "horario_fim" => $empresas->terca_horario_fim,
                "horario_inicio" => $empresas->terca_horario_inicio
            ],
            "quarta" => [
                "disponivel" => $empresas->quarta,
                "horario_fim" => $empresas->quarta_horario_fim,
                "horario_inicio" => $empresas->quarta_horario_inicio
            ],
            "quinta" => [
                "disponivel" => $empresas->quinta,
                "horario_fim" => $empresas->quinta_horario_fim,
                "horario_inicio" => $empresas->quinta_horario_inicio
            ],
            "sexta" => [
                "disponivel" => $empresas->sexta,
                "horario_fim" => $empresas->sexta_horario_fim,
                "horario_inicio" => $empresas->sexta_horario_inicio
            ],
            "sabado" => [
                "disponivel" => $empresas->sabado,
                "horario_fim" => $empresas->sabado_horario_fim,
                "horario_inicio" => $empresas->sabado_horario_inicio
            ],
        ];
        return $data;
    }
    public function totalClientes()
    {
        return Clientes::query()->where("user_id", Auth::id())->count();
    }
    public function totalFuncionarios()
    {
        return funcionarios::query()->where("user_id", Auth::id())->count();
    }
    public function totalAgendamentos()
    {
        return Agendamento::query()->where("user_id", Auth::id())->where("data_agendamento", date("Y-m-d"))->count();
    }
    public function comissaoFuncionarios()
    {
        $inicio  = date("Y-m-01");
        $fim     = date("Y-m-31");
        return
            DB::table('funcionarios')->select('funcionarios.nome', 'funcionarios.id', 'funcionarios.foto', DB::raw('SUM(servicos.valor) as receita_total'),
                DB::raw('SUM(CASE WHEN ordem_servicos.situacao = 1 THEN servicos.valor * funcionarios.comissao / 100 ELSE 0 END) as comissao_funcionario'))
            ->join('ordem_servicos', 'funcionarios.id', '=', 'ordem_servicos.id_funcionario')
            ->join('ordem_servico_servicos', 'ordem_servicos.id', '=', 'ordem_servico_servicos.os_id')
            ->join('servicos', 'ordem_servico_servicos.id_servico', '=', 'servicos.id')
            ->where('ordem_servicos.user_id', '=', Auth::id())
            ->where('ordem_servicos.situacao', '=', 1)
            ->whereBetween('ordem_servicos.created_at', ["$inicio 00:00:00", "$fim 23:59:59"])
            ->groupBy('funcionarios.nome', 'funcionarios.id', 'funcionarios.foto')
            ->get();
    }
    public function faturamento()
    {
        $resultados = DB::table('ordem_servicos')
            ->join('ordem_servico_servicos', 'ordem_servicos.id', '=', 'ordem_servico_servicos.os_id')
            ->join('servicos', 'ordem_servico_servicos.id_servico', '=', 'servicos.id')
            ->selectRaw('YEAR(ordem_servicos.created_at) AS ano, MONTH(ordem_servicos.created_at) AS mes, SUM(servicos.valor) AS valor_total')
            ->where('ordem_servicos.user_id', Auth::id())
            ->where('ordem_servicos.situacao', 1)
            ->whereYear('ordem_servicos.created_at', date("Y"))
            ->groupBy('ano', 'mes')
            ->orderBy('ano', 'asc')
            ->orderBy('mes', 'asc')
            ->get();
        $resultadosFormatados = [];
        $mesesDoAno = array(
            'janeiro', 'fevereiro', 'marco', 'abril', 'maio', 'junho',
            'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
        );
        foreach ($resultados as $resultado) {
            $ano = $resultado->ano;
            $mes = Carbon::createFromFormat('!m', $resultado->mes)->locale('pt_BR')->monthName;
            $valorTotal = $resultado->valor_total;
            if (!isset($resultadosFormatados[$ano])) {
                $resultadosFormatados[$ano] = array_fill_keys($mesesDoAno, 0);
            }
            $resultadosFormatados[$ano][$mes] = $valorTotal;
        }
        return isset($resultadosFormatados[date("Y")]) ? $resultadosFormatados[date("Y")] : $resultadosFormatados ;
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

}
