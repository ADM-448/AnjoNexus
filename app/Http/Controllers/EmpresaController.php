<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 🎓 DICA DE ESTUDO: 
 * O EmpresaController é um CRUD clássico, mas focado na relação Um-para-Um.
 * Um usuário só pode ter UMA empresa. Por isso usamos 'firstOrCreate' e 'first'.
 */
class EmpresaController extends Controller
{
    /**
     * Mostra o formulário para preencher os dados da empresa (Página Meu Perfil Empresarial)
     */
    public function edit()
    {
        // 1. firstOrCreate é genial: 
        // Ele vai no banco tentar achar "A empresa do Usuário X".
        // O primeiro colchete diz O QUE PROCURAR. (ID do user)
        // O segundo colchete diz o que colocar caso NÃO EXISTA e tenha que criar uma vazia.
        $empresa = Empresa::firstOrCreate(
            ['user_id' => Auth::id()],
            ['razao_social' => null, 'cnpj' => null]
        );

        // Devolve o formulário de Editar Empresa passando a empresa (vazia ou não)
        return view('empresa.edit', compact('empresa'));
    }

    /**
     * Recebe o envio do Formulário quando o usuário aperta em "Salvar"
     */
    public function update(Request $request)
    {
        // Busca a empresa logada
        $empresa = Empresa::where('user_id', Auth::id())->first();

        // 1. Validação! (Bouncer de balada)
        // Verifica se cada campo enviado está de acordo com as regras antes de salvar.
        // ex: 'required' (Não pode tar vazio), 'max:255' (limite de letras)
        $validated = $request->validate([
            // Dados Basicos da Empresa
            'razao_social'          => 'required|string|max:255',
            // O unique abaixo impede de alguem botar um CNPJ que já está em uso na tabela empresas
            // vírgula + id da empresa = Permite ele ignorar se o CNPJ for o MESMO da própria empresa atualizando
            'cnpj'                  => 'required|string|max:20|unique:empresas,cnpj,' . $empresa->id,
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
            // Tese de Captação / Deck
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
            // Impactos Esperados
            'impacto_economico'         => 'nullable|string|max:2000',
            'impacto_social'            => 'nullable|string|max:2000',
            'impacto_ambiental'         => 'nullable|string|max:2000',
            'metricas_indicadores'      => 'nullable|string|max:2000',
            // Financeiro
            'tipo_recurso_interesse'        => 'nullable|string|max:100',
            'como_recurso_sera_utilizado'   => 'nullable|string|max:2000',
        ]);

        // 2. Salva
        // Se a validação passou limpa, aplica a variável '$validated' com tudo que tem dentro atualizando a empresa
        $empresa->update($validated);

        // 3. Devolve a página e mostra a mensagem bonitinha ('with' serve pra passar "Flash Messages" verdes)
        return redirect()->route('empresa.edit')->with('success', 'Dados da empresa atualizados com sucesso!');
    }
}
