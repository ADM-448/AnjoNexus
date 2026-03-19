<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;

class OpenAIController extends Controller
{
    // Propriedades que guardam os serviços para uso na classe
    protected $gemini;

    // O Laravel injeta automaticamente as dependências quando o controller é criado.
    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    // -----------------------------------------------------------------------
    // PÁGINA PRINCIPAL DO GERADOR DE PROPOSTAS
    // Este método serve tanto para GET (abrir a página) quanto POST (gerar proposta).
    // É uma escolha de design: mantém tudo em uma rota só ao invés de criar duas.
    // -----------------------------------------------------------------------
    public function index(Request $request)
    {
        // Lista fixa de modelos disponíveis para o usuário escolher.
        // Está no código (hardcoded) para não precisar chamar a API do Google
        // só para buscar os modelos disponíveis — economiza tempo e dinheiro.
        $models = collect([
            ['id' => 'gemini-flash-latest', 'value' => 'Google Gemini 1.5 Flash (Rápido)'],
            ['id' => 'gemini-pro',          'value' => 'Google Gemini Pro (Complexo)']
        ]);

        // Só entra aqui se o usuário clicou em "Gerar" (método POST) E enviou um texto.
        // Se for só um GET (abrindo a página), pula esse bloco inteiro.
        if ($request->isMethod('post') && $request->search) {

            // Pega o modelo escolhido pelo usuário.
            $modelId = $request->get('model', 'gemini-flash-latest');

            // --- Lógica de Créditos ou Chave Própria ---
            $user = auth()->user();
            $userKey = $user->google_gemini_key ?? null;
            $usandoChavePropria = !empty($userKey);
            
            // Custo: Pro (3) vs Outros (Lite/Flash) (1)
            $custo = ($modelId === 'gemini-pro') ? 3 : 1;

            if (!$usandoChavePropria) {
                if ($user->creditos_ia < $custo) {
                    return back()->with('error', "Saldo insuficiente! Você tem {$user->creditos_ia} créditos e precisa de {$custo} para rodar este modelo. Vincule sua própria chave no Perfil para uso ilimitado!");
                }
                
                // Debita créditos antes do processamento
                $user->decrement('creditos_ia', $custo);
            }

            // Busca a empresa para contexto
            $empresa = \App\Models\Empresa::where('user_id', $user->id)->first();

            // Monta o prompt
            $finalPrompt = $this->buildPrompt($request, $empresa);

            // Chave do Cache (para não cobrar 2x se der F5)
            $key_search = "gemini_{$modelId}_" . md5($finalPrompt);

            try {
                if (cache()->has($key_search)) {
                    $info   = ["Info em cache (Gemini)"];
                    $result = cache()->get($key_search);
                    
                    // Reembolsa créditos se veio do cache (opcional, mas justo)
                    if (!$usandoChavePropria) {
                        $user->increment('creditos_ia', $custo);
                    }
                } else {
                    $result = $this->gemini->generateContent($finalPrompt, $modelId, $userKey);

                    // Se a IA não retornou nada, lança uma exceção para cair no catch abaixo.
                    if (!$result) {
                        throw new Exception("A IA não retornou um resultado válido.");
                    }

                    $info = ['API' => "Dados processados pela API do Google Gemini 🚀"];

                    // Salva o resultado no cache por 12 horas.
                    // cache()->add() só salva se a chave ainda não existir (evita sobrescrever).
                    cache()->add($key_search, $result, now()->addHours(12));
                }

                // Salva a proposta gerada no banco (cria Projeto + DocumentoGerado).
                $projetoSalvo = $this->saveProposal($empresa, $request->get('edital_id'), $result, $request->search);

                // Busca o objeto do edital se foi passado um edital_id na requisição.
                // O operador ternário evita erro caso edital_id não exista.
                $editalObj = $request->has('edital_id') ? \App\Models\Edital::find($request->edital_id) : null;

                // Redirecionamento (Padrão PRG - Post/Redirect/Get)
                // Isso evita que o usuário adicione versões duplicadas ao dar F5 na página.
                return redirect()->route('projetos.show', $projetoSalvo->id)
                    ->with('success', 'Nova versão da proposta gerada com sucesso pela IA!');

            } catch (Exception $e) {
                // Se qualquer coisa deu errado (API fora do ar, etc):
                // Loga o erro, volta para a página anterior mantendo o que o usuário tinha digitado,
                // e exibe a mensagem de erro na tela.
                Log::error("Erro no OpenAIController: " . $e->getMessage());
                return redirect()->back()
                    ->withInput()          // Mantém os campos do formulário preenchidos.
                    ->with('error', $e->getMessage());
            }
        }

        // -----------------------------------------------------------------------
        // FLUXO GET: Usuário só abriu a página (não clicou em gerar ainda).
        // -----------------------------------------------------------------------

        // Se veio um edital_id na URL (?edital_id=5), busca o edital para pré-selecionar.
        $editalObj = $request->has('edital_id') ? \App\Models\Edital::find($request->edital_id) : null;

        // Se tem um edital selecionado, cria um texto de exemplo para ajudar o usuário a começar.
        // Se não tem edital, deixa o campo vazio.
        $defaultSearch = $editalObj
            ? "Descreva o seu projeto focado no edital: {$editalObj->titulo}...\n\nExemplo:\n1. Nome do Projeto:\n2. Problema que resolve:"
            : "";

        // Abre a página em branco com os modelos disponíveis e o edital pré-selecionado (se houver).
        return view('openai.index', [
            "models"            => $models,
            "selectedEditalName"=> $editalObj->titulo ?? "",
            "editalObj"         => $editalObj,
            "defaultSearch"     => $defaultSearch // Texto de exemplo no campo de input.
        ]);
    }

