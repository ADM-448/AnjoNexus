👤 1. User (User.php)
🎯 Objetivo / Responsabilidade: Gerenciar a autenticação, segurança e acesso à plataforma. É a "pessoa física" por trás do teclado.

🔗 Relacionamentos:

empresa() (hasOne): Um usuário possui apenas uma conta de Startup.

⚙️ Destaques Técnicos: É o único model que usa $fillable (Lista Branca) por questões de segurança nativa do Laravel. Também gerencia os Tokens de API (HasApiTokens).

🏢 2. Empresa (Empresa.php)
🎯 Objetivo / Responsabilidade: Guardar o "Currículo" da Startup. É aqui que moram o CNPJ, o Pitch, os impactos gerados e a tese do negócio. É a principal fonte de contexto para o Gemini.

🔗 Relacionamentos:

user() (belongsTo): Sabe exatamente quem é o dono (pessoa física) desta conta.

projetos() (hasMany): Uma empresa pode se candidatar a dezenas de editais diferentes ao longo do tempo.

⚙️ Destaques Técnicos: Usa $guarded = ['id'], o que facilita muito a vida quando você adiciona colunas novas ao perfil, não precisando atualizar o código do Model toda vez.

🎯 3. Edital (Edital.php)
🎯 Objetivo / Responsabilidade: O coração do "Radar". Representa a oportunidade de fomento, a verba disponível e as regras gerais do jogo.

🔗 Relacionamentos:

secoes() (hasMany): O edital se divide em várias partes/capítulos para organizar a proposta.

projetos() (hasMany): Um edital mega concorrido pode receber candidaturas (projetos) de centenas de empresas diferentes.

⚙️ Destaques Técnicos: Brilha pelo uso inteligente do $casts. Ele converte sozinho o JSON feio do banco de dados em um array do PHP, e transforma a data de varredura (last_scanned_at) em um objeto de tempo manipulável.

📑 4. EditalSecao (EditalSecao.php)
🎯 Objetivo / Responsabilidade: Estruturar o edital em "blocos lógicos" mapeados pela IA. (Ex: "1. Dados da Empresa", "2. Orçamento", "3. Equipe Técnica").

🔗 Relacionamentos:

edital() (belongsTo): Sabe a qual edital pertence.

perguntas() (hasMany): Cada bloco de seção contém várias perguntas específicas dentro dele.

❓ 5. EditalPergunta (EditalPergunta.php)
🎯 Objetivo / Responsabilidade: A granularidade máxima da estrutura. É a "prova" que a startup tem que responder. (Ex: "Qual o valor total solicitado para maquinário?").

🔗 Relacionamentos:

secao() (belongsTo): Sabe de qual seção (bloco lógico) ela faz parte.

🌉 6. Projeto (Projeto.php) — O Ponto de Encontro
🎯 Objetivo / Responsabilidade: É a "Candidatura" ou o "Match". É o Model mais importante do ponto de vista de negócios, porque ele une a Fome (A Empresa precisando de dinheiro) com a Vontade de Comer (O Edital oferecendo dinheiro).

🔗 Relacionamentos:

empresa() (belongsTo): Quem está pedindo o dinheiro.

edital() (belongsTo): De onde vem o dinheiro.

respostas() (hasMany): As respostas manuais ou rascunhos que a empresa digitou.

documentosGerados() (hasMany): Os textos finais polidos que a IA gerou para este projeto.

✍️ 7. ProjetoResposta (ProjetoResposta.php)
🎯 Objetivo / Responsabilidade: Guardar o insumo humano. Se a startup quiser digitar manualmente uma justificativa antes de pedir para a IA melhorar, esse texto fica salvo aqui.

🔗 Relacionamentos:

projeto() (belongsTo): Pertence a uma candidatura específica.

pergunta() (belongsTo): Aponta exatamente para qual pergunta do edital esta resposta está servindo.

🤖 8. DocumentoGerado (DocumentoGerado.php)
🎯 Objetivo / Responsabilidade: O "Produto Final" da sua plataforma. Guarda as minutas de alta qualidade escritas pelo Gemini, prontas para serem exportadas em Word/PDF.

🔗 Relacionamentos:

projeto() (belongsTo): Todo documento gerado pertence a uma candidatura.

secao() (belongsTo - Opcional): Pode pertencer a uma seção específica (ex: "Aqui está o texto só da parte do Orçamento") ou pode ser NULL (Uma Proposta Geral do edital inteiro).