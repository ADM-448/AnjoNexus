<?php

namespace App\Scrapers;

use App\Scrapers\Contracts\ScraperInterface;
use Illuminate\Support\Facades\Log;

class GoogleNewsRssScraper implements ScraperInterface
{
    /**
     * Busca as notícias do Google News sobre editais do Anjo Inovador.
     * É um ótimo ponto de partida porque retorna um feed XML puro e determinístico.
     */
    public function scrape(): array
    {
        // Alterei a busca para algo bem mais específico e técnico (termos de governo)
        // Isso ajuda a trazer resultados mais próximos de editais reais.
        $termos = urlencode('site:gov.br ("extrato de edital" OR "chamada pública") inovação');
        $url = "https://news.google.com/rss/search?q={$termos}&hl=pt-BR&gl=BR";

        $resultados = [];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get($url);

            if (!$response->successful()) {
                Log::error("GoogleNewsRssScraper: Falha na requisição / Status: " . $response->status());
                return [];
            }

            $xml = simplexml_load_string($response->body());

            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $noticia) {
                    
                    $tituloString = (string) $noticia->title;

                    // FILTRO DE QUALIDADE: 
                    // Se não tiver palavras-chave no título, provavelmente é só uma notícia/matéria
                    // Queremos reduzir o ruído para o TCC do usuário.
                    $lowTitle = mb_strtolower($tituloString);
                    if (!str_contains($lowTitle, 'edital') && 
                        !str_contains($lowTitle, 'chamada') && 
                        !str_contains($lowTitle, 'concurso')) {
                        continue;
                    }

                    $linkString = (string) $noticia->link;
                    $codigoExterno = md5($linkString);
                    $publisher = explode(' - ', $tituloString);
                    $orgao = trim(end($publisher));
                    
                    if (empty($orgao)) {
                        $orgao = 'Portal Transparência / Gov';
                    }

                    $resultados[] = [
                        'titulo' => $tituloString,
                        'codigo_externo' => 'googlenews_' . $codigoExterno,
                        'orgao' => $orgao,
                        'email_contato' => null, // GNews não provê isso
                        'modalidade' => 'Oportunidade Listada (RSS)',
                        'orcamento_global' => null,
                        'publico_alvo' => null,
                        'temas' => 'Notícia sobre edital de inovação',
                        'trl_min' => null,
                        'trl_max' => null,
                        // Data de publicação do RSS "pubDate" convertida p BD
                        'data_abertura' => date('Y-m-d', strtotime((string)$noticia->pubDate)),
                        'data_encerramento' => null, // não se sabe só lendo noticia
                        'status' => 'Em breve', // Oportunidade via news sempre entra pendente
                        'url_oficial' => $linkString,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("GoogleNewsRssScraper: Ocorreu um erro no parsing do XML: " . $e->getMessage());
        }

        return $resultados;
    }
}
