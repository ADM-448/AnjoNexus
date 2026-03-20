# AnjoNexus

Bem-vindo ao repositório do **AnjoNexus**, uma plataforma inteligente de automação de busca, análise e elaboração de propostas para Editais de fomento, subvenção e inovação. 

## O que é o AnjoNexus?
O AnjoNexus atua como uma ponte entre as oportunidades de financiamento (público e privado) e as empresas inovadoras. Ele resolve a maior dor de captação de recursos: **o tempo perdido na leitura de dezenas de páginas burocráticas e na formulação de respostas do zero**. 

O sistema utiliza automação (Scrapers) para varrer editais diariamente e processamento de linguagem baseada em Inteligência Artificial para ler, traduzir e gerar os argumentos preenchendo os formulários baseado no perfil inovador da própria empresa.

## Documentação Oficial 


## Tecnologias e Stack
- **Linguagem:** PHP 8.2 (Laravel MVC) e JavaScript.
- **Banco de Dados:** MySQL (via Eloquent ORM).
- **APIs Externas:** Google Gemini e Mercado Pago.
- **Frontend:** TailwindCSS e Blade Templates compilados via Vite.

## Instalação e Setup
1. Clone o repositório.
2. Copie o arquivo `.env.example` para `.env` e ajuste suas chaves de API.
3. Instale os pacotes: `composer install` e `npm install`
4. Gere a chave do framework e inicie o banco: `php artisan key:generate` e `php artisan migrate`
5. Inicie o servidor: `php artisan serve` e `npm run dev`

---
*AnjoNexus — Gestão e Inteligência para Captação de Fomento.*
