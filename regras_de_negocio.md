# Regras de Negócio: AnjoNexus

Este documento detalha **por que as coisas acontecem**, as restrições impostas aos usuários para evitar abusos e as lógicas de fluxo das entidades no AnjoNexus.

## 1. Regra de Coleta de Editais (Varredura)
*   **Autonomia:** A aplicação busca os editais sem a necessidade de intervenção do usuário por meio do [ScrapeEditaisCommand](file:///c:/AnjoNexus/app/Console/Commands/ScrapeEditaisCommand.php#7-111). 
*   **Restrição Anti-Abuso (Cooldown):** Há um botão no Dashboard que permite aos usuários forçarem o sistema a buscar editais ("Varredura Manual"). Como esse processo sobrecarrega a máquina, existe uma trava de negócio que impede que o sistema seja trigado manualmente mais de uma vez a cada **5 minutos**.
*   **Unicidade Institucional (Anti-Duplicata):** Editais coletados da FINEP ou Google News possuem um identificador único (o `codigo_externo`). No banco de dados, caso o sistema baixe um edital pela segunda vez, ele **não será duplicado**, evitando poluir o Radar do usuário. Apenas a sua data de última visualização (`last_scanned_at`) será atualizada.

## 2. Regra de Enriquecimento de Dados
*   **Recurso Escasso:** Perguntar à IA custa tempo de servidor e chamadas de API (dinheiro).
*   **Regra de Processamento Único:** Todo Edital descoberto pela plataforma nasce "cru". Assim que o *primeiro* usuário clica para ver os detalhes daquele edital, o sistema dispara uma requisição para a Inteligência Artificial. A IA lê e enriquece os campos (objetivos, público alvo, prazos). 
*   **Persistência e Blindagem:** A resposta da IA é salva no banco de dados com a flag `ia_enriquecido = true`. Quando um segundo usuário acessar o mesmo edital, o sistema não consulta mais a IA. Ele carrega a versão já traduzida do banco de dados relacional.
*   **Hierarquia de Dados:** A IA só preenche campos que estão **vazios**. Caso um administrador humano altere as informações de um edital no banco de dados, a IA não sobrescreve os dados revisados pelo humano, blindando curadorias manuais.

## 3. Regra de Análise Estrutural do Formulário
*   Cada edital tem regras diferentes de submissão (alguns pedem 10 seções, outros 3).
*   Quando o usuário decide gerar uma proposta, a IA faz a secção morfológica do edital limitando a geração e fatiando o texto em, no máximo, **4 seções estruturais**. Por quê? Para garantir que a aplicação não se perca processando dados massivos numa única chamada, gerando timeouts na plataforma, garantindo performance e precisão textual para cada pequena seção.

## 4. Regra de Monitização (Monetização)
*   **Integração:** Realizada via Mercado Pago (Webhooks).
*   A confirmação de crédito ou desbloqueio de geração só ocorre via notificação oficial do Mercado Pago no sistema servidor. O usuário não tem controle local sobre esse fluxo, impossibilitando fraudes na tela, pois a autorização bate diretamente no `PaymentController`.

## 5. Regra de Contexto e Proximidade de Geração
*   Ao gerar uma proposta, a IA não cria uma resposta estática generalista. Uma regra fundamental do sistema é mesclar dois vetores:
    *   **Vetor 1:** O que o edital está pedindo.
    *   **Vetor 2:** O cadastro das características da empresa (tecnologia, estágio, receita, histórico de inovação).
*   Mecanismo: O prompt fornecido para o LLM instrui que não existam fantasias: a IA é forçada a formatar o portfólio do usuário nos moldes que o órgão governamental quer ler. 

> [!NOTE] 
> O Produto não cria mentiras para aprovar o cliente final nas chamadas públicas (algo eticamente e penalmente inviável), ele estrutura e realça pontos chaves factuais da empresa com base nos pesos que determinado edital demonstra ter na semântica identificada pela IA.
