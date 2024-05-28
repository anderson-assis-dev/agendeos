<?php

//use Illuminate\Http\Request;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ExcecaoHorariosController;
use App\Http\Controllers\FuncionarioAtendeServicoController;

use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\PlanosController;
use App\Http\Controllers\SegmentoController;
use App\Http\Controllers\FuncionariosController;
use App\Http\Controllers\VeiculosController;
use App\Http\Controllers\ServicosController;
use App\Http\Controllers\OrdemServicosController;
use App\Http\Controllers\FluxoCaixaController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('agendamento/getEstabelecimento/{slug}', [OrdemServicosController::class, 'getEstabelecimento'])->name('getEstabelecimento');
    Route::permanentRedirect('/', 'https://site.agendos.com.br/');
    Route::get('clientes/getall{id}/{filter?}', [ClientesController::class, 'getall']);
    Route::get('clientes/getAllclientByType/{type}/{id}/{filter?}', [ClientesController::class, 'getAllclientByType']);
    Route::get('clientes/show/{id}', [ClientesController::class, 'exibirCliente'])->name('exibirCliente');
    Route::post('clientes/insert', [ClientesController::class, 'store']);
    Route::put('clientes/update/{id}',[ClientesController::class, 'update']);
    Route::delete('clientes/destroy{clientes}', [ClientesController::class, 'destroy']);

    Route::get('empresas/getall/{filter?}', [EmpresasController::class, 'getall']);
    Route::get('empresas/show/{empresas}', [EmpresasController::class, 'show']);

    Route::delete('empresas/destroy{empresas}', [EmpresasController::class, 'destroy']);

    Route::get('funcionarios/getall/{id}/{filter?}', [FuncionariosController::class, 'getall']);

    Route::get('funcionarios/show/{funcionario}', [FuncionariosController::class, 'show']);
    Route::post('funcionarios/insert', [FuncionariosController::class, 'store']);
    Route::put('funcionarios/update/{funcionario}',[FuncionariosController::class, 'update']);
    Route::delete('funcionarios/destroy{funcionario}', [FuncionariosController::class, 'destroy']);

    Route::get('os/getall/{id}/{inicio}/{fim?}/{funcionario_id?}', [OrdemServicosController::class, 'getall']);
    Route::get('relatorio/{id}/{inicio?}/{fim?}', [OrdemServicosController::class, 'relatorio']);
    Route::get('os/pdf/{id}/{inicio}/{fim?}/{funcionario_id?}', [OrdemServicosController::class, 'gerarPDF']);
    Route::get('os/show/{os}', [OrdemServicosController::class, 'show']);
    Route::get('os/getServicosOs/{os}', [OrdemServicosController::class, 'getServicosOs']);
    Route::post('os/insert', [OrdemServicosController::class, 'store']);
    Route::put('os/update/{os}',[OrdemServicosController::class, 'update']);
    Route::delete('os/destroy{os}', [OrdemServicosController::class, 'destroy']);


    Route::post('/upload', [UploadController::class, 'upload']);
    Route::get('fluxo_caixa/getall/{id}/{inicio}/{fim}/{filter?}', [FluxoCaixaController::class, 'getall']);
    Route::get('fluxo_caixa/show/{fluxo_caixa}', [FluxoCaixaController::class, 'show']);
    Route::get('fluxo_caixa/saldodia', [FluxoCaixaController::class, 'saldoDia']);
    Route::post('fluxo_caixa/insert', [FluxoCaixaController::class, 'store']);
    Route::put('fluxo_caixa/update/{fluxo_caixa}',[FluxoCaixaController::class, 'update']);
    Route::delete('fluxo_caixa/destroy{fluxo_caixa}', [FluxoCaixaController::class, 'destroy']);

    Route::get('veiculos/getall/{id}/{filter?}', [VeiculosController::class, 'getall']);
    Route::get('veiculos/cliente/{id}', [VeiculosController::class, 'getallCliente']);
    Route::get('veiculos/show/{veiculos}', [VeiculosController::class, 'show']);
    Route::post('veiculos/insert', [VeiculosController::class, 'store']);
    Route::put('veiculos/update/{veiculos}',[VeiculosController::class, 'update']);
    Route::delete('veiculos/destroy{veiculos}', [VeiculosController::class, 'destroy']);

    Route::get('servicos/getall/{id}/{filter?}', [ServicosController::class, 'getall']);
    Route::get('servicos/show/{servicos}', [ServicosController::class, 'show']);
    Route::get('servicos/terminoPrevisao/{horario}/{servicos}', [ServicosController::class, 'terminoPrevisao']);
    Route::post('servicos/insert', [ServicosController::class, 'store']);
    Route::put('servicos/update/{servicos}',[ServicosController::class, 'update']);
    Route::delete('servicos/destroy{servicos}', [ServicosController::class, 'destroy']);

    Route::get('produtos/getall/{id}/{filter?}', [ProdutosController::class, 'getall']);
    Route::get('produtos/show/{produtos}', [ProdutosController::class, 'show']);
    Route::post('produtos/insert', [ProdutosController::class, 'store']);
    Route::put('produtos/update/{produtos}',[ProdutosController::class, 'update']);
    Route::delete('produtos/destroy{produtos}', [ProdutosController::class, 'destroy']);

    Route::delete('usuario/destroy/{usuario}', [UsuariosController::class, 'delete']);
    Route::delete('usuario/cancelar_conta/{usuario}', [UsuariosController::class, 'exlcuirTodosDados']);
    Route::put('usuario/update/{usuario}',[UsuariosController::class, 'atualizarPerfil'])->name('atualizarPerfil');
    Route::post('/agendamentos/{agendamentoId}/adicionar-itens', [AgendamentoController::class, 'adicionarItens']);


    Route::put('/agendamentos/{agendamento}/{situacao_id}', [AgendamentoController::class, 'updateStatusAgendamento']);
    Route::put('/agendamentos/{id}', [AgendamentoController::class, 'update']);
    Route::delete('/agendamentos/{agendamento}', [AgendamentoController::class, 'destroy']);

    Route::put('/excecao_horarios/{id}', [ExcecaoHorariosController::class, 'update']);
    Route::delete('/excecao_horarios/{id}', [ExcecaoHorariosController::class, 'delete']);
    Route::post('/excecao_horarios', [ExcecaoHorariosController::class, 'store']);
    Route::get('/excecao_horarios/{id}/{filter?}', [ExcecaoHorariosController::class, 'getAll'])->name('excecao_horariosGetAll');
    Route::get('/buscar_horarios/{id}', [ExcecaoHorariosController::class, 'show'])->name("excecao_horariosBuscar");

    Route::put('/funcionario_atende_servico/{id}', [FuncionarioAtendeServicoController::class, 'update']);
    Route::delete('/funcionario_atende_servico/{id}', [FuncionarioAtendeServicoController::class, 'delete']);
    Route::post('/funcionario_atende_servico', [FuncionarioAtendeServicoController::class, 'store']);
    Route::get('/funcionario_atende_servico/{id}/{filter?}', [FuncionarioAtendeServicoController::class, 'getAll'])->name('funcionario_atende_servicoGetAll');
    Route::get('/funcionario_atende_servico/{id}', [FuncionarioAtendeServicoController::class, 'show'])->name("funcionario_atende_servicoBuscar");

});
Route::post('/agendamentos', [AgendamentoController::class, 'store'])->name("adicionarAgendamento");