    // -----------------------------------------------------------------------
    // MONTA O PROMPT COMPLETO QUE SERÁ ENVIADO PARA A IA
    // Recebe a requisição e a empresa do usuário (pode ser null).
    // O "?" antes de Empresa indica que o parâmetro pode ser null (nullable).
    // -----------------------------------------------------------------------
    private function buildPrompt(Request $request, ?\App\Models\Empresa $empresa): string
    {
        // Bloco 1: Contexto do edital (só adiciona se veio um edital_id).
        $editalContext = "";
        if ($request->has('edital_id')) {
            $edital = \App\Models\Edital::find($request->edital_id);
            if ($edital) {
                // Formata as informações do edital como texto estruturado para a IA entender.
                $editalContext = "EDITAL DE REFERÊNCIA:\n- Título: {$edital->titulo}\n- Órgão: {$edital->orgao}\n- Temas: {$edital->temas}\n- Modalidade: {$edital->modalidade}\n";
            }
        }

        // Bloco 2: Dados da empresa (só adiciona se o usuário tem empresa cadastrada).
        $empresaContext = "";
        if ($empresa && $empresa->razao_social) {
            $empresaContext = "\nDADOS DA EMPRESA: {$empresa->razao_social} ({$empresa->cnpj})\n" .
                             "TESE: {$empresa->solucao_proposta}\n" .
                             "PROBLEMA: {$empresa->problema_que_resolve}\n";
        }

        // Bloco 3: Instrução de comportamento — diz para a IA como ela deve agir.
        $systemInstructions = "Você é um consultor especialista em editais de inovação. Gere uma proposta formal em Markdown, estruturada com clareza e persuasão.";

        // Junta tudo em um prompt final estruturado em camadas:
        // [Como agir] + [Contexto do edital] + [Dados da empresa] + [O que o usuário digitou]
        return "{$systemInstructions}\n\n{$editalContext}\n{$empresaContext}\n\nPROPOSTA DO USUÁRIO:\n{$request->search}";
    }

    // -----------------------------------------------------------------------
    // SALVA A PROPOSTA GERADA NO BANCO DE DADOS
    // Cria (ou reutiliza) um Projeto e adiciona um novo DocumentoGerado a ele.
    // Retorna o projeto salvo ou null se algo impedir o salvamento.
    // -----------------------------------------------------------------------
    private function saveProposal(?\App\Models\Empresa $empresa, $editalId, string $result, string $search)
    {
        // Não salva se não tiver empresa ou edital — não faz sentido salvar sem vínculo.
        if (!$empresa || !$editalId) return null;

        $editalObj = \App\Models\Edital::find($editalId);

        // Se o edital_id não existe mais no banco, abandona.
        if (!$editalObj) return null;

        try {
            // ANTI-CLONES: firstOrCreate garante que existe apenas UM projeto
            // por combinação de empresa + edital.
            // Se já existe → retorna o existente. Se não existe → cria novo.
            // Primeiro array = condição de busca. Segundo array = valores para criar se não existir.
            $projeto = \App\Models\Projeto::firstOrCreate(
                [
                    'empresa_id' => $empresa->id,
                    'edital_id'  => $editalObj->id,
                ],
                [
                    // Str::limit() corta o título em 80 chars para não estourar o banco.
                    'titulo' => 'Proposta: ' . \Illuminate\Support\Str::limit($editalObj->titulo, 80),
                    'status' => 'Em rascunho',
                ]
            );


            // Cada geração cria um NOVO documento vinculado ao projeto.
            // Assim o usuário mantém o histórico de todas as versões geradas,
            // mesmo que o projeto (empresa+edital) já existisse.
            \App\Models\DocumentoGerado::create([
                'projeto_id'            => $projeto->id,
                'conteudo_ia'           => $result, // Texto completo gerado pela IA.
                'prompt_utilizado'      => $search, // O que o usuário digitou (para referência futura).
                'status'                => 'Pronto',
            ]);

            return $projeto;

        } catch (Exception $e) {
            // Se deu erro ao salvar, loga mas não quebra a aplicação.
            // O usuário ainda vê o resultado da IA na tela — só não foi salvo.
            Log::error('Erro ao salvar proposta: ' . $e->getMessage());
            return null;
        }
    }
}