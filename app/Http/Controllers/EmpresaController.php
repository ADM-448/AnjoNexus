<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Empresa::firstOrCreate(
            ['user_id' => Auth::id()],
            ['razao_social' => '', 'cnpj' => '']
        );

        return view('empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Dados da Empresa
            'razao_social'          => 'required|string|max:255',
            'cnpj'                  => 'required|string|max:20',
            'porte'                 => 'nullable|string|max:50',
            'setor'                 => 'nullable|string|max:100',
            'estado'                => 'nullable|string|max:2',
            'n_funcionarios'        => 'nullable|integer|min:0',
            'faturamento_anual'     => 'nullable|string|max:100',
            // Representante Legal
            'telefone'              => 'nullable|string|max:20',
            'email_contato'         => 'nullable|email|max:255',
            'representante_legal'   => 'nullable|string|max:255',
            'cargo_representante'   => 'nullable|string|max:100',
            // Tese de Captação
            'problema_que_resolve'      => 'nullable|string|max:2000',
            'quem_e_impactado'          => 'nullable|string|max:1000',
            'solucao_proposta'          => 'nullable|string|max:2000',
            'como_funciona_na_pratica'  => 'nullable|string|max:2000',
            'estagio_solucao'           => 'nullable|string|max:50',
            'diferenciais'              => 'nullable|string|max:2000',
            'propriedade_intelectual'   => 'nullable|string|max:2000',
            'historico_inovacao'        => 'nullable|string|max:2000',
            'segmento_mercado'          => 'nullable|string|max:100',
            'publico_alvo_empresa'      => 'nullable|string|max:255',
            // Impactos
            'impacto_economico'         => 'nullable|string|max:2000',
            'impacto_social'            => 'nullable|string|max:2000',
            'impacto_ambiental'         => 'nullable|string|max:2000',
            'metricas_indicadores'      => 'nullable|string|max:2000',
            // Financeiro
            'tipo_recurso_interesse'        => 'nullable|string|max:100',
            'como_recurso_sera_utilizado'   => 'nullable|string|max:2000',
        ]);

        $empresa = Empresa::where('user_id', Auth::id())->first();
        $empresa->update($validated);

        return redirect()->route('empresa.edit')->with('success', 'Dados da empresa atualizados com sucesso!');
    }
}
