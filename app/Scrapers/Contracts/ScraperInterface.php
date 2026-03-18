<?php

namespace App\Scrapers\Contracts;

interface ScraperInterface
{
    /**
     * Executa a varredura e retorna a lista de editais e oportunidades.
     * Deve retornar um array de resultados formatados prontos pra salvar no banco.
     *
     * @return array
     */
    public function scrape(): array;
}
