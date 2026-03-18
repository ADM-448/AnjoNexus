🚀 Anjo Inovador — Plataforma de Inteligência para Editais de Inovação
Manual de Projeto Versão: 0.3 | Data: Março 2026

NOTE

Contexto duplo: Este projeto é desenvolvido solo agora como produto real para a empresa de assessoria do autor. Em 6 meses, o time do TCC (5 pessoas) entra para co-desenvolver a versão acadêmica. O código feito agora é o ponto de partida do TCC.

📌 Visão Geral
O Anjo Inovador é um benefício governamental que financia o desenvolvimento de novas tecnologias. O problema central é que:

Os editais aparecem de forma dispersa e sem aviso em sites de governos e órgãos de fomento
O processo exige um volume enorme de documentação burocrática, cada edital com sua própria estrutura
Empresas sem consultoria especializada raramente conseguem participar
Nossa solução resolve isso em duas fases:

Fase	O que faz
Fase 1 — Radar	Varre a web e agrega os editais abertos (valor, prazos, elegibilidade)
Fase 2 — Gerador	A empresa/assessoria fornece dados; o sistema entrevista com as perguntas certas do edital e devolve a documentação já escrita
🎯 O Fluxo Central (Como Funciona na Prática)
Esta é a parte mais importante do sistema: ele não é um formulário genérico. Ele age como um entrevistador inteligente por edital.

┌─────────────────────────────────────────────────────────────────────┐
│                        FLUXO PRINCIPAL                              │
│                                                                     │
│  [Usuário seleciona um edital]                                      │
│           │                                                         │
│           ▼                                                         │
│  [Sistema carrega a estrutura daquele edital]                       │
│  (Cada edital tem suas seções e perguntas cadastradas)              │
│           │                                                         │
│           ▼                                                         │
│  [Sistema exibe as perguntas CERTAS para esse edital]               │
│  Ex: "Descreva a inovação tecnológica do projeto (máx 500 palavras)"│
│           │                                                         │
│           ▼                                                         │
│  [Empresa/assessoria responde cada pergunta no sistema]             │
│           │                                                         │
│           ▼                                                         │
│  [IA combina as respostas + dados da empresa + regras do edital]    │
│  e gera o texto final formatado para cada seção do documento        │
│           │                                                         │
│           ▼                                                         │
│  [Sistema devolve o documento pronto para revisão e download (PDF)] │
└─────────────────────────────────────────────────────────────────────┘
📋 Requisitos do TCC e Como o Projeto os Atende
Requisito Obrigatório	Como Atendemos
✅ Backend REST stateless	API Laravel (JSON), sem sessão no servidor
✅ Persistência relacional	MySQL com relacionamentos completos
✅ Frontend Web (HTML5+JS)	React ou Vue.js
✅ App Mobile	React Native (mesmo banco de componentes)
✅ Testes automatizados	PHPUnit (unitários) + Pest (integração)
✅ WebSocket (ao menos 1 caso de uso)	Notificação em tempo real quando novo edital é detectado
✅ Integração com API de pagamento	Stripe ou Mercado Pago (planos mensais)
✅ Deploy	VPS (AWS/DigitalOcean) ou Railway
✅ 1M escritas / 1M leituras (arquitetura)	Filas para geração de docs, cache Redis, índices DB
✅ CRUD completo	Editais, Empresas, Projetos, Documentos, Usuários
IMPORTANT

O regulamento proíbe uso abusivo de IA para gerar código. O papel da IA nesse projeto é do produto em si (gerar a documentação do cliente), não para escrever o sistema. Isso é um diferencial importante e precisa ser documentado explicitamente nas entregas.

🏗️ Arquitetura Geral
┌──────────────────────────────────────────────────────────────┐
│                      CLIENTES                                │
│          [App Mobile]          [Frontend Web]                │
│          React Native          React / Vue.js                │
└──────────────┬───────────────────────────┬───────────────────┘
               │ HTTPS / REST              │ WebSocket
               ▼                          ▼
┌──────────────────────────────────────────────────────────────┐
│                  BACKEND (Laravel API REST)                  │
│                                                              │
│  [Auth] [Editais] [Empresas] [Projetos] [Documentos] [Pag.] │
│                          │                                   │
│              [Filas — Laravel Queue]                         │
│          (Geração de documentos assíncrona)                  │
└───────────┬──────────────────────────┬───────────────────────┘
            │                          │
     ┌──────▼──────┐          ┌────────▼────────┐
     │    MySQL    │          │   Redis (Cache  │
     │  (Dados)   │          │   e Filas)      │
     └─────────────┘          └─────────────────┘
                                       │
                              ┌────────▼────────┐
                              │   Gemini API    │
                              │  (Geração docs) │
                              └─────────────────┘
