<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 🎓 DICA DE ESTUDO: 
 * Este arquivo testa o Painel Principal (Dashboard). 
 * Testes "Feature" simulam um usuário acessando o site pelo navegador.
 */
class DashboardTest extends TestCase
{
    // RefreshDatabase apaga e recria o banco de dados a cada teste,
    // garantindo que um teste não suje os dados do outro.
    use RefreshDatabase;

    /**
     * Testa se um usuário logado consegue ver a tela inicial do painel.
     */
    public function test_dashboard_is_accessible_by_authenticated_users()
    {
        // 1. PREPARAÇÃO (Arrange)
        // Cria um usuário falso no banco de testes
        $user = User::factory()->create();
        
        // Cria uma empresa vinculada a este usuário falso
        Empresa::create([
            'user_id' => $user->id,
            'razao_social' => 'Empresa Teste',
            'cnpj' => '12.345.678/0001-99'
        ]);

        // 2. AÇÃO (Act)
        // actingAs($user) simula que o usuário fez login.
        // get(route('dashboard')) simula ele digitando a URL do dashboard no navegador.
        $response = $this->actingAs($user)->get(route('dashboard'));

        // 3. VERIFICAÇÃO (Assert)
        // assertStatus(200) significa "A página carregou com sucesso?" (HTTP 200 OK)
        $response->assertStatus(200);
        // Verifica se a tela HTML retornada é realmente a 'dashboard.blade.php'
        $response->assertViewIs('dashboard');
        // Verifica se o Controller enviou a variável $empresa para a tela HTML
        $response->assertViewHas('empresa');
        $response->assertViewHas('projetos');
        $response->assertViewHas('totalEditais');
    }

    /**
     * Testa a barreira de segurança: um visitante anônimo não pode acessar o painel.
     */
    public function test_dashboard_redirects_unauthenticated_users()
    {
        // 1. AÇÃO: Tenta acessar direto sem fazer login (não usamos actingAs aqui)
        $response = $this->get(route('dashboard'));

        // 2. VERIFICAÇÃO:
        // assertStatus(302) significa "O sistema me redirecionou?" (HTTP 302 Found)
        $response->assertStatus(302);
        // Verifica se o redirecionamento foi de fato para a tela de Login
        $response->assertRedirect(route('login'));
    }
}
