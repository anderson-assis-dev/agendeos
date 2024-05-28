<?php

namespace App\Http\Controllers;
use App\Models\Agendamento;
use App\Models\AgendamentoItem;
use App\Models\Empresas;
use App\Models\fluxo_caixa;
use App\Models\FormaPagamento;
use App\Models\funcionarios;
use App\Models\GatewayPagamento;
use App\Models\ordem_servico_servico;
use App\Models\OrdemServicos;
use App\Models\Planos;
use App\Models\Situacao;
use App\Models\token;
use App\Models\User;
use App\Models\UsuarioAssinatura;
use App\Models\whatsapp;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagBankController extends Controller
{
    public function criarAssinante($data)
    {
        $dadosPagBank = GatewayPagamento::query()->where("nome", "PagBank")->first();
        if($dadosPagBank->producao){
            $apiKey = $dadosPagBank->token_producao;
            $url = $dadosPagBank->endpoint_producao.'customers';
        }
        else{
            $url = $dadosPagBank->endpoint_homologacao.'customers';
            $apiKey = $dadosPagBank->token_homologacao;
        }
        $user = User::query()->where('id', $data['user_id'])->first();
        $empresaUser = Empresas::query()->where("id", $user->empresa_id)->first();

        if(empty($data["cartaoHash"]) or !isset($data["cartaoHash"])){
            return response()->json(["message" => "Informe o cartão de crédito", error => true], 401);
        }
        $dados = [
            'address' => [
                'country' => 'BRA',
                'street' => $data["logradouro"] ?? $user->logradouro,
                'number' => $data["numero"] ?? $user->numero,
                'complement' => $data["complemento"] ?? $user->complemento,
                'locality' => $data["cidade"] ?? $user->cidade ,
                'city' => $data["cidade"] ?? $user->cidade,
                'region_code' => $data["estado"] ?? $user->estado,
                'postal_code' => str_replace(array(".", "-"), "", $data["cep"] ?? $user->cep),
            ],
            'reference_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tax_id' => str_replace(array(".", "-"), "", $data["cpf"]),
            'phones' => [
                [
                    'country' => '55',
                    'area' => $data["ddd"],
                    'number' => $data["telefone"],
                ],
            ],
            'birth_date' => $data["aniversario"],
            'billing_info' => [
                [
                    'card' => [
                        'encrypted' => $data["cartaoHash"],
                        "holder"=> [
                            "name"=> $data["nome_titular"] ?? $user->name,
                              "birth_date"=> $data["aniversario"],
                              "tax_id"=> str_replace(array(".", "-"), "", $data["cpf"])
                        ],
                    ],
                    "type"=> "CREDIT_CARD"
                ],
            ],
        ];
        $client = new Client();
        try {
            $response = $client->request('POST', $url, [
                'body' => json_encode($dados),
                'headers' => [
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
            ]);
            $body = $response->getBody()->getContents();
            $decodedResponse = json_decode($body, true);
            $user->gateway_assinante_id = $decodedResponse['id'];
            $user->save();

            return $decodedResponse['id'];
        } catch (RequestException $e) {
            // Verifica se a exceção tem uma resposta HTTP associada
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);
                $errorMessage = $responseData['error_messages'][0]['description'];
                return response()->json(['error' => $errorMessage, "status" => 500, "dados" => $dados, "url" => $url, "response_pagbank" => $responseData], 500);
            } else {
                return response()->json(['error' => $e->getMessage(), "status" => 500, "dados" => $dados], 500);
            }
        }
    }
    public function buscarAssinante($id)
    {
        $dadosPagBank = GatewayPagamento::query()->where("nome", "PagBank")->first();
        if($dadosPagBank->producao){
            $url = $dadosPagBank->endpoint_producao.'customers/'.$id;
            $apiKey = $dadosPagBank->token_producao;
        }
        else{
            $url = $dadosPagBank->endpoint_homologacao.'customers';
            $apiKey = $dadosPagBank->token_homologacao;
        }
        $client = new Client();
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
            ]);
            $body = $response->getBody()->getContents();
            $decodedResponse = json_decode($body, true);
            logger($url);
            logger('GET');
            logger($body);
            logger("__");
            logger($decodedResponse["customers"]);
            return $decodedResponse["customers"]['id'];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function criarAssinatura(Request $request)
    {
        $user = User::query()->where('id',$request->user_id)->first();

        $cliente_id = $user->gateway_assinante_id ? $user->gateway_assinante_id : $this->criarAssinante($request->all());
        if(empty($request->cartaoHash) or !isset($request->cartaoHash)){
            return response()->json(["message" => "Informe o cartão de crédito", "error" => true], 401);
        }
        if(isset($cliente_id->original))
            return response()->json($cliente_id->original, 500);
        $dadosPagBank = GatewayPagamento::query()->where("nome", "PagBank")->first();
        if($dadosPagBank->producao){
            $url = $dadosPagBank->endpoint_producao.'subscriptions';
            $apiKey = $dadosPagBank->token_producao;
        }
        else{
            $url = $dadosPagBank->endpoint_homologacao.'subscriptions';
            $apiKey = $dadosPagBank->token_homologacao;
        }
        $empresaUser = Empresas::query()->where("id", $user->empresa_id)->first();
        $planoAssinado = Planos::query()->where("id", $empresaUser->plano_id)->first();
        $assinatura = UsuarioAssinatura::query()->where("user_id", $user->id)
            ->where("plano_id", $planoAssinado->id)
            ->whereNull("referencia_id")
            ->first();
        if(!$assinatura){
            $assinatura = UsuarioAssinatura::create(["plano_id" => $planoAssinado->id, "user_id" => $user->id, "ativo" => 0, "data_assinatura" => date("Y-m-d")]);
        }
        $data = array(
            "plan" => array(
                "id" => $planoAssinado->gateway_plano_id
            ),
            "customer" => array(
                "id" => $cliente_id,
                "billing_info" => array(
                    array(
                        "card" => array(
                            "holder" => array(
                                "phone" => array(
                                    'country' => "55",
                                    'area' => $request->ddd,
                                    'number' => $request->telefone
                                ),
                                'name' => $request->nome_titular,
                                'birth_date' => $request->aniversario,
                                'tax_id' => str_replace(array(".", "-"), "", $request->cpf)
                            ),
                            "encrypted" => $request->cartaoHash
                        ),
                        "type" => "CREDIT_CARD"
                    )
                )
            ),
            "amount" => array(
                "currency" => "BRL",
                "value" => $planoAssinado->valor
            ),
            "reference_id" => $assinatura->id,
            "payment_method" => array(
                array(
                    "type" => "CREDIT_CARD",
                    "card" => array(
                        "security_code" => $request->cvv
                    )
                )
            ),
            "pro_rata" => false
        );

        $client = new Client();
        try {
            $response = $client->request('POST', $url, [
                'body' => json_encode($data),
                'headers' => [
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $decodedResponse = json_decode($body, true);

            if($statusCode >= 200 and $statusCode <= 299){
                $dataAtual = Carbon::now();
                $dataFutura = $dataAtual->addDays(30);
                $dataAtualFormatada = $dataAtual->format('Y-m-d');
                $dataFuturaFormatada = $dataFutura->format('Y-m-d');
                $assinatura->update(["referencia_id" => $decodedResponse["id"], "ativo"  => 1,"teste"  => 0, "data_pagamento" => date("Y-m-d"),
                    "data_assinatura" =>date("Y-m-d"), "data_renovacao" => $dataFuturaFormatada  ]);
                return response()->json(['error' => false, 'response' => $decodedResponse], 200);

            }
            return response()->json(['error' => true, 'status' => $statusCode], 500);
        } catch (RequestException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            $errorMessage = $responseData['error_messages'][0]['description'];
            return response()->json(['error' => $errorMessage], 500);
        }
    }
    public function criarPagarPedidoPagamento(Request $request)
    {
        $dadosPagBank = GatewayPagamento::query()->where("nome", "PagBank")->first();
        $agendamento = Agendamento::query()->where("id", $request->agendamento_id)->first();
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $agendamento->telefone);

        $ddd = substr($telefoneLimpo, 0, 2);
        $numero = substr($telefoneLimpo, 2);
        if($dadosPagBank->producao){
            $url = $dadosPagBank->endpoint_producao."orders";
            $apiKey = $dadosPagBank->token_producao;
            $accountId = $dadosPagBank->account_id_producao;
        }
        else{
            $url = $dadosPagBank->endpoint_homologacao."orders";
            $apiKey = $dadosPagBank->token_homologacao;
            $accountId = $dadosPagBank->account_id_homologacao;
        }
        $url = str_replace(".assinaturas", "", $url);
        $now = Carbon::now();
        $expirationDate = $now->addMinutes(30);

        $formattedExpirationDate = $expirationDate->toIso8601String();
        $totalITens = $this->itensAgendamentoValorTotal($agendamento);
        $taxa = 100;
        $porcentagemCancelamento = 0.3;
        $taxaCancelamento = $totalITens * $porcentagemCancelamento;
        $total = $totalITens + $taxa;
        $itens = [];
        $servicos = $this->itensAgendamento($agendamento);
        foreach ($servicos as $item) {
            $itens[] =  [
                "reference_id" => $item["id"],
                "name" => $item["nome"],
                "quantity" => 1,
                "unit_amount" => $item["valor"]
            ];
        }
        $itens[] =  [
            "reference_id" => "Taxa",
            "name" => "Taxa",
            "quantity" => 1,
            "unit_amount" => $taxa
        ];
        $receivers =  [
            [
                "account" => [
                    "id" => $accountId //Agendos
                ],
                "amount" => [
                    "value" => $total
                ]
            ]
        ] ;
        $qr_codes = [
            "amount" => [
                "value" => $total
            ],
            "expiration_date" => $formattedExpirationDate,
        ];
        $charges =     [
            [
                "reference_id" => $request->agendamento_id,
                "description" => "Pagamento dos serviçoes referentes ao agendamento",
                "amount" => [
                    "value" => $total,
                    "currency" => "BRL"
                ],
                "payment_method" => [
                    "type" => "CREDIT_CARD",
                    "installments" => 1,
                    "capture" => true,

                    "card" => [
                        "number" => str_replace(" ", "", $request->card_number),
                        "exp_month" => $request->mes,
                        "exp_year" => $request->ano,
                        "security_code" => $request->cvv,
                        "holder" => [
                            "name" => $request->nome,
                            "tax_id" => str_replace(array(".", "-"), "", $request->cpf)
                        ],
                        "encrypted" => $request->cartaoHash,
                        "store" => false
                    ]
                ]

            ]
        ];
        /*
         * // ou $qr_codes
        $charges["splits"] =  [
            "method" => "FIXED",
            "receivers" => $receivers
        ];*/
        $client = new Client();
        $data = [
            "reference_id" => "agendamento",
            "customer" => [
                "name" => $agendamento->nome,
                "email" => $agendamento->email,
                "tax_id" => str_replace(array(".", "-"), "", $request->cpf),
                "phones" => [
                    [
                        "country" => "55",
                        "area" => $ddd,
                        "number" => $numero,
                        "type" => "MOBILE"
                    ]
                ]
            ],
            "items" => $itens,
            "shipping" => [
                "address" => [
                    "street" => "Avenida Brigadeiro Faria Lima",
                    "number" => "1384",
                    "complement" => "apto 12",
                    "locality" => "Pinheiros",
                    "city" => "São Paulo",
                    "region_code" => "SP",
                    "country" => "BRA",
                    "postal_code" => "01452002"
                ]
            ],
            "notification_urls" => [
                "https://agendos.com.br/retorno_pagamento"
            ]

        ];
        if($request->forma_pagamento == 3){
            $data["charges"] = $charges;
        }
        else{
            $data["qr_codes"] = [$qr_codes];
        }
        try {
            $response = $client->request('POST', $url, [
                'body' => json_encode($data),
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $body = json_decode($body, true);
            if($statusCode >= 200 and $statusCode <= 299){
                $administrador  = User::where('id', $agendamento->user_id)->first();
                $estabelecimento  =  Empresas::where('situacao', 1)->where('id', $administrador->empresa_id)->first();
                if(isset($body["charges"][0]["status"]) and  $body["charges"][0]["status"] == "PAID"){
                    $agendamento->situacao_id = 2;
                    $agendamento->forma_pagamento_id = $request->forma_pagamento;
                    $agendamento->save();
                    $this->gerarComanda($agendamento->id, $servicos);
                    if($agendamento->funcionario_id){
                        $funcionario = funcionarios::query()->where('id', $agendamento->funcionario_id)->first();
                        if($funcionario->celular){
                            $estabelecimento->telefone = $funcionario->celular;
                        }
                    }
                    $this->notifyClient($agendamento->id,$estabelecimento, false );
                    $this->notifyClient($agendamento->id,$estabelecimento, true );
                }
                elseif(isset($body["qr_codes"][0]["text"])){
                    $agendamento->situacao_id = 1;
                    $agendamento->forma_pagamento_id = $request->forma_pagamento;
                    $agendamento->save();
                    $this->notifyClient($agendamento->id,$estabelecimento, false, $body["qr_codes"][0]["text"] );
                    $this->notifyClient($agendamento->id,$estabelecimento, true , $body["qr_codes"][0]["text"]);
                }
            }

            return response()->json($body, $statusCode);
        } catch (\Exception $e) {
            // Tratar erros, se necessário
            return response()->json(["request" => $data, "response" => $e->getMessage()." ".$e->getLine() ], 500);

        }
    }

    public function gerarComanda($id, $servicos)
    {
        $agendamento = Agendamento::query()->where("id", $id)->first();
        $comanda = OrdemServicos::create(
            [
                "id_cliente" => $agendamento->clientes_id,
                "id_funcionario" => $agendamento->funcionario_id,
                "id_servico" => 0,
                "situacao" => $agendamento->situacao_id,
                "id_forma_pagamento" => $agendamento->forma_pagamento_id,
                "inicio_os" => date("Y-m-d H:i:s", strtotime($agendamento->data_agendamento->format('Y-m-d')." ".$agendamento->hora_agendamento)),
                "previsao_os" => date("Y-m-d H:i:s", strtotime($agendamento->data_agendamento->format('Y-m-d')." ".$agendamento->hora_agendamento)),
                "user_id" => $agendamento->user_id,
            ]
        );
        $comanda->valor = 0;

        foreach ($servicos as $item) {
            $valor_temp = $item['valor'];
            $comanda->valor += $valor_temp;
            $data = array(
                "os_id"      => $comanda->id,
                "id_servico" => $item['servicos_id'],
                "quantidade" => $item['quantidade'],
                "valor" => $valor_temp
            );
            ordem_servico_servico::create($data);
        }
        $this->gerarFluxoCaixa($comanda);
    }
    public function gerarFluxoCaixa($data)
    {
        fluxo_caixa::create(
            [
                "nome" => "Ordem de Serviço #" . $data->id,
                "user_id" => $data->user_id,
                "tipo_id" => 1,
                "data" => date("Y-m-d"),
                "pagamento_id" => 3,
                "produto_id" => null,
                "os_id" => $data->id,
                "cliente_id" => $data->id_cliente,
                "quantidade" => 1,
                "situacao" => 3,
                "valor" => $data->valor
            ]
        );
        return true;
    }
    public function itensAgendamento($agendamento)
    {
        return AgendamentoItem::query()->where("agendamento_id", $agendamento->id)
            ->leftJoin('servicos', 'servicos.id', '=', 'agendamento_itens.servicos_id')
            ->select("agendamento_itens.*", "servicos.nome")
            ->get();
    }
    public function itensAgendamentoValorTotal($agendamento)
    {
        return AgendamentoItem::query()->where("agendamento_id", $agendamento->id)->sum('valor');
    }
    public function getServicosNotifyClint($dados)
    {
        $data = array();
        $nomes = "";
        $total = 0;

        foreach ($dados as $item) {
            $nomes .= " | " . $item->servico->nome;
            $total += ($item->valor/100);
        }
        return array("nomes" => $nomes, "total" => $total);
    }

    public function notifyClient($id, $empresa, $notificar_empresa=false, $textoPagamento = null)
    {
        $data = Agendamento::query()->where("id", $id)->first();
        $itens = AgendamentoItem::where('agendamento_id', $id)
            ->with('servico')
            ->get();
        $extras = $this->getServicosNotifyClint($itens);
        $cancelar = "";

        if($notificar_empresa){
            $telefone  = "55" . str_replace(array("(", ")", ".", "-", " "), "",   $empresa->telefone );
        }
        else{
            $telefone  = "55" . str_replace(array("(", ")", ".", "-", " "), "",   $data->telefone);
        }
        if($textoPagamento){
            $nome_cliente = $data->nome.", este é o Pix copia-e-cola para realizar o pagamento do agendamento na empresa ".$empresa->razao_social." copie o código a seguir ";
            $nome_cliente .= $textoPagamento;

        }
        else{
            $nome_cliente = $data->nome.", esta é uma confirmação do pagamento do seu agendamento realizado na empresa ".$empresa->razao_social;

        }

        $situacao = Situacao::where('id',$data->situacao_id)->first()->nome;

        $values = [
            "1" => [
                "type" => "text",
                "text" => $nome_cliente
            ],
            "2" => [
                "type" => "text",
                "text" => $id
            ],
            "3" => [
                "type" => "text",
                "text" => $extras['nomes']
            ],
            "4" => [
                "type" => "text",
                "text" => number_format($extras['total'], 2, ".", ",")
            ],
            "5" => [
                "type" => "text",
                "text" => $situacao
            ],
            "6" => [
                "type" => "text",
                "text" =>  FormaPagamento::where('id', $data->forma_pagamento_id)->first()->nome
            ]
            ,
            "7" => [
                "type" => "text",
                "text" =>  date("d/m/Y", strtotime($data->data_agendamento))." ".$data->hora_agendamento."  ".$cancelar
            ]
        ];
        $vetor = array(
            "messaging_product" => "whatsapp",
            "to"           => $telefone,
            "type"         => 'template',
            "template"     => array(
                "name"     => "agendamento",
                "language" => array(
                    "code" => "pt_BR",
                    "policy" => "deterministic"
                ),
                "components"     =>
                    array(
                        array(
                            "type"       => "body",
                            "parameters" => $values
                        )
                    )
            ),


        );

        $zap =  whatsapp::sendMessage($vetor, token::token());
        return [$vetor,$zap];
    }


}
