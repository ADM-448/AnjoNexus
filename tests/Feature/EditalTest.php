<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Edital;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 🎓 DICA DE ESTUDO: 
 * Avalia as telas relacionadas à leitura e listagem de Editais da Finep, Fapesp, etc.
 */
class EditalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a página "Radar de Oportunidades" carrega sem erros 500.
     */
    public function test_edital_index_is_accessible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('editais.index'));
        
        $response->assertStatus(200);
    }

    /**
     * Testa o evento de "Visualizar" um edital.
     * Como a IA (Gemini) seria acionada automaticamente aqui nos bastidores,
     * precisamos usar MOCKS para bloquear a requisição à internet real e evitar 
     * gastar créditos da API do Google durante as baterias de teste.
     */
    public function test_edital_show_redirects_to_generator()
    {
        // Revela o erro completo na tela caso o teste quebre
        $this->withoutExceptionHandling(); 

        // 1. PREPARAÇÃO (Mock)
        // Isso "sequestra" o GeminiService original e cria um dublê de IA.
        $mockGemini = \Mockery::mock(\App\Services\GeminiService::class);
        
        // Ensinamos o dublê: "Quando chamarem generateContent, devolva este texto falso".
        $mockGemini->shouldReceive('generateContent')->andReturn('{"temas": "Teste Inovação"}');
        
        // Injeta o dublê no Laravel (no controller, quando ele pedir a classe Gemini, receberá o dublê)
        $this->app->instance(\App\Services\GeminiService::class, $mockGemini);

        $user = User::factory()->create();
        $edital = Edital::create([
            'titulo' => 'Edital Fake de Inovacao',
            'orgao' => 'FINEP',
            'status' => 'Aberto',
            'url_oficial' => 'https://finep.gov.br'
        ]);

        // 2. AÇÃO: Clica no botão "Ver Detalhes"
        $response = $this->actingAs($user)->get(route('editais.show', $edital->id));
        
        // 3. VERIFICAÇÃO: 
        // A regra de negócio do seu controller diz que não existe mais a tela de "Ver Detalhes".
        // Ao invés disso, ele redireciona (302) o usuário imediatamente para a tela do Gerador OpenAI.
        $response->assertStatus(302);
        
        // Confirma se o destino do desvio é a rota do gerador
        $this->assertStringContainsString(route('openai.index'), $response->headers->get('Location'));
    }
}
