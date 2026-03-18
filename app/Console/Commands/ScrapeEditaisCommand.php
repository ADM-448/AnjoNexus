<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeEditaisCommand extends Command
{
    // Nome do comando que será digitado no terminal.
    // Exemplo de uso: php artisan editais:scrape
    protected $signature = 'editais:scrape';

    // Descrição que aparece quando você roda "php artisan list" no terminal.
    protected $description = 'Roda o web scraper inicial para buscar editais baseados em XML RSS';

    // -----------------------------------------------------------------------
    // MÉTODO PRINCIPAL — executa quando o comando é chamado no terminal.
    // É o equivalente ao index() de um controller, mas para comandos.
    // -----------------------------------------------------------------------
    public function handle()
    {
        // $this->info() imprime uma mensagem em VERDE no terminal.
        $this->info("Iniciando varredura em todos os Scrapers configurados...");

        // Instancia os scrapers disponíveis.
        // Cada scraper sabe buscar editais de uma fonte diferente.
        $rssScraper   = new \App\Scrapers\GoogleNewsRssScraper(); // Busca via Google News RSS
        $finepScraper = new \App\Scrapers\FinepScraper();         // Busca direto no site da FINEP

        // Coloca os scrapers num array para poder rodar todos com um loop.
        // Vantagem: para adicionar um novo scraper no futuro, basta incluir aqui.
        $scrapers = [$rssScraper, $finepScraper];

        // Array que vai acumular todos os editais encontrados por todos os scrapers.
        $editais = [];

        foreach ($scrapers as $scraper) {
            // get_class() retorna o nome da classe como string para exibir no terminal.
            // Exemplo: "App\Scrapers\FinepScraper"
            $this->info("Rodando scraper: " . get_class($scraper));

            // Chama o método scrape() de cada scraper e junta os resultados no array geral.
            // array_merge() combina o que já temos com o que o scraper atual retornou.
            $editais = array_merge($editais, $scraper->scrape());
        }

        // Se nenhum scraper retornou nada, avisa em AMARELO e encerra.
        // return 0 = terminou sem erro (convenção Unix: 0 = sucesso).
        if (empty($editais)) {
            $this->warn("Nenhum edital retornado na busca.");
            return 0;
        }

        $this->info("Sucesso! Encontrei " . count($editais) . " editais listados via Google News.");

        // Contador de editais novos para exibir no resumo final.
        $novos = 0;

        foreach ($editais as $editalData) {
            // O try/catch está DENTRO do loop propositalmente.
            // Se um edital com dado corrompido falhar, o próximo continua normalmente.
            try {
                // Salva o dado bruto original dentro do próprio registro como histórico.
                // Útil para debugar de onde veio cada informação no futuro.
                $editalData['payload_origem'] = $editalData;

                // Registra o momento exato em que esse edital foi encontrado.
                $editalData['last_scanned_at'] = now();

                // ANTI-DUPLICATA: firstOrCreate garante que o mesmo edital nunca é inserido duas vezes.
                // Primeiro array  → condição de busca: procura pelo codigo_externo (ID único do site de origem).
                // Segundo array   → dados para criar SE o edital ainda não existir no banco.
                // Se já existe    → retorna o existente sem alterar nada.
                // Se não existe   → cria um novo registro com os dados do scraper.
                $edital = \App\Models\Edital::firstOrCreate(
                    ['codigo_externo' => $editalData['codigo_externo']],
                    $editalData
                );

                // wasRecentlyCreated é uma propriedade do Eloquent.
                // Vale true se o registro acabou de ser criado agora nessa execução.
                // Vale false se já existia no banco antes.
                if ($edital->wasRecentlyCreated) {
                    $novos++;
                    // $this->line() imprime em BRANCO (cor padrão) no terminal.
                    $this->line("✨ Novo edital: {$editalData['titulo']}");
                } else {
                    // Se o edital já existia, apenas atualiza a data da última varredura.
                    // Não sobrescreve nenhum outro dado — protege informações já enriquecidas.
                    $edital->update(['last_scanned_at' => now()]);
                }

            } catch (\Throwable $e) {
                // Usa \Throwable ao invés de \Exception porque Throwable captura TUDO,
                // inclusive erros fatais do PHP que Exception não pegaria.
                // $this->error() imprime em VERMELHO no terminal.
                $this->error("Erro ao inserir: " . $e->getMessage());

                // Loga o erro no arquivo de log do Laravel para consulta posterior.
                \Illuminate\Support\Facades\Log::error("Erro ao inserir via comando: " . $e->getMessage());
            }
        }

        $this->info("Varredura Concluída. Mágica feita! {$novos} registros novos adicionados.");

        // Command::SUCCESS é uma constante do Laravel que vale 0.
        // É a forma mais legível de dizer "terminou com sucesso".
        return Command::SUCCESS;
    }
}