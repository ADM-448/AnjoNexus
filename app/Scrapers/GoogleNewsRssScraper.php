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
        // Ampliei o termo para "edital inovação" para garantir que a gente sempre ache
        // notícias e você veja a mágica do scraper rodando na prática!
        $termos = urlencode('edital inovação');
        $url = "https://news.google.com/rss/search?q={$termos}&hl=pt-BR&gl=BR";

        $resultados = [];

        try {
            // Em vez de simplexml_load_file q as vezes o google bloqueia por faltar User-Agent,
            // usamos o Facade Http nativo do Laravel passando que somos um navegador!
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get($url);

            if (!$response->successful()) {
                Log::error("GoogleNewsRssScraper: Falha na requisição / Status: " . $response->status());
                return [];
            }

            // O PHP parseia o texto XML para um Object com base na string que o Http pegou
            $xml = simplexml_load_string($response->body());

            // O XML de RSS tem `<channel>` e dentro de channel vários `<item>`
            // Cada item é uma notícia.
            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $noticia) {
                    
                    // Converte os dados do XML para string (tipo seguro do PHP)
                    $tituloString = (string) $noticia->title;
                    $linkString = (string) $noticia->link;
                    // Hash o link pq ele é único e a url original vem camuflada no google news
                    $codigoExterno = md5($linkString);
                    // extrai publisher do title, google news bota publisher no final "- Publisher"
                    $publisher = explode(' - ', $tituloString);
                    $orgao = trim(end($publisher));
                    
                    if (empty($orgao)) {
                        $orgao = 'Google News / Governo';
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