Caso de Uso WebSocket
Quando o Radar de Editais (job agendado) encontra um novo edital, ele dispara um evento via WebSocket que aparece em tempo real no painel do usuário: 🔔 Novo edital detectado: FINEP Anjo Inovador – RS (R$ 500k).

📦 Módulos do Sistema
Módulo 1 — Radar de Editais
Job agendado (Laravel Scheduler, 1x/dia) que busca novos editais.

Dados coletados por edital:

Título, Órgão responsável (FINEP, SEBRAE, CNPq, FAPs estaduais)
Valor total e valor máximo por empresa
Tipos de empresa elegíveis (MEI, ME, EPP, etc.)
Setores/segmentos elegíveis
Datas de abertura e encerramento
Link para o edital oficial
Status: Aberto / Encerrado / Em breve
Estratégia de varredura escolhida: Scraper próprio (PHP) ✅

Cada fonte de dados terá seu próprio Scraper dedicado (uma classe PHP por site). O Laravel roda um Job agendado (1x/dia) que instancia cada scraper, coleta os dados, valida e salva no banco. Nenhuma IA envolvida na coleta — 100% determinístico.

Fontes mapeadas para o Anjo Inovador:

Site	Tecnologia de scraping	Observação
finep.gov.br	Goutte (PHP nativo)	HTML estático, fácil
sebraeinova.org.br	Goutte	HTML estático
gov.br/editais	Goutte	HTML estático
FAPs estaduais	Goutte ou Browsershot	Alguns usam JS
Google News (RSS)	PHP simplexml_load_file	Feed RSS, muito simples
Ferramentas PHP para scraping:

Goutte (composer require fabpot/goutte) — para sites HTML estáticos (sem JavaScript). Simples e rápido.
Browsershot (composer require spatie/browsershot) — para sites que carregam conteúdo com JavaScript. Usa o Chrome headless por baixo.
Google News RSS — para varredura genérica: https://news.google.com/rss/search?q=anjo+inovador+edital&hl=pt-BR devolve um XML gratuito com notícias recentes.
Arquitetura do Scraper:

app/
└── Scrapers/
    ├── Contracts/
    │   └── ScraperInterface.php   ← interface com método scrape(): array
    ├── FinepScraper.php
    ├── SebraeScraper.php
    ├── GovBrScraper.php
    └── GoogleNewsRssScraper.php
app/Jobs/
└── RunAllScrapersJob.php   ← orquestra todos os scrapers e salva no banco
Fluxo do Job diário:

Agendador (01:00 AM todo dia)
  └→ RunAllScrapersJob
       ├→ FinepScraper::scrape()     → array de editais
       ├→ SebraeScraper::scrape()   → array de editais
       ├→ GovBrScraper::scrape()    → array de editais
       └→ Para cada edital:
             └→ Verificar se já existe no banco (por URL ou título)
             └→ Se novo: salvar + disparar WebSocket (notificação)
             └→ Se existente: atualizar status (Aberto/Encerrado)
NOTE

Cada scraper deve ter tratamento de erro isolado: se o site do SEBRAE sair do ar, o job continua e coleta as outras fontes normalmente.

Módulo 2 — Estrutura de Editais (Admin)
Cada edital cadastrado pode ter sua estrutura documental configurada: quais seções o documento exige e quais perguntas devem ser feitas para preencher cada seção.

edital
  └── secoes[]
        └── "1. Descrição do Projeto"
              └── perguntas[]
                    ├── "Qual o problema que o projeto resolve?"
                    ├── "Descreva a solução proposta."
                    └── "Qual a inovação tecnológica?"
Módulo 3 — Perfil da Empresa
Dados cadastrados uma vez e reutilizados em múltiplos projetos.

Dados necessários:

Razão Social, CNPJ, Porte (MEI/ME/EPP/Médio/Grande)
Setor, Estado/Município, Data de fundação
Número de funcionários, Faturamento anual aproximado
Histórico de projetos de inovação
Módulo 4 — Projeto e Entrevista Guiada ⭐
O coração do sistema. Funciona em etapas:

Etapa 1 — Seleção do edital: O usuário escolhe para qual edital vai submeter o projeto.

Etapa 2 — Entrevista guiada: O sistema exibe as perguntas específicas daquele edital, uma seção por vez. O usuário responde em texto livre. Exemplos de perguntas:

