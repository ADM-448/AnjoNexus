<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Criar um usuário default
        $user = \App\Models\User::factory()->create([
            'name' => 'Usuário Teste',
            'email' => 'teste@anjoinovador.com',
            'password' => bcrypt('password'),
        ]);

        // Vincular uma empresa
        \App\Models\Empresa::create([
            'user_id' => $user->id,
            'razao_social' => 'Tech Mining Inovação LTDA',
            'cnpj' => '12.345.678/0001-90',
            'porte' => 'Média',
            'setor' => 'Mineração / Tecnologia',
            'estado' => 'SP',
            'n_funcionarios' => 45,
            'faturamento_anual' => '2.5 milhões',
            'historico_inovacao' => 'Desenvolvimento de sensores IoT para maquinário pesado.'
        ]);

        $this->call([
            EditalSeeder::class,
        ]);
    }
}
