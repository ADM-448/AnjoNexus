<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Edital;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 🎓 DICA DE ESTUDO: 
 * Testa a página final onde o botão "Gerar Proposta" aparece para o usuário.
 */
class OpenAITest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a página carrega corretamente e recebe o Edital pela URL.
     */
    public function test_openai_generator_interface_is_accessible()
    {
        $user = User::factory()->create();
        
        // Para enviar propostas, o usuário é obrigado a ter uma empresa cadastrada.
        Empresa::create([
            'user_id' => $user->id,
            'razao_social' => 'Empresa Teste',
            'cnpj' => '00.000.000/0001-00'
        ]);

        // O Edital também deve estar salvo no banco
        $edital = Edital::create([
            'titulo' => 'Edital Base', 
            'orgao' => 'FAPESP'
        ]);
        
        // Simula o acesso à URL http://site/openai?edital_id=X
        $response = $this->actingAs($user)->get(route('openai.index', ['edital_id' => $edital->id]));
        
        // Sucesso 200 e confirma que carregou a view 'openai.index.blade.php'
        $response->assertStatus(200);
        $response->assertViewIs('openai.index');
    }

}