"Descreva, em até 500 palavras, o problema que o seu projeto resolve."
"Qual é o diferencial tecnológico da sua solução em relação às já existentes no mercado?"
"Informe o valor total do investimento e a contrapartida da empresa."
Etapa 3 — Geração por IA: O sistema monta um prompt combinando:

As respostas do usuário
Os dados da empresa
As regras e requisitos do edital selecionado
Uma System Instruction que define o tom e formato exigido
Etapa 4 — Revisão e Download: O usuário revisa a documentação gerada, pode ajustar e faz o download em PDF.

Como a IA Gera os Documentos (System Instructions)
Exemplo de System Instruction para o Plano de Trabalho:

Você é um especialista em projetos de inovação com 20 anos de experiência
em editais do Anjo Inovador, FINEP e CNPq. Escreva um Plano de Trabalho
para submissão ao edital informado.
REGRAS OBRIGATÓRIAS:
1. Use linguagem formal exigida por órgãos governamentais brasileiros.
2. Estrutura obrigatória: Introdução → Objetivos → Metodologia →
   Cronograma (tabela) → Metas e Indicadores → Equipe.
3. Valores SEMPRE por extenso E em numeral: "R$ 50.000,00 (cinquenta mil reais)".
4. Use somente as informações fornecidas. Nunca invente dados.
5. Destaque a inovação tecnológica em todos os tópicos relevantes.
Módulo 5 — Pagamentos e Planos
Integração com Stripe ou Mercado Pago.

Plano	Preço	Limites
Gratuito	R$ 0	Ver editais. Sem geração de documentos.
Starter	R$ 97/mês	3 documentos/mês, 1 projeto ativo
Pro	R$ 297/mês	Ilimitado, 5 projetos, notificações
Agência	R$ 997/mês	Multi-empresa, personalização de documentos
🗃️ Esboço do Banco de Dados
users               → Autenticação
empresas            → Perfil da empresa (1:1 com user)
editais             → Editais mapeados pelo radar
edital_secoes       → Seções de cada edital (ex: "Plano de Trabalho")
edital_perguntas    → Perguntas por seção
projetos            → Projeto vinculado a edital + empresa
projeto_respostas   → Respostas da empresa para cada pergunta
documentos_gerados  → Saída da IA (texto gerado por seção)
planos              → Planos de assinatura
assinaturas         → Empresa ↔ plano ativo
pagamentos          → Histórico de cobranças
🛠️ Stack Tecnológica
Camada	Tecnologia
Backend API	Laravel (PHP) — REST stateless
Frontend Web	Vue.js ou React
App Mobile	Capacitor (empacota o app web para iOS/Android — sem reescrever código)
Banco de Dados	MySQL
Cache e Filas	Redis + Laravel Queue
WebSocket	Laravel Reverb (ou Pusher)
IA + Radar	Google Gemini API (geração de docs + Search Grounding para varredura)
Pagamentos	Stripe ou Mercado Pago
CI/CD	Jenkins (suporte oficial do TCC)
Deploy	Railway, Render ou VPS DigitalOcean
Versionamento	GitHub
TIP

Por que Capacitor? Você escreve o app web uma vez (Vue/React) e o Capacitor gera o .apk e .ipa nativos automaticamente. Para apps de formulários e dashboards (como este), o resultado é idêntico ao React Native com muito menos trabalho.

🗓️ Roadmap (Sprints de 2 Semanas)
Sprint 1 — Fundação
 Setup do repositório GitHub + Jira + CI/CD Jenkins
 Migrations e models principais
 CRUD de Editais (manual, para popular o banco)
 Autenticação (registro, login, perfis)
Sprint 2 — Radar e WebSocket
 Job de varredura de editais agendado
 WebSocket: notificação em tempo real de novo edital
 Painel público de editais com filtros
Sprint 3 — Perfil de Empresa e Projetos
 Cadastro de perfil da empresa
 Estrutura de seções e perguntas por edital
 Fluxo de entrevista guiada (pergunta por pergunta)
Sprint 4 — Geração por IA
 Integração Gemini API com System Instructions por tipo de documento
 Geração assíncrona via fila (Queue)
 Tela de revisão dos documentos gerados
Sprint 5 — Pagamentos e Mobile
 Integração Stripe/Mercado Pago
 Controle de planos e cotas
 App Mobile via Capacitor (empacotar o app web)
Sprint 6 — Qualidade e Deploy
 Testes unitários e de integração (PHPUnit/Pest)
 Geração de PDF (DomPDF)
 Deploy em produção
 Documentação da API REST (Swagger/Postman)
