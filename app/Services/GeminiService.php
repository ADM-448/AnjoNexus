<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    private string $key;
    private string $defaultModel;

    public function __construct()
    {
        $this->key = env('GOOGLE_GEMINI_KEY') ?? env('OPEN_AI_KEY'); 
        // Atualizado para o padrão mais seguro do Google
        $this->defaultModel = "gemini-flash-latest"; 
    }

    // Agora o método aceita um segundo parâmetro opcional
    public function generateContent(string $prompt, ?string $modelOverride = null)
    {
        $modelToUse = $modelOverride ?? $this->defaultModel;

        try {
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$modelToUse}:generateContent?key={$this->key}",
                ['contents' => [['parts' => [['text' => $prompt]]]]]
            );

            if ($response->failed()) {
                $errorBody = $response->body();
                Log::error("Gemini API Error [{$modelToUse}]: " . $errorBody);
                $errorMessage = $response->json()['error']['message'] ?? 'Erro desconhecido na API do Gemini';
                throw new Exception("Erro na IA: " . $errorMessage);
            }

            $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$text) {
                Log::error("Gemini API Empty Response [{$modelToUse}]: " . $response->body());
                throw new Exception("A IA não retornou conteúdo. Tente novamente em instantes.");
            }

            return $text;

        } catch (Exception $e) {
            Log::error("Gemini Exception: " . $e->getMessage());
            throw $e;
        }
    }
}