<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunAllScrapersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $rssScraper = new \App\Scrapers\GoogleNewsRssScraper();
        $finepScraper = new \App\Scrapers\FinepScraper();

        $scrapers = [$rssScraper, $finepScraper];
        
        $totalNovos = 0;
        $totalAtualizados = 0;

        foreach ($scrapers as $scraper) {
            $editais = $scraper->scrape();
                        
            foreach ($editais as $editalData) {
                try {
                    // Adiciona o payload bruto aos dados
                    $editalData['payload_origem'] = $editalData;
                    $editalData['last_scanned_at'] = now();

                    // Usa firstOrCreate para NUNCA sobrescrever dados bons com lixo de scraper
                    $edital = \App\Models\Edital::firstOrCreate(
                        ['codigo_externo' => $editalData['codigo_externo']], 
                        $editalData 
                    );

                    // Se não foi recém criado, atualizamos pelo menos a data de sincronização
                    if (!$edital->wasRecentlyCreated) {
                        $edital->update(['last_scanned_at' => now()]);
                    }
                    
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Erro ao inserir: " . $e->getMessage());
                }
            }
        }
        
        \Illuminate\Support\Facades\Log::info("RunAllScrapersJob executado. {$totalNovos} novos, {$totalAtualizados} atualizados.");
    }
}
