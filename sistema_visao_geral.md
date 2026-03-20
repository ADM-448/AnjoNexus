# Visão Geral do Sistema: AnjoNexus

O **AnjoNexus** é uma plataforma inteligente que automatiza a busca, análise e geração de propostas baseadas em Editais de fomento e inovação (como os da FINEP). Ele funciona como uma ponte entre as oportunidades de financiamento público/privado e empresas que buscam esse recurso, tirando do usuário o fardo de ler centenas de páginas burocráticas e redigir propostas do zero.

## O Produto Principal (O que o sistema faz?)
O produto resolve uma dor central: **escrever uma proposta para um edital é chato, demorado e arriscado.**
A plataforma automatiza isso em 3 grandes fases:

### 1. Descoberta (O "Radar")
O sistema possui robôs (Scrapers) que varrem a internet diariamente, de forma automatizada, em busca de novos editais lançados em fontes confiáveis (ex: FINEP, portais de inovação via Google News). O usuário acessa um painel onde vê todos esses editais listados, filtrados e organizados por status, órgão responsável e modalidade. 

### 2. Leitura e Enriquecimento Inteligente (A Mágica da IA)
Geralmente o título de um edital diz muito pouco e o arquivo real é gigantesco. Quando um novo edital e encontrado, o AnjoNexus envia esse conteúdo enxuto para uma Inteligência Artificial (Google Gemini). A IA funciona como um analista de investimentos: lê o contexto em segundos e mastiga as informações preenchendo automaticamente:
*   **Temas:** Do que trata o edital.
*   **Objetivos e Requisitos:** O que precisa ter para passar.
*   **Público-alvo:** Quem pode se inscrever.
*   **A Estrutura de Perguntas:** Automaticamente monta um formulário das seções do edital (Ex: "Fale sobre a Empresa", "O Orçamento", "O Risco Tecnológico")

### 3. Geração da Proposta
Uma vez que o Edital está mapeado, o usuário insere os dados da sua empresa ou do seu projeto e o AnjoNexus cruza as informações. O sistema pede à IA que escreva uma resposta argumentativa e persuasiva para cada pergunta do edital, baseando-se especificamente nos pontos fortes do projeto do usuário. O usuário revisa e pronto, tem um documento de aplicação gerado.

## O Modelo de Operação
Para viabilizar isso, o sistema trabalha com um misto de:
*   **Processos de Fundo (Backgrund/Cron):** O sistema não espera o usuário agir para buscar editais; ele faz isso sozinho de tempos em tempos.
*   **Consumo de APIs Externas:** 
    *   **IA:** Usa o Google Gemini como cérebro interpretativo.
    *   **Pagamentos:** Integra com MercadoPago para gerenciar cobranças, planos ou créditos que permitam o uso da plataforma de forma recorrente (SaaS).

## Para Quem é?
Startups, Empresas de Base Tecnológica, ICTS, Pesquisadores e Consultores de inovação. Pessoas que precisam monitorar dinheiro inteligente (smart money via fomento) de forma ativa e rápida.

---
**Resumo:** O sistema varre editais, a IA lê e explica o edital para o usuário, e a mesma IA ajuda o usuário a responder o edital de forma competitiva, ganhando tempo e assertividade.
