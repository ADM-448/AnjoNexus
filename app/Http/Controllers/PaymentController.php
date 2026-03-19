<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

/**
 * 🎓 DICA DE ESTUDO: 
 * Este Controlador gerencia dinheiro. Ele fala diretamente com os servidores do Mercado Pago.
 * É aqui que a mágica das transações acontece e seu TCC ganha vida.
 */
class PaymentController extends Controller
{
    /**
     * O Método Mágico "__construct" sempre roda antes de qualquer outra coisa na classe.
     * Nós usamos ele para autenticar nosso sistema no Mercado Pago logo de cara,
     * usando a "Senha Secreta" (Token) que guardamos no arquivo .env por segurança.
     */
    public function __construct()
    {
        $token = trim(config('services.mercadopago.token'), '" ');
        MercadoPagoConfig::setAccessToken($token);
    }

    /**
     * Rota GET simples que mostra a tela com os 3 cartões de preços (Starter, Pro, Master).
     */
    public function checkout()
    {
        // Arrays no PHP servem como listas. Aqui definimos nossos produtos.
        $plans = [
            ['id' => 'starter', 'name' => 'Plano Start', 'credits' => 10, 'price' => 29.90, 'color' => 'indigo'],
            ['id' => 'pro',     'name' => 'Plano Pro',   'credits' => 50, 'price' => 99.90, 'color' => 'purple'],
            ['id' => 'master',  'name' => 'Plano Master','credits' => 200,'price' => 299.90, 'color' => 'emerald'],
        ];

        return view('payments.checkout', compact('plans'));
    }

    /**
     * O usuário clicou num botão de comprar. O que fazemos?
     * Nós não geramos cobrança de cartão direto aqui. Nós criamos uma "Intenção de Compra" (Preference)
     * e mandamos pro Mercado Pago processar a segurança.
     */
    public function createPreference(Request $request)
    {
        // Se a senha do Mercado Pago não tiver sido configurada, barra antes de dar erro na API
        if (empty(config('services.mercadopago.token'))) {
             return back()->with('error', 'Configuração do Mercado Pago faltando no .env (MERCADOPAGO_ACCESS_TOKEN)');
        }
        
        $planId = $request->plan_id;
        $plans = [
            'starter' => ['name' => 'Plano Start', 'credits' => 10, 'price' => 29.90],
            'pro'     => ['name' => 'Plano Pro',   'credits' => 50, 'price' => 99.90],
            'master'  => ['name' => 'Plano Master','credits' => 200,'price' => 299.90],
        ];

        // Hacker tentando comprar um plano que não existe na lista? Volta ele.
        if (!isset($plans[$planId])) {
            return back()->with('error', 'Plano inválido.');
        }

        $plan = $plans[$planId];
        $client = new PreferenceClient(); // Classe oficial do Mercado Pago

        // 1. DADOS DA PREFERÊNCIA: O que o MP precisa saber pra cobrar o cara?
        $preferenceData = [
            "items" => [
                [
                    "title" => $plan['name'],
                    "quantity" => 1,
                    "unit_price" => (float) $plan['price'] // Forçando ser decimal (dinheiro)
                ]
            ],
            // TRUQUE DE MESTRE: O Mercado Pago não sabe quem é nosso usuário local.
            // Para lembrarmos quando ele avisar que o pagamento foi aproado,
            // nós passamos esse código de referência gigante: "ID_CREDITOS_TIMESTAMP"
            "external_reference" => (string) auth()->id() . "_" . $plan['credits'] . "_" . time(),
            
            // Onde jogar o cara assim que ele pagar (ou se der erro)
            "back_urls" => [
                "success" => route('payments.success'),
                "failure" => route('dashboard'),
                "pending" => route('dashboard'),
            ],
            // Para onde o robô do Mercado Pago deve enviar o aviso fantasma por trás dos panos
            // Importante: No seu servidor web, este link precisa ser o seudominio.com/payments/webhook
            "notification_url" => "https://honest-bugs-trade.loca.lt/payments/webhook",
        ];

        try {
            // Cria a compra lá nos servidores deles e recebe o de-acordo
            $preference = $client->create($preferenceData);
            // Pega o cara daqui e arremessa ele na tela do portal do MP
            return redirect($preference->init_point);
        } catch (\Exception $e) {
            // Se algo der errado (ex: Chave MP Inválida), quebramos a mensagem pra você ver rápido
            $errorDetail = $e->getMessage();
            if (method_exists($e, 'getApiResponse')) {
                $errorDetail .= ' | Response: ' . json_encode($e->getApiResponse()->getContent());
            } elseif (method_exists($e, 'getResponse')) {
                $errorDetail .= ' | Response: ' . json_encode($e->getResponse());
            }
            Log::error('Erro MP Detalhado: ' . $errorDetail);
            return back()->with('error', 'Erro MP: ' . $errorDetail);
        }
    }