📎 Artefatos Obrigatórios (TCC)
 Diagrama de Casos de Uso
 Diagrama de Classes
 Diagramas de Sequência (cadastro, batch, integração IA, relatório)
 Diagrama Entidade-Relacionamento (DER)
 Glossário do Negócio
 System Design (arquitetura, escalabilidade 1M req/s)
 Especificação completa dos casos de uso
 Critérios de aceitação / cenários de teste
 Mockups das telas
 Documentação da API REST
✅ Decisões Finalizadas
Varredura: Gemini ou Scraper? Scraper PHP próprio (Goutte + Browsershot) ✅
Mobile: React Native ou Capacitor? Capacitor ✅
Escopo dos editais? Apenas Anjo Inovador ✅
Revisão humana? Documento vai direto ao cliente, sem revisão intermediária ✅
Modelo de negócio do MVP? Mensalidade que libera acesso à plataforma ✅
📝 Documento vivo — atualizar a cada sprint.


Plano de Estudos — Do Laravel Junior ao Pleno
Objetivo: Dominar tudo para construir a plataforma Anjo Inovador solo. Perfil atual: Laravel Junior | Duração estimada: 4 a 6 meses (1h a 2h/dia)

Como usar este plano
Siga as fases em ordem — cada fase depende da anterior.
O exercício prático de cada fase é sempre no próprio projeto Anjo Inovador.
Marque cada item com ✅ quando se sentir confortável com ele.
Fase 1 — Alicerce: PHP e Laravel com Profundidade
~3 a 4 semanas

PHP Moderno (não pode pular)
 Namespaces e Autoloading — o que é use, como o Composer carrega as classes
 Interfaces e Contratos — o que é implements e por que usar (você usará nos scrapers!)
 Traits — o que são e quando usar
 Arrow functions e closures — fn() =>, array_map, array_filter, collect()
 Tipagem forte — string, int, ?string (nullable), mixed
 Exceções customizadas — criar sua própria classe de exceção
Laravel Core
 Service Container (IoC) — como o Laravel injeta dependências automaticamente
 Service Providers — o que são e quando criar um próprio
 Eloquent avançado — scopes, accessors, mutators, casts
 Eloquent Relationships — hasMany, belongsTo, hasManyThrough, eager loading (with())
 Migrations — nullable(), foreign(), 
index()
, unique()
 Factories e Seeders — criar dados falsos para testes
 Form Requests — validação em classe separada (não no controller)
 Policies — controle de autorização (quem pode ver o quê)
 Artisan Commands — criar seus próprios comandos (php artisan make:command)
🎯 Exercício prático: Criar as migrations, models e seeders de editais, empresas, projetos.

Fase 2 — API REST com Laravel
~2 a 3 semanas

 Verbos HTTP e Status codes — GET/POST/PUT/DELETE, 200/201/400/401/403/422/500
 API Resources — php artisan make:resource — transformar models em JSON
 Laravel Sanctum — autenticação via token para APIs (não sessão!)
 Middleware customizado — verificar plano ativo do usuário
 Route Groups e Prefixos — organizar rotas em /api/v1/...
 Paginação — paginate() e cursorPaginate()
 Versionamento de API — por que versionar (/v1/, /v2/)
 Documentação de API — Swagger (L5-Swagger) ou Postman Collections
 Tratamento global de erros — Handler.php, retornar JSON padronizado
🎯 Exercício prático: Endpoints de editais (GET /api/v1/editais, GET /api/v1/editais/{id}) com Resource e Sanctum.

Fase 3 — Frontend Web com Vue.js + Mobile com Capacitor
~4 a 5 semanas

Vue.js 3 (Composition API)
 Composition API — ref(), reactive(), computed(), watch()
 v-if, v-for, v-model — diretivas fundamentais
 Componentes — props, emit, slots
 Vue Router — rotas no frontend e rotas protegidas (guards)
 Pinia — estado global (ex: usuário logado, editais carregados)
 Axios + Interceptors — token em toda requisição, tratar 401 globalmente
 Composables — reutilizar lógica: useAuth(), useEditais()
Capacitor (Mobile sem reescrever código)
 O que é Capacitor — empacota o app web como app nativo iOS/Android
 Setup — npm install @capacitor/core @capacitor/cli
 Build e sync — npx cap sync, npx cap open android
 Diferenças web vs. app — CORS, cookies, links externos
🎯 Exercício prático: Tela de listagem de editais em Vue.js consumindo a API da Fase 2.

Fase 4 — Web Scraping com PHP
~2 a 3 semanas

A parte mais exclusiva do projeto. Você constrói os robôs coletores.

Fundamentos
 Inspecionar o HTML — F12 no navegador, entender a árvore DOM
 Seletores CSS — .classe, #id, div > span, [attr=value]
 XPath — alternativa mais poderosa para estruturas complexas
 Site estático vs. dinâmico — por que o Goutte não funciona em sites com JavaScript
Goutte (sites estáticos)
 Instalação — composer require fabpot/goutte
 $crawler->filter()->text() e ->attr() e ->each()
 Tratamento de erros — try/catch quando o site muda ou está fora
Browsershot (sites com JavaScript)
 Instalação — composer require spatie/browsershot + Puppeteer
 Como funciona — Chrome headless renderiza o JS, então extrai o HTML
 waitUntilNetworkIdle() — aguardar carregamento completo
Google News RSS (mais simples, começa aqui)
 O que é RSS — feed XML gratuito
 simplexml_load_file() — ler XML no PHP
 URL do Google News: https://news.google.com/rss/search?q=anjo+inovador+edital&hl=pt-BR&gl=BR
Arquitetura dos Scrapers
 Interface PHP — ScraperInterface com scrape(): array
 Classes concretas — FinepScraper, SebraeScraper implementando a interface
 Logs — Log::info() e Log::error() para rastrear coletas
 Deduplicação — firstOrCreate() para não salvar o mesmo edital duas vezes
🎯 Exercício prático: Criar o GoogleNewsRssScraper que busca "anjo inovador edital" e retorna um array de resultados.

Fase 5 — Laravel Avançado: Filas, WebSocket e Pagamentos
~3 a 4 semanas

Filas (Queues)
 Por que usar — geração de docs demora; não pode travar a requisição do usuário
 php artisan make:job — criar um Job
 dispatch() — despachar para a fila
 Drivers — database (simples) e Redis (produção)
 php artisan queue:work — o worker que processa
 Horizon — painel visual para monitorar filas
Agendamento (Scheduler)
 $schedule->command()->dailyAt('01:00') — agendar o scraper
 Cron em produção — o servidor chama o Scheduler do Laravel
WebSocket com Laravel Reverb
 O que é WebSocket — conexão persistente (diferente do REST)
 php artisan install:broadcasting
 php artisan make:event NovoEditalDetectado
 broadcast(new NovoEditalDetectado($edital))
 Vue.js: Echo.channel('editais').listen(...) — receber em tempo real
Pagamentos
 Laravel Cashier — composer require laravel/cashier (abstrai o Stripe)
 Webhooks — o Stripe avisa seu sistema quando pagamento é confirmado
 Middleware de plano — verificar se assinatura está ativa antes de liberar rota
🎯 Exercício prático: Job assíncrono que gera documento e retorna "Em processamento..." imediatamente ao usuário.

Fase 6 — Testes Automatizados, Escalabilidade e Deploy
~2 a 3 semanas

Testes
 Unitário vs. Integração — a diferença conceitual
 Pest — sintaxe mais simples que PHPUnit puro
 RefreshDatabase — banco de teste separado
 Testar a API — $this->getJson('/api/v1/editais') e verificar resposta
 Mockar HTTP — testar scrapers sem chamar o site real
Escalabilidade (Requisito TCC: 1M req/s)
 Índices no MySQL — EXPLAIN para verificar performance
 Redis como cache — Cache::remember() para não bater no banco sempre
 Rate Limiting — throttle nas rotas da API
 Load Balancer — conceito de múltiplos servidores com balanceador
Deploy
 .env em produção — nunca sobe para o GitHub
 config:cache, route:cache, view:cache — otimizações
 Railway ou Render — deploy conectando ao GitHub
 Jenkins / GitHub Actions — CI/CD: testes automáticos no push
 Supervisor — manter queue:work rodando eternamente em produção
Resumo Visual
[Você agora]                        [Em ~5 meses]
Laravel Junior          →           Laravel Pleno
   Fase 1: PHP + Laravel sólido       (4 semanas)
   Fase 2: REST API profissional       (3 semanas)
   Fase 3: Vue.js + Capacitor          (5 semanas)
   Fase 4: Web Scraping PHP            (3 semanas)
   Fase 5: Filas + WebSocket + Pag.    (4 semanas)
   Fase 6: Testes + Deploy             (3 semanas)
                             Total: ~22 semanas
Recursos Recomendados
Tópico	Recurso
Laravel	Laracasts — melhor plataforma do mundo
PHP moderno	PHP The Right Way
Vue.js 3	Documentação oficial
Goutte	GitHub FriendsOfPHP/Goutte
Pest	pestphp.com
Capacitor	capacitorjs.com/docs
TIP

A melhor forma de aprender é construindo. Aplique cada fase diretamente no projeto Anjo Inovador. Errar, buscar e corrigir fixa muito mais do que só assistir aulas.