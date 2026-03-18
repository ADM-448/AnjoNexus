<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEditalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo' => 'sometimes|string|max:255',
            'orgao' => 'nullable|string|max:255',
            'email_contato' => 'nullable|email|max:255',
            'modalidade' => 'nullable|string|max:255',
            'orcamento_global' => 'nullable|numeric',
            'publico_alvo' => 'nullable|string|max:255',
            'temas' => 'nullable|string',
            'trl_min' => 'nullable|string|max:10',
            'trl_max' => 'nullable|string|max:10',
            'data_abertura' => 'nullable|date',
            'data_encerramento' => 'nullable|date',
            'url_oficial' => 'nullable|url|max:255',
            'status' => 'nullable|in:Aberto,Encerrado,Em breve',
        ];
    }
}
