<?php

namespace App\Scrapers;

use App\Scrapers\Contracts\ScraperInterface;
use Goutte\Client;
use Illuminate\Support\Facades\Log;

class FinepScraper implements ScraperInterface
{
    /**
     * Varrer o site oficial de editais da Finep.
     */
    public function scrape(): array
    {
        $url = 'http://www.finep.gov.br/chamadas-publicas?situacao=aberta';
        $client = new Client();
        
        $resultados = [];

        try {
            // "Pede" o HTML da URL para o Goutte (ele simula um Browser!)
            $crawler = $client->request('GET', $url);

            // No Finep, eles usam a classe '.item' para cada edital no HTML.
            // Para encontrar o título: .item h3 a (o link com titulo)
            $crawler->filter('.item')->each(function ($node) use (&$resultados) {
                
                $tituloNode = $node->filter('h3 a');
                if ($tituloNode->count() === 0) return;

                $tituloString = trim($tituloNode->text());
                $linkHref = $tituloNode->attr('href');
                
                if (strpos($linkHref, 'http') === false) {
                    $linkHref = 'http://www.finep.gov.br' . $linkHref;
                }

                $partes = explode('/', $linkHref);
                $codigoExterno = end($partes);
                if (empty($codigoExterno)) {
                     $codigoExterno = md5($linkHref);
                }

                // Extraindo o Tema real do HTML
                $temasNode = $node->filter('.tema span');
                $temasText = $temasNode->count() > 0 ? trim($temasNode->text()) : 'Inovação Geral';

                // Extraindo Data de Publicação
                $dataNode = $node->filter('.data_pub span');
                $dataPub = $dataNode->count() > 0 ? \Carbon\Carbon::createFromFormat('d/m/Y', trim($dataNode->text()))->format('Y-m-d') : date('Y-m-d');

                $resultados[] = [
                    'titulo' => $tituloString,
                    'codigo_externo' => 'finep_' . $codigoExterno,
                    'orgao' => 'Finep',
                    'email_contato' => null, 
                    'modalidade' => 'Subvenção / Financiamento',
                    'orcamento_global' => null, 
                    'publico_alvo' => 'Empresas e ICTs',
                    'temas' => $temasText,
                    'trl_min' => null,
                    'trl_max' => null,
                    'data_abertura' => $dataPub, 
                    'data_encerramento' => null,
                    'status' => 'Aberto',
                    'url_oficial' => $linkHref,
                ];
            });

        } catch (\Exception $e) {
            Log::error("FinepScraper: Falha ao varrer Finep HTML: " . $e->getMessage());
        }

        return $resultados;
    }
}
