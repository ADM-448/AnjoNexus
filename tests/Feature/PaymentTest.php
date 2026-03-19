<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery\MockInterface;

/**
 * 🎓 DICA DE ESTUDO: 
 * Este arquivo testa todo o ciclo de vida da integração com o Mercado Pago.
 * É a parte mais robusta e crítica do seu TCC.
 */
class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TESTE 1: O "Gatilho" inicial da compra. Manda e redireciona (302).
     * O usuário clica no botão com o Plano e enviamos pro Mercado Pago de mentirinha.
     */
    public function test_create_preference_redirects_to_mercado_pago()
    {
        // 1. Arrange: Criar um usuário logado
        $user = User::factory()->create();

        // Em vez de chamar o Mercado Pago lá na internet de verdade e tomar um bloqueio de cartão,
        // Nós usamos uma técnica de "Mock Overload". Dizemos para o PHP:
        // "Quando o PaymentController pedir a classe PriorityClient, passe este dublê aqui que sempre aprova."
        $mockPreference = new \stdClass();
        $mockPreference->init_point = 'https://sandbox.mercadopago.com.br/checkout/v1/redirect-test';

        $mockClient = \Mockery::mock('overload:MercadoPago\Client\Preference\PreferenceClient');
        $mockClient->shouldReceive('create')->once()->andReturn($mockPreference);

        // 2. Act: O botão de compra do front dispara a URL de preferência passando o ID do plano
        $response = $this->actingAs($user)->post(route('payments.preference'), [
            'plan_id' => 'starter'
        ]);

        // 3. Assert: O sistema tem que aceitar a preferência e desviar o usuário (Status 302 Redirecionamento)
        $response->assertStatus(302);
        
        // E para onde ele mandou? Isso valida se o URL leva pro mercado pago de verdade.
        $this->assertStringContainsString('mercadopago.com', $response->headers->get('Location'));
    }

    /**
     * TESTE 2: O retorno da Notificação (IPN/Webhook). A mágica (Back-end to Back-end).
     * Esse teste não tem usuário na tela, ele simula a máquina do Mercado Pago batendo no seu site.
     */
    public function test_payment_webhook_grants_credits()
    {
        $user = User::factory()->create(['creditos_ia' => 5]);
        $paymentId = '123456';
        $creditsToGrant = 50;

        // O dublê desta vez finge ser o Mercado Pago respondendo com a nossa referência customizada: ID + SALDO + TEMPO
        $mockPayment = new \stdClass();
        $mockPayment->status = 'approved';
        $mockPayment->transaction_amount = 29.90;
        $mockPayment->external_reference = "{$user->id}_{$creditsToGrant}_1234567890";

        $mockClient = \Mockery::mock('overload:MercadoPago\Client\Payment\PaymentClient');
        $mockClient->shouldReceive('get')->with($paymentId)->andReturn($mockPayment);

        // Dispara o JSON idêntico ao que a API oficial envia.
        $payload = [
            'type' => 'payment',
            'data' => [
                'id' => $paymentId
            ]
        ];

        // Rota POST sem estar logado! (Aqui que seu Tabela de Banco SQLite foi crucial)
        $response = $this->postJson(route('payments.webhook'), $payload);

        // Sucesso 200 pro Mercado Pago parar de importunar e dar baixa
        $response->assertStatus(200);

        // A prova cabal para sua Banca: o usuário de fato rebebeu os 50 de saldo? (Tinha 5, ficou 55?)
        $this->assertEquals(55, $user->fresh()->creditos_ia);
        
        // Salvou o comprovante na tabela 'payments' do banco?
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'external_id' => $paymentId,
            'credits' => $creditsToGrant,
            'status' => 'approved'
        ]);
    }

    /**
     * TESTE 3: E se o usuário clicou em Voltar para Loja antes do Webhook notificar?
     * Pega o payment_id do ?GET e adianta tudo.
     */
    public function test_success_return_grants_credits()
    {
        $user = User::factory()->create(['creditos_ia' => 5]);
        $paymentId = '987654';
        
        // Dublê novamente, devolvendo Approved
        $mockPayment = new \stdClass();
        $mockPayment->status = 'approved';
        $mockPayment->transaction_amount = 29.90;
        $mockPayment->external_reference = "{$user->id}_50_123456789";

        $mockClient = \Mockery::mock('overload:MercadoPago\Client\Payment\PaymentClient');
        $mockClient->shouldReceive('get')->with($paymentId)->andReturn($mockPayment);

        // Aqui o usuário clica literalmente numa URL do navegador voltando com parâmetros
        $response = $this->actingAs($user)->get(route('payments.success', ['payment_id' => $paymentId]));

        // Se der tudo certo vai ver o dashboard bonitinho
        $response->assertRedirect(route('dashboard'));
        
        // E o crédito tá lá (tinha 5 + 50 = 55)
        $this->assertEquals(55, $user->fresh()->creditos_ia);
    }

    /**
     * Ao encerrar o teste, desfaz a "Magia" do Mockery para que os próximos testes rodem normais.
     */
    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
