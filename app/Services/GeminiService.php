<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * 🎓 DICA DE ESTUDO: 
 * Isto é um "Service" (Serviço). O Laravel não tem pastas Services por padrão,
 * a gente cria para guardar coisas que vão na internet (APIs Externas).
 * Imagina se a gente deixasse essa sujeirada de HTTP solta no meio do EditalController? Ia virar miojo!
 */
class GeminiService
{
    // Variáveis restritas dessa classe
    private string $key;
    private string $defaultModel;

    /**
     * Quando o Laravel invoca essa classe (no controller com "$gemini = new GeminiService"),
     * ele roda isso aqui primeiro. Puxamos a Senha Secreta da API do Google lá do arquivo oculto .env
     */
    public function __construct()
    {
        $this->key = env('GOOGLE_GEMINI_KEY') ?? env('OPEN_AI_KEY'); 
        // O Flash é o modelo mais barato, burrinho e rápido do Google. Perfeito e barato pra Extrair Títulos.
        $this->defaultModel = "gemini-flash-latest"; 
    }

    /**
     * O "Botão Mágico" de fazer perguntas.
     * Pode ser chamado passando 1 só texto (O padrão), ou forçando um modelo caro (Pro), ou forçando a chave do usuário logado.
     */
    public function generateContent(string $prompt, ?string $modelOverride = null, ?string $userApiKey = null)
    {
        // Se mandou substituir o modelo (tipo, mandou 'gemini-pro'), ele usa. Se não, usa o Flash ali de cima.
        $modelToUse = $modelOverride ?? $this->defaultModel;
        
        // Mesma coisa pra chave: Mandou chave na tela Perfil? Usa ela. Senão gasta do dono do AnjoNexus do (.env).
        $keyToUse = $userApiKey ?? $this->key;

        // Se não existir neeeenhuma chave informada
        if (empty($keyToUse)) {
            // Joga o erro (Catch) que devolve a mensagem ruborizada (vermelha) pro coitado do usuário.
            throw new Exception("Nenhuma chave de API (Gemini Key) configurada. Vá em 'Perfil' e adicione a sua chave.");
        }

        try {
            // Aqui a mágica da rede acontece. Bate no site da Google
            // timeout(60) = Se demorar mais de 60 segundos ele "corta" pra não estourar o limite de tempo do PHP Apache
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$modelToUse}:generateContent?key={$keyToUse}",
                ['contents' => [['parts' => [['text' => $prompt]]]]] // Formato bizarro exigido pelo Google SDK REST
            );

            // Bateu e voltou com erro? (Ex: Extourou o limite de graça de 15 mandos por minuto)
            if ($response->failed()) {
                $errorBody = $response->body();
                // Grava o erro nos logs de dentro do servidor do AnjoNexus (Pra você debugar chorando depois)
                Log::error("Gemini API Error [{$modelToUse}]: " . $errorBody);
                $errorMessage = $response->json()['error']['message'] ?? 'Erro desconhecido na API do Gemini';
                throw new Exception("Erro na IA: " . $errorMessage);
            }

            // O Google devolve um JSON enooorme (candidatos, moderação, filtros anti-racismo, etc).
            // Navegamos pelo buraco do array até chegar na string de textinho puro.
            $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            // Falso positivo
            if (!$text) {
                Log::error("Gemini API Empty Response [{$modelToUse}]: " . $response->body());
                throw new Exception("A IA não retornou conteúdo. Tente novamente em instantes.");
            }

            // Ufa. Retorna o texto lindo gerado pela Inteligência Artificial.
            return $text;

        } catch (Exception $e) {
            Log::error("Gemini Exception: " . $e->getMessage());
            throw $e; // Joga pra quem chamou o generator no controller se virar na tela.
        }
    }
}