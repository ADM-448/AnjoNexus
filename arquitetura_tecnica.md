# Engenharia e Arquitetura do AnjoNexus

Esta documentação foca nos aspectos de Infraestrutura, Stack Tecnológica e Padrões de Design adotados por baixo do capô.

## Stack e Ferramentas
*   **Linguagem & Framework:** PHP ^8.x utilizando o framework **Laravel** (MVC - Model View Controller).
*   **Persistência de Dados (Banco):** Relacional (MySQL/PostgreSQL), utilizando Eloquent ORM. 
*   **Inteligência Artificial (Motor Semântico):** Serviço terceirizado via API do **Google Gemini** (acabado em camada abstrata de Service, no Laravel).
*   **Ferramenta de Pagamento:** **Mercado Pago API** com recepção de *Webhooks* para status de pagamento.

## Padrões de Práticas e Design

### 1. MVC com Forte Delegação ao Controller
A estrutura do projeto separa rigorosamente os domínios:
*   **Model (`app/Models`):** Guardam e representam tabelas do banco. Temos Entidades como: `Edital`, `EditalSecao` (1 Edital tem N Seções), `EditalPergunta` (1 Seção tem N perguntas), `User`, `Empresa` e `Payment`. Note o modelo altamente normalizado onde os formulários do edital não são guardados em strings infinitas e sim em pequenas porções de banco de dados.
*   **View (`resources/views`):** Templates Blade dinâmicos que sofrem processamento estático das variáveis do PHP provindas do controller. O site se utiliza muito de SSR (Server-Side Rendering) e as rotas Web dependem das visões Blade para existir.
*   **Controller (`app/Http/Controllers`):** Orquestra todas as ações. Eles foram montados usando a metodologia de Controllers de Recurso (`EditalController`), focado no CRUD e em integrações cruciais.

### 2. Service Pattern (Camada de Serviço)
No lugar de sobrecarregar os Controllers com a difícil e extensa lógica de conversar com o Google, foi embutida a pasta de *Services* (ex: `GeminiService`). O Controller (`EditalController`) depende da Injeção de Dependências feita pelo Kernel/Container do Laravel para automaticamente instanciar o `GeminiService` em seu construtor. Isso se caracteriza num baixo acoplamento.

### 3. Design de Form Requests
A aplicação não valida de forma porca dentro do Controller. As requisições (Formulários POST vindos do Frontend) passam antes pelo roteamento de validação `StoreEditalRequest` ou `UpdateEditalRequest`. Isso faz uma blindagem perimetral, bloqueando injeções ou *mass-assignment* antes mesmo de entrarem na regra de negócio do app.

### 4. Strategy e Polimorfismo nos Scrapers
Possivelmente a estrutura mais avançada de arquitetura de software no projeto encontra-se em `App\Scrapers\`. Ao invés de criar longas e difíceis classes de comandos para varrer a internet, foi adotado um padrão onde: 
*   Temos uma camada padronizada de coleta. 
*   Existem várias classes de coletores concretos: Ex: `GoogleNewsRssScraper` e `FinepScraper`. Elas realizam o mesmo contrato (o método `scrape()`) mas de maneiras totalmente díspares sob o capô. A máquina rodadora (`ScrapeEditaisCommand.php`) só faz o Iterador passar por cada uma recolhendo a caçada final, não interessando se o dev for criar +20 scrapers diferentes na semana que vem. O código ficará ileso.

### 5. Lógica de Tratamento de JSON via Expressão Regular 
Lidar com IA que "fala JSON e se porta como MarkDown" é um problema famoso na integração de aplicativos. A IA ocasionalmente envolve os JSONs de "enriquecimento de Edital" em tags ` ```json e ``` `.
O sistema usa uma abordagem defensiva com tratamento estrito em duas vias de Regex `(preg_match)` focadas em extração de subestruturas com profundidades variáveis para garantir um JSON limpo, decodando-o e revertendo para matriz sem gerar erro `500 Fatal Error`.

---

**Topologia da Aplicação:** É um sistema web clássico, monolítico, com integrações via API (tanto saída de request síncrono para Gemini, quanto recepção via porta aberta para o Mercado Pago). Possui um motor de jobs acionados via terminal para atualização cadenciada por CRON, resultando assim num sistema misto.
