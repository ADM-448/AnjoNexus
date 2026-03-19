<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 🎓 DICA DE ESTUDO: 
 * Este arquivo testa o formulário onde a empresa atualiza os dados dela (CNPJ, Porte, etc).
 */
class EmpresaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a página do formulário de empresa carrega corretamente.
     */
    public function test_empresa_edit_page_is_accessible()
    {
        $user = User::factory()->create();

        // O seu EmpresaController tem uma função especial (firstOrCreate).
        // Se a empresa não existe, ele cria uma vazia só com o ID do usuário.
        $response = $this->actingAs($user)->get(route('empresa.edit'));

        // Verifica se a tela respondeu sucesso (200)
        $response->assertStatus(200);
        $response->assertViewIs('empresa.edit');
        
        // Verifica se a "mágica" do firstOrCreate funcionou criando o registro fantasma no banco
        $this->assertDatabaseHas('empresas', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Testa se o CRUD de edição (Update) realmente salva as coisas no banco de dados.
     */
    public function test_empresa_can_be_updated()
    {
        $user = User::factory()->create();
        
        // Cria os dados no banco "como estavam antes"
        $empresa = Empresa::create([
            'user_id' => $user->id,
            'razao_social' => 'Antiga',
            'cnpj' => '00.000.000/0001-00'
        ]);

        // .put() simula o momento em que o usuário preenche o formulário e clica no botão "Salvar"
        // Passamos um array que simula os campos preenchidos na tela
        $response = $this->actingAs($user)->put(route('empresa.update'), [
            'razao_social' => 'Nova Empresa Atualizada',
            'cnpj' => '11.111.111/0001-11',
            'porte' => 'Micro'
        ]);

        // Verifica se ao salvar o sistema recarregou a página (com a mensagem verdinha)
        $response->assertRedirect(route('empresa.edit'));
        
        // A prova real: Verifica se lá dentro do banco de dados (SQLite) as colunas foram alteradas!
        $this->assertDatabaseHas('empresas', [
            'id' => $empresa->id,
            'razao_social' => 'Nova Empresa Atualizada', // Mudou de "Antiga" para "Nova"
            'cnpj' => '11.111.111/0001-11',
            'porte' => 'Micro'
        ]);
    }
}
