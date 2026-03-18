<?php

namespace App\Http\Controllers;

use App\Models\Edital;
use App\Http\Requests\StoreEditalRequest;
use App\Http\Requests\UpdateEditalRequest;
use Illuminate\Http\Request;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class EditalController extends Controller
{
    // Propriedade que vai guardar o serviço da IA para usar em qualquer método da classe.
    protected $gemini;

    // O Laravel injeta automaticamente o GeminiService aqui quando o controller é criado.
    // Você não precisa chamar "new GeminiService()" em lugar nenhum — o Laravel faz isso sozinho.
    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    // -----------------------------------------------------------------------
    // LISTAGEM DE EDITAIS (com filtros dinâmicos e paginação)
    // -----------------------------------------------------------------------
    public function index(Request $request)
    {
        // Inicia um query builder sem executar nada ainda.
        // Permite adicionar filtros condicionalmente antes de ir ao banco.
        $query = Edital::query();

        // when() só adiciona o WHERE se o valor do filtro existir na requisição.
        // Se o usuário não preencheu o campo "busca", essa linha é ignorada completamente.
        // O LIKE "%texto%" encontra o valor em qualquer posição da coluna.
        $query->when($request->busca, fn($q, $busca) => $q->where('titulo', 'like', "%{$busca}%")->orWhere('temas', 'like', "%{$busca}%"));

        // Mesmo esquema: só filtra por status/orgao/modalidade se o usuário escolheu um.
        $query->when($request->status,    fn($q, $status) => $q->where('status', $status));
        $query->when($request->orgao,     fn($q, $orgao)  => $q->where('orgao', $orgao));
        $query->when($request->modalidade,fn($q, $mod)    => $q->where('modalidade', $mod));

        // Executa a query com ordenação e paginação de 12 itens por página.
        // withQueryString() mantém os filtros na URL ao trocar de página (?busca=x&status=y).
        $editais = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Busca os valores únicos de órgão e modalidade para popular os <select> de filtro.
        // distinct() evita repetições, whereNotNull() evita itens vazios, sort() ordena alfabeticamente.
        $orgaos     = Edital::select('orgao')->distinct()->whereNotNull('orgao')->pluck('orgao')->sort();
        $modalidades= Edital::select('modalidade')->distinct()->whereNotNull('modalidade')->pluck('modalidade')->sort();

        // Se a requisição veio com header "Accept: application/json" (ex: app mobile, fetch JS),
        // retorna JSON. Caso contrário, retorna a página HTML normalmente.
        // Isso evita precisar criar uma rota separada só para API.
        if ($request->wantsJson()) {
            return response()->json($editais);
        }

        // Envia os dados para a view Blade usando compact(),
        // que transforma as variáveis em um array ['editais' => $editais, ...].
        return view('editais.index', compact('editais', 'orgaos', 'modalidades'));
    }

    // -----------------------------------------------------------------------
    // ABRE O FORMULÁRIO DE CRIAÇÃO
    // -----------------------------------------------------------------------
    public function create()
    {
        // Apenas abre a view do formulário — sem dados para buscar.
        return view('editais.create');
    }

    // -----------------------------------------------------------------------
    // SALVA UM NOVO EDITAL NO BANCO
    // -----------------------------------------------------------------------
    public function store(StoreEditalRequest $request)
    {
        // StoreEditalRequest é uma classe separada que contém as regras de validação.
        // validated() retorna APENAS os campos que passaram na validação,
        // protegendo contra envio de campos não autorizados (mass assignment).
        $edital = Edital::create($request->validated());

        // Se for uma requisição de API, retorna o edital criado em JSON com status 201 (Created).
        if ($request->wantsJson()) {
            return response()->json($edital, 201);
        }

        // Redireciona para a listagem com uma mensagem de sucesso na sessão.
        return redirect()->route('editais.index')->with('success', 'Edital cadastrado com sucesso.');
    }

    // -----------------------------------------------------------------------
    // EXIBE UM EDITAL (com enriquecimento automático por IA)
    // -----------------------------------------------------------------------
    public function show(Edital $edital)
    {
        // O Laravel já buscou o edital pelo ID da URL automaticamente (Route Model Binding).
        
        // Se o edital ainda não passou pela IA (campo ia_enriquecido = false),
        // chama o método privado que usa o Gemini para preencher os campos faltantes.
        if (!$edital->ia_enriquecido) {
            $this->enriquecerComIA($edital);
        }

        // Ao invés de mostrar uma página de detalhes, redireciona direto para
        // o gerador de propostas com o edital já pré-selecionado.
        return redirect()->route('openai.index', ['edital_id' => $edital->id]);
    }

    // -----------------------------------------------------------------------
    // ENRIQUECE OS DADOS DO EDITAL USANDO IA (método interno/privado)
    // private = só pode ser chamado dentro dessa própria classe
    // -----------------------------------------------------------------------
    private function enriquecerComIA(Edital $edital): void
    {
        // Monta o prompt com os dados que já temos do edital.
        // Pede para a IA retornar APENAS JSON, sem markdown ou texto extra.
        $prompt = "Analise o título deste edital de inovação/fomento e extraia informações estruturadas.
        Título: \"{$edital->titulo}\"
        Órgão: \"{$edital->orgao}\"

        Retorne APENAS um JSON válido (sem markdown, sem ```), exatamente neste formato:
        {
          \"temas\": \"lista dos temas principais separados por vírgula\",
          \"objetivos\": \"descrição dos possíveis objetivos deste edital em 2-3 frases\",
          \"requisitos\": \"requisitos típicos para participar deste tipo de edital em 2-3 frases\",
          \"modalidade\": \"tipo de financiamento ou modalidade (ex: Subvenção, Financiamento, Bolsa, Encomenda)\",
          \"publico_alvo\": \"quem pode participar (ex: Startups, MPEs, ICTs, Empresas de Base Tecnológica)\"
        }
        Baseie-se no contexto brasileiro de editais de inovação. Seja específico e realista.";

        try {
            // Envia o prompt para o Gemini e recebe o texto bruto de resposta.
            $rawBody = $this->gemini->generateContent($prompt);

            // Se a IA não retornou nada, loga um aviso e abandona a função.
            if (!$rawBody) {
                Log::warning("IA Enriquecimento falhou para edital #{$edital->id}");
                return;
            }

            // ESTRATÉGIA 1: Tenta encontrar um objeto JSON { ... } dentro do texto bruto.
            // A regex lida com objetos aninhados graças ao (?R) que é recursão.
            if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $rawBody, $matches)) {
                $jsonString = $matches[0]; // Pega o JSON encontrado.
            } else {
                // ESTRATÉGIA 2 (fallback): Remove blocos de markdown ```json ... ```
                // que a IA às vezes coloca ao redor do JSON.
                $jsonString = preg_replace('/```json|```/', '', $rawBody);
            }

            // Converte o JSON em array PHP. O "true" é obrigatório para virar array (sem ele vira objeto).
            // trim() remove espaços e quebras de linha que possam ter sobrado nas bordas.
            $dados = json_decode(trim($jsonString), true);

            // Se o JSON era inválido e json_decode retornou null,
            // apenas marca o edital como "já processado" para não tentar de novo na próxima visita.
            if (!is_array($dados)) {
                $edital->update(['ia_enriquecido' => true]);
                return;
            }

            // Começa o array de atualização já com ia_enriquecido = true.
            // Isso garante que o campo seja marcado independente do que aconteça abaixo.
            $updateData = ['ia_enriquecido' => true];

            // BLINDAGEM: Cada campo só é atualizado se:
            // 1. A IA retornou um valor para ele (!empty)
            // 2. O campo está vazio OU contém um valor genérico padrão
            // Isso NUNCA sobrescreve dados que o usuário preencheu manualmente.

            if (!empty($dados['temas']) && (empty($edital->temas) || $edital->temas === 'Inovação Geral' || $edital->temas === 'Notícia sobre edital de inovação')) {
                $updateData['temas'] = $dados['temas'];
            }
            if (!empty($dados['objetivos']) && empty($edital->objetivos)) {
                $updateData['objetivos'] = $dados['objetivos'];
            }
            if (!empty($dados['requisitos']) && empty($edital->requisitos)) {
                $updateData['requisitos'] = $dados['requisitos'];
            }
            if (!empty($dados['modalidade']) && (empty($edital->modalidade) || $edital->modalidade === 'Geral' || $edital->modalidade === 'Oportunidade Listada (RSS)')) {
                $updateData['modalidade'] = $dados['modalidade'];
            }
            if (!empty($dados['publico_alvo']) && empty($edital->publico_alvo)) {
                $updateData['publico_alvo'] = $dados['publico_alvo'];
            }

            // Salva todos os campos atualizados no banco de uma vez.
            $edital->update($updateData);

            // Recarrega o objeto PHP com os dados frescos do banco.
            // Necessário porque update() salva no banco mas não atualiza o objeto em memória.
            $edital->refresh();

        } catch (\Exception $e) {
            // Se qualquer erro aconteceu, apenas loga e deixa a aplicação continuar.
            // O usuário não vê nenhum erro — o edital simplesmente fica sem enriquecimento.
            Log::warning("Exceção no enriquecimento IA do edital #{$edital->id}: " . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // ABRE O FORMULÁRIO DE EDIÇÃO
    // -----------------------------------------------------------------------
    public function edit(Edital $edital)
    {
        // O Laravel já buscou o edital pelo ID da URL (Route Model Binding).
        // Passa o edital para a view poder preencher os campos do formulário.
        return view('editais.edit', compact('edital'));
    }

    // -----------------------------------------------------------------------
    // ATUALIZA UM EDITAL EXISTENTE NO BANCO
    // -----------------------------------------------------------------------
    public function update(UpdateEditalRequest $request, Edital $edital)
    {
        // validated() garante que só os campos permitidos pelo UpdateEditalRequest sejam salvos.
        $edital->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json($edital);
        }

        return redirect()->route('editais.index')->with('success', 'Edital atualizado com sucesso.');
    }

    // -----------------------------------------------------------------------
    // DELETA UM EDITAL
    // -----------------------------------------------------------------------
    public function destroy(Edital $edital)
    {
        $edital->delete();

        // Aqui usa request() global ao invés de injetar Request no método,
        // mas o resultado é o mesmo — verifica se quer JSON.
        if (request()->wantsJson()) {
            // Status 204 = "No Content" — sucesso, mas sem corpo de resposta. Padrão REST para DELETE.
            return response()->json(null, 204);
        }

        return redirect()->route('editais.index')->with('success', 'Edital excluído com sucesso.');
    }

    // -----------------------------------------------------------------------
    // DISPARA A VARREDURA MANUAL DE NOVOS EDITAIS (scraping)
    // -----------------------------------------------------------------------
    public function manualScrape()
    {
        // Busca no cache quando foi a última vez que a varredura rodou.
        $lastRun = cache()->get('last_manual_scrape');

        // Se rodou há menos de 5 minutos, bloqueia e avisa o usuário.
        // Isso evita sobrecarregar o servidor com múltiplas varreduras seguidas.
        if ($lastRun && $lastRun > now()->subMinutes(5)) {
            return redirect()->route('editais.index')->with('info', 'A varredura foi realizada há menos de 5 minutos. Tente novamente em breve.');
        }

        // dispatchSync() executa o Job AGORA, na hora, sem fila de espera.
        // É como chamar o código do Job diretamente, mas de forma organizada.
        \App\Jobs\RunAllScrapersJob::dispatchSync();

        // Registra no cache o horário atual. O cache expira em 30 minutos automaticamente.
        cache()->put('last_manual_scrape', now(), now()->addMinutes(30));

        return redirect()->route('editais.index')->with('success', 'Varredura concluída! O Radar foi atualizado.');
    }

    // -----------------------------------------------------------------------
    // GERA A ESTRUTURA DE SEÇÕES DO EDITAL USANDO IA (chamado via botão na tela)
    // Diferença do privateAnalyzeIA: este é público e faz redirect com mensagem para o usuário.
    // -----------------------------------------------------------------------
    public function analyzeIA(Edital $edital)
    {
        // Monta o prompt pedindo um JSON com seções e perguntas típicas desse tipo de edital.
        $prompt = "Analise o título e contexto deste edital de inovação: '{$edital->titulo}'. Temas: {$edital->temas}. 
        Gere uma estrutura JSON de seções e perguntas que normalmente são pedidas em propostas para este tipo de edital (ex: Dados da Empresa, Descrição Técnica, Orçamento, Impacto).
        Retorne APENAS um JSON no formato: [{\"titulo\": \"Nome da Seção\", \"perguntas\": [\"Pergunta 1\", \"Pergunta 2\"]}, ...]. No máximo 4 seções.";

        try {
            $rawBody = $this->gemini->generateContent($prompt);

            // Aqui usa throw ao invés de return — porque é um método público que precisa
            // mostrar o erro para o usuário, não silenciar como o método privado faz.
            if (!$rawBody) {
                throw new \Exception("A IA não retornou uma resposta válida.");
            }

            // Mesma lógica de extração do JSON: tenta regex primeiro, remove markdown como fallback.
            if (preg_match('/\[(?:[^[\]]|(?R))*\]/s', $rawBody, $matches)) {
                $jsonString = $matches[0];
            } else {
                $jsonString = preg_replace('/```json|```/', '', $rawBody);
            }

            $secoesArray = json_decode(trim($jsonString), true);

            if (!is_array($secoesArray)) {
                throw new \Exception("O formato retornado pela IA é inválido.");
            }

            // Para cada seção retornada pela IA, cria no banco vinculada ao edital.
            foreach ($secoesArray as $index => $secaoData) {
                $secao = $edital->secoes()->create([
                    'titulo' => $secaoData['titulo'],
                    'ordem'  => $index + 1 // $index começa em 0, somamos 1 para ordem começar em 1.
                ]);

                // Para cada pergunta dentro da seção, cria vinculada à seção acima.
                foreach ($secaoData['perguntas'] as $qIndex => $perguntaTexto) {
                    $secao->perguntas()->create([
                        'texto' => $perguntaTexto,
                        'tipo'  => 'textarea', // Tipo fixo: sempre campo de texto longo.
                        'ordem' => $qIndex + 1
                    ]);
                }
            }

            // Redireciona de volta para o edital com mensagem de sucesso.
            return redirect()->route('editais.show', $edital)->with('success', 'Estrutura mapeada com sucesso!');

        } catch (\Exception $e) {
            // Se deu erro, loga e redireciona com mensagem de erro visível para o usuário.
            Log::error("Erro no analyzeIA: " . $e->getMessage());
            return redirect()->route('editais.show', $edital)->with('error', 'Erro ao processar IA: ' . $e->getMessage());
        }
    }
}