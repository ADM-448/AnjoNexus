# Timeline de Execução e Rotas de Código (Passo a Passo)

Esta documentação traça de forma exata a trajetória cronológica e os arquivos ativados caso seguíssimos o fluxo do sistema como se fôssemos o computador ou o usuário final.

### Fase 1: O Despertar da Madrugada (A Coleta Passiva)
1. **O Agendador.** O servidor de hospedagem bate hora em hora ativando o serviço Cron do Laravel.
2. **O Comando [app/Console/Commands/ScrapeEditaisCommand.php](file:///c:/AnjoNexus/app/Console/Commands/ScrapeEditaisCommand.php)** acorda. 
3. **Instância dos Scrapers:** Ele vai na pasta `App/Scrapers` e invoca o `GoogleNewsRssScraper` e/ou `FinepScraper`.
4. Os Scrapers leem a rede mundial, baixam os arquivos XML e convertem para array. Retornam dados básicos: título, orgão, código de link ao provedor e data de lançamento.
5. De volta no `ScrapeEditaisCommand`, linha a linha o sistema varre a matriz. Executa-se `Edital::firstOrCreate()`. Se no banco de dados na Tabela Edital (`app/Models/Edital.php`) não tiver a chave exterior idêntica... **O Edital cru é plantado no Banco de dados.** Fim da fase passiva. Deixa-se o Edital dormindo aguardando.

### Fase 2: O Usuário Entra no Sistema (Radar)
6. O Cliente Loga. Seu browser viaja até `seusite.com/editais`.
7. **O Roteador:** O site entra por `public/index.php`, cruza pelo nosso famoso **`app/Http/Kernel.php`** (que certifica a sessão de Login) e, nos trilhos do arquivo `routes/web.php`, aponta para a rota index dos editais.
8. **Controller de Entrada:** Invoca `EditalController@index`.
9. Uma longa Query para o Banco de Dados resgata todos os deitais do mais novo ao mais velho, separados em 12 por página. Se o Usuário aplicar os Select Boxers para filtrar (Ex: só orgão = "CNPQ"), a Query dinamicamente faz um agrupamento do Eloquent. Envia a lista para view Blade `resources/views/editais/index.blade.php`. A tela está renderizada no browser do cliente.

### Fase 3: A IA é Acionada (Processo de Enriquecimento Invisível)
10. O cliente acha um título interessante entre centenas, e num botão, ele clica para Visualizar.
11. Rota de `GET: edital/{id}` chama o **`EditalController@show`**.
12. O controller puxa o Edital ID 12 do banco. Ele inspeciona: `Ele tem $edital->ia_enriquecido == true?`. Resposta é _Não_. 
13. O sistema paralisa a página que o usuário estava carregando por 4 segundos. Atrás das cortinas, chama o método private `enriquecerComIA($edital)`.
14. Esse método contata a nossa abstração técnica chamada `GeminiService`. Manda um comando complexo no prompt injetando título e órgão para o servidor externo Google, ordenando gerar JSON.
15. O JSON volta. Recebe decodificação nativa do array e injeta no banco de dados informações valiosíssimas na tabela Editais, dando ao $edital[id=12] público alvo e objetivos que acabaram de brotar da IA, trocando aquela flag ia pra _true_. 
16. Com tudo formatado, redireciona magicamente a leitura do edital como se nada desse processo maçante tivesse ocorrido.

### Fase 4: Do Mapeamento à Proposta (O Módulo Avançado)
17. Agora que o Edital foi enriquecido. O Cliente percebe a magnitude e a janela de caixa alto que ele provém pro seu próprio negócio. O Cliente clica em **"Gerar Proposta Analítica"**. 
18. Chamado via `web.php` dispara para `EditalController@analyzeIA()`. A plataforma junta tudo que tem e suplica a IA para desconstruí-lo num agrupamento metodológico em até 4 seções de perguntas. O Laravel vai no banco e cria N Modelos na tabela `editais_secoes` amarradas no `edital[id=12]` e N Modelos em `edital_perguntas`.
19. Redirecionamento leva o site para à Interface Dinâmica (O **Gerador de Propostas**). Caminho da requisição em `OpenAIController@index`. O banco injeta os dados de TRL/Risco da Empresa e funde a uma visão gráfica premium que simula uma tela de progressão hacker/sci-fi para o usuário.
20. O cliente clica o Botão Faturar. Fogo de artifício visual enquanto envia Request via AJAX (Frontend) ou POST(Backend) voltando ao `OpenAIController@generate`.
21. IA recebe o form, faz um parecer final detalhista e embasado cruzando Dados Do Formulario (Empresa) vs Dados Da Inteligência (Edital). Fato consubstanciado e documento salvo no banco por fim entregue pro cliente na aba superior "Avançar".

Isso tudo encerra o ciclo central da inteligência de Produto do AnjoNexus.
