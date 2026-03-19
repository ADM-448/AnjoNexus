<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 🎓 DICA DE ESTUDO: 
 * O que é um "Job"? (Trabalho/Tarefa).
 * Enquanto um Controller roda na hora em que o usuário clica na tela (e deixa a tela carregando),
 * um Job roda escondido no Servidor, no "Backstage". 
 * Por isso o site não trava mesmo quando ele busca 500 editais na FINEP de forma simultânea.
 */
class RunAllScrapersJob implements ShouldQueue
{
    // Essas "traits / magias" dão poder pro Job rodar em Fila, pausar, e não dar erro de memória.
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // Aqui dentro a gente passaria variáveis se precisasse na hora de criar o job.
        // Como o Scraping varre a internet inteira toda vez, ele não precisa de id específico pra nascer.
    }

    /**
     * O Método Principal!
     * Assim que o Worker do servidor disser "É sua vez", ele roda essa função.
     */
    public function handle(): void
    {
        // 1. Instancia as nossas máquinas de sucatear HTML / XML
        $rssScraper = new \App\Scrapers\GoogleNewsRssScraper();
        $finepScraper = new \App\Scrapers\FinepScraper();

        $scrapers = [$rssScraper, $finepScraper];
        
        $totalNovos = 0;
        $totalAtualizados = 0;

        // 2. Loop principal: Vai em cada site
        foreach ($scrapers as $scraper) {
            
            // Vai na internet e devolve uma lista (array) de editais já formatados
            $editais = $scraper->scrape();
                        
            // 3. Loop secundário: Tratamento de dados (ETL: Extract, Transform, Load)
            foreach ($editais as $editalData) {
                try {
                    // Guarda o lixo original da página na íntegra. 
                    // Muito útil pra você debugar depois se um link tá vindo quebrado.
                    $editalData['payload_origem'] = $editalData;
                    
                    // Diz pro seu banco "Foi visto vivo agora mesmo"
                    $editalData['last_scanned_at'] = now();

                    // firstOrCreate é a armadura de "Upsert" do Laravel.
                    // Ele NUNCA duplica editais iguais (Mesmo codigo_externo)
                    // Ele NUNCA apaga dados que o Gemini IA já enriqueceu sobrescrevendo com null do HTML da Finep.
                    $edital = \App\Models\Edital::firstOrCreate(
                        ['codigo_externo' => $editalData['codigo_externo']], 
                        $editalData 
                    );

                    // 4. Contabilidade
                    if ($edital->wasRecentlyCreated) {
                        // Se for novinho em folha
                        $totalNovos++;
                    } else {
                        // Se já existia, a gente só "dá um bump" na hora que foi visto por último pra saber que não expirou.
                        $edital->update(['last_scanned_at' => now()]);
                        $totalAtualizados++;
                    }
                    
                } catch (\Throwable $e) {
                    // O try/catch blinda seu loop. Se o 3º edital falhar, o 4º continua de boa!
                    \Illuminate\Support\Facades\Log::error("Erro ao inserir/varrer: " . $e->getMessage());
                }
            }
        }
        
        // Cuspida oficial no relatorio do arquivo de logs (storage/logs/laravel.log)
        \Illuminate\Support\Facades\Log::info("Job Concluído no Backstage: {$totalNovos} novos, {$totalAtualizados} atualizados.");
    }
}