    /**
     * O ROBO DO MERCADO PAGO BATE NA PORTA (IPN/Webhook)
     * Essa Rota é acessada DE FORA. Não tem usuário logado, browser, nada.
     * É o Mercado Pago gritando no ouvido do AnjoNexus: "O João pagou!"
     */
    public function webhook(Request $request)
    {
        Log::info('MP Webhook Recebido: ', $request->all());

        $paymentId = null;

        // O Mercado pago tem 3 versões de JSON que ele envia ao longo dos anos.
        // A gente testa as 3 formatos pra ter certeza absoluta que achamos o ID ($paymentId)
        if ($request->has('type') && $request->type == 'payment') {
            $paymentId = $request->input('data.id');
        } elseif ($request->has('action') && strpos($request->action, 'payment') !== false) {
            $paymentId = $request->input('data.id');
        } elseif ($request->has('resource')) {
            $resource = $request->input('resource');
            $parts = explode('/', $resource);
            $paymentId = end($parts);
        }

        // Se o robô nos deu um ID...
        if ($paymentId) {
            try {
                // Vai no MP pela "porta dos fundos" e pergunta: "Esse ID pagou memo?"
                $client = new PaymentClient();
                $mpPayment = $client->get($paymentId);
                
                // Se pagou de verdade e o status tá 'Aprovado'
                if ($mpPayment && $mpPayment->status == 'approved') {
                    // Lembra do nosso TRUQUE DE MESTRE lá em cima? Puxa ele aqui.
                    $ref = $mpPayment->external_reference;
                    
                    if ($ref && strpos($ref, '_') !== false) {
                        // O explode "fatia" a frase onde tem '_' criando variaveis
                        // "2_50_19902" vira: $userId(2), $credits(50)
                        list($userId, $credits, $timestamp) = explode('_', $ref);

                        // Garante que não tamos dando crédito duplicado pro mesmo pagamento (Fraude)
                        if (!Payment::where('external_id', $paymentId)->exists()) {
                            $user = User::find($userId); // Acha o usuario no seu banco SQLite/MySQL
                            if ($user) {
                                // Deposita os créditos na conta dele
                                $user->increment('creditos_ia', (int) $credits);
                                
                                // Salva o recibo pra controle seu
                                Payment::create([
                                    'external_id' => $paymentId,
                                    'user_id' => $userId,
                                    'amount' => $mpPayment->transaction_amount,
                                    'credits' => (int) $credits,
                                    'status' => 'approved',
                                ]);
                                Log::info("Créditos Concedidos via Webhook: {$credits} para o User ID: {$userId}");
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Deu B.O de conexão com a API deles? Avisa o erro silencioso pra não matar o site
                Log::error('MP Webhook Processing Error: ' . $e->getMessage());
            }
        }

        // Tem que sempre responder 200 OK, se não o Mercado Pago fica tentando mandar de novo pra sempre.
        return response()->json(['status' => 'ok']);
    }

    /**
     * O CAMINHO FELIZ (Fallback)
     * Às vezes o João paga no Pix, o MP aprova na tela deles, o João aperta "Voltar Pra Loja" rápido,
     * e o seu Webhook ali de cima demora 5 min para chegar e o saldo do Joao não ta no site.
     * Esse método pega o João que apertou Voltar, lê o link e joga o saldo na conta de imediato.
     */
    public function successReturn(Request $request)
    {
        // Pega o ID na barra de endereços (tipo ?payment_id=45)
        $paymentId = $request->query('payment_id');
        
        if (!$paymentId) {
            return redirect()->route('dashboard')->with('error', 'Sem ID de pagamento do Mercado Pago.');
        }

        try {
            // Refaz a checagem oficial só pra ter certeza que ele não fabricou aquele id na barra
            $client = new PaymentClient();
            $mpPayment = $client->get($paymentId);
            
            if ($mpPayment && $mpPayment->status == 'approved') {
                $ref = $mpPayment->external_reference;
                
                if (strpos($ref, '_') !== false) {
                    list($userId, $credits, $timestamp) = explode('_', $ref);

                    // A verificação EXISTS é crucial aqui pra não dar crédito duplo caso o webhook ja tenha agido rapido
                    if (!Payment::where('external_id', $paymentId)->exists()) {
                        $user = User::find($userId);
                        if ($user) {
                            $user->increment('creditos_ia', (int) $credits);
                            Payment::create([
                                'external_id' => $paymentId,
                                'user_id' => $userId,
                                'amount' => $mpPayment->transaction_amount,
                                'credits' => (int) $credits,
                                'status' => 'approved',
                            ]);
                        }
                        // Efeito UAU! pra ele
                        return redirect()->route('dashboard')->with('success', "Sensacional! Pagamento aprovado e {$credits} créditos foram adicionados à sua conta na hora! 🚀");
                    }
                }
                
                return redirect()->route('dashboard')->with('success', 'Seus créditos já foram adicionados com sucesso ao seu saldo!');
            }
        } catch (\Exception $e) {
            Log::error('Erro no Retorno Cliente: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Pagamento registrado! Seus créditos estarão no painel.');
    }
}