Route::post('/reset-password-token', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('token_senha');
Route::post('/agendamentos/agenda_funcionario/{id}/{funcionario_id}/{data}', [AgendamentoController::class, 'getHorariosDisponiveis'])->name("getHorariosDisponiveis");
Route::get('/agendamentos/{id}/{filter?}', [AgendamentoController::class, 'getAll'])->name('agendamentosGetAll');
Route::get('planos/getall/{filter?}', [PlanosController::class, 'getall'])->name('planosGetAll');
Route::get('planos/show/{planos}', [PlanosController::class, 'show'])->name('planosShow');
Route::get('/agendamentos/buscar/{id}/{agendamento?}', [AgendamentoController::class, 'show'])->name('agendamentoShow');
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login'])->name('login');
Route::post('/assinatura', [App\Http\Controllers\PagBankController::class, 'criarAssinatura'])->name('criarAssinatura');
Route::post('empresas/insert', [EmpresasController::class, 'store'])->name('empresacriar');
Route::put('empresas/update/{empresas}',[EmpresasController::class, 'update'])->name('empresaatualizar');
Route::post('/cadastrar', [App\Http\Controllers\RegisterController::class, 'store'])->name('cadastrar');
Route::post('/password/reset', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.reset.resetPassword');
Route::get('segmento/getall/{filter?}', [SegmentoController::class, 'getall'])->name('segmentoAll');
Route::get('segmento/show/{segmento}', [SegmentoController::class, 'show'])->name('segmentoShow');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('trocar_senha');
Route::post('logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
Route::get('/politica-de-privacidade', function () {
    return view('politica-privacidade');
});
