<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Edital;

class EditalSeeder extends Seeder
{
    public function run()
    {
        $edital = Edital::create([
            'titulo' => 'Finep Mais Inovação Brasil – Rodada 2 – Transformação Mineral',
            'codigo_externo' => '771',
            'orgao' => 'Finep',
            'email_contato' => 'cp_transformacao_mineral@finep.gov.br',
            'modalidade' => 'Subvenção Econômica',
            'orcamento_global' => 200000000.00,
            'publico_alvo' => 'Empresas (obrigatória parceria com ICTs)',
            'temas' => 'Minerais críticos e estratégicos, Descarbonização, Transição Energética',
            'trl_min' => '3',
            'trl_max' => '9',
            'status' => 'Aberto',
            'url_oficial' => 'http://www.finep.gov.br/chamadas-publicas/771'
        ]);

        // Criar Seções e Perguntas Mocks do Finep
        $secao1 = $edital->secoes()->create([
            'titulo' => '1. Descrição do Projeto',
            'ordem' => 1
        ]);
        
        $secao1->perguntas()->create([
            'texto' => 'Qual o problema que o projeto resolve? (Aponte também o diferencial inovador).',
            'tipo' => 'textarea',
            'max_palavras' => 500,
            'ordem' => 1
        ]);
        $secao1->perguntas()->create([
            'texto' => 'Descreva a solução proposta.',
            'tipo' => 'textarea',
            'max_palavras' => 800,
            'ordem' => 2
        ]);
        
        $secao2 = $edital->secoes()->create([
            'titulo' => '2. Viabilidade e TRL',
            'ordem' => 2
        ]);
        
        $secao2->perguntas()->create([
            'texto' => 'Qual o Nível de Maturidade Tecnológica (TRL) atual do projeto e qual TRL se espera atingir ao final?',
            'tipo' => 'textarea',
            'max_palavras' => 300,
            'ordem' => 1
        ]);
    }
}
