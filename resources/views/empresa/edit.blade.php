<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Perfil da Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
                    <p class="font-bold">✅ Sucesso</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('empresa.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- SEÇÃO 1: DADOS DA EMPRESA --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="bg-indigo-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">1</span>
                            Dados da Empresa
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Informações básicas de identificação</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="razao_social" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Razão Social *</label>
                                <input type="text" name="razao_social" id="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}" required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Razão social completa">
                                @error('razao_social') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="cnpj" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">CNPJ *</label>
                                <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj', $empresa->cnpj) }}" required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="00.000.000/0000-00">
                                @error('cnpj') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="porte" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Porte</label>
                                <select name="porte" id="porte" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach(['MEI', 'ME', 'EPP', 'Médio Porte', 'Grande Porte'] as $p)
                                        <option value="{{ $p }}" {{ old('porte', $empresa->porte) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="setor" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Setor de Atuação</label>
                                <input type="text" name="setor" id="setor" value="{{ old('setor', $empresa->setor) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Ex: Tecnologia, Saúde...">
                            </div>
                            <div>
                                <label for="estado" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Estado (UF)</label>
                                <select name="estado" id="estado" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">UF</option>
                                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                        <option value="{{ $uf }}" {{ old('estado', $empresa->estado) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="n_funcionarios" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nº Funcionários</label>
                                <input type="number" name="n_funcionarios" id="n_funcionarios" value="{{ old('n_funcionarios', $empresa->n_funcionarios) }}" min="0"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="15">
                            </div>
                            <div>
                                <label for="faturamento_anual" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Faturamento Anual</label>
                                <input type="text" name="faturamento_anual" id="faturamento_anual" value="{{ old('faturamento_anual', $empresa->faturamento_anual) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="R$ 500.000">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEÇÃO 2: REPRESENTANTE LEGAL --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="bg-indigo-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">2</span>
                            Representante Legal
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dados do responsável pela empresa</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="representante_legal" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nome do Representante</label>
                                <input type="text" name="representante_legal" id="representante_legal" value="{{ old('representante_legal', $empresa->representante_legal) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Nome completo">
                            </div>
                            <div>
                                <label for="cargo_representante" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Cargo</label>
                                <input type="text" name="cargo_representante" id="cargo_representante" value="{{ old('cargo_representante', $empresa->cargo_representante) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Ex: CEO, Diretor, Sócio">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email_contato" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">E-mail de Contato</label>
                                <input type="email" name="email_contato" id="email_contato" value="{{ old('email_contato', $empresa->email_contato) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="contato@empresa.com">
                            </div>
                            <div>
                                <label for="telefone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Telefone</label>
                                <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $empresa->telefone) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="(00) 00000-0000">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEÇÃO 3: TESE DE CAPTAÇÃO / SOBRE O PROJETO --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="bg-indigo-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">3</span>
                            Tese de Captação
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Descreva o problema, a solução e o estágio do seu projeto. A IA usará isso para personalizar propostas.</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="problema_que_resolve" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Qual problema ou desafio o seu projeto busca resolver?</label>
                            <textarea name="problema_que_resolve" id="problema_que_resolve" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Explique de forma clara a dor ou dificuldade que existe...">{{ old('problema_que_resolve', $empresa->problema_que_resolve) }}</textarea>
                        </div>
                        <div>
                            <label for="quem_e_impactado" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quem é impactado por esse problema?</label>
                            <textarea name="quem_e_impactado" id="quem_e_impactado" rows="2"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Identifique o público afetado: cidadãos, empresas, prefeituras...">{{ old('quem_e_impactado', $empresa->quem_e_impactado) }}</textarea>
                        </div>
                        <div>
                            <label for="solucao_proposta" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Qual é a solução que será desenvolvida ou implementada?</label>
                            <textarea name="solucao_proposta" id="solucao_proposta" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Descreva de forma simples o que será criado...">{{ old('solucao_proposta', $empresa->solucao_proposta) }}</textarea>
                        </div>
                        <div>
                            <label for="como_funciona_na_pratica" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Como essa solução funciona na prática?</label>
                            <textarea name="como_funciona_na_pratica" id="como_funciona_na_pratica" rows="2"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Detalhe o funcionamento para qualquer pessoa entender...">{{ old('como_funciona_na_pratica', $empresa->como_funciona_na_pratica) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="estagio_solucao" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Em que estágio a solução se encontra?</label>
                                <select name="estagio_solucao" id="estagio_solucao" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach(['Ideia', 'Protótipo', 'MVP', 'Validação (Piloto)', 'Operação', 'Escala'] as $est)
                                        <option value="{{ $est }}" {{ old('estagio_solucao', $empresa->estagio_solucao) == $est ? 'selected' : '' }}>{{ $est }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="segmento_mercado" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Segmento de mercado</label>
                                <input type="text" name="segmento_mercado" id="segmento_mercado" value="{{ old('segmento_mercado', $empresa->segmento_mercado) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Ex: GovTech, HealthTech, AgTech...">
                            </div>
                        </div>
                        <div>
                            <label for="diferenciais" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quais são os principais diferenciais técnicos ou de modelo de negócio?</label>
                            <textarea name="diferenciais" id="diferenciais" rows="2"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Explique o que seu projeto tem de diferente...">{{ old('diferenciais', $empresa->diferenciais) }}</textarea>
                        </div>
                        <div>
                            <label for="propriedade_intelectual" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Possui propriedade intelectual (patente, marca, know-how exclusivo)?</label>
                            <textarea name="propriedade_intelectual" id="propriedade_intelectual" rows="2"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Indique se há registro, patente, marca ou conhecimento especializado...">{{ old('propriedade_intelectual', $empresa->propriedade_intelectual) }}</textarea>
                        </div>
                        <div>
                            <label for="historico_inovacao" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Histórico de Inovação</label>
                            <textarea name="historico_inovacao" id="historico_inovacao" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Projetos anteriores, editais já participados, parcerias com universidades...">{{ old('historico_inovacao', $empresa->historico_inovacao) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SEÇÃO 4: IMPACTOS --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="bg-indigo-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">4</span>
                            Impactos Esperados
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Editais exigem projeção de impactos econômicos, sociais e ambientais</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="impacto_economico" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quais serão os impactos econômicos?</label>
                                <textarea name="impacto_economico" id="impacto_economico" rows="2"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Geração de empregos, receita, redução de custos...">{{ old('impacto_economico', $empresa->impacto_economico) }}</textarea>
                            </div>
                            <div>
                                <label for="impacto_social" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quais serão os impactos sociais?</label>
                                <textarea name="impacto_social" id="impacto_social" rows="2"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Melhoria de qualidade de vida, acesso a serviços...">{{ old('impacto_social', $empresa->impacto_social) }}</textarea>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="impacto_ambiental" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quais serão os impactos ambientais?</label>
                                <textarea name="impacto_ambiental" id="impacto_ambiental" rows="2"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Redução de consumo de água, energia, resíduos...">{{ old('impacto_ambiental', $empresa->impacto_ambiental) }}</textarea>
                            </div>
                            <div>
                                <label for="metricas_indicadores" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quais métricas podem comprovar esses impactos?</label>
                                <textarea name="metricas_indicadores" id="metricas_indicadores" rows="2"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Ex: redução de 15% no uso de água, aumento de 20% na produtividade...">{{ old('metricas_indicadores', $empresa->metricas_indicadores) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEÇÃO 5: FINANCEIRO --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="bg-indigo-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">5</span>
                            Captação de Recursos
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sobre o tipo de financiamento e uso dos recursos</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="tipo_recurso_interesse" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Qual tipo de recurso financeiro tem interesse em captar?</label>
                            <select name="tipo_recurso_interesse" id="tipo_recurso_interesse" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Selecione...</option>
                                @foreach(['Subvenção Econômica (não reembolsável)', 'Financiamento (reembolsável)', 'Equity / Investimento', 'Misto (subvenção + financiamento)', 'Não sei ainda'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo_recurso_interesse', $empresa->tipo_recurso_interesse) == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="como_recurso_sera_utilizado" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Como o recurso será utilizado?</label>
                            <textarea name="como_recurso_sera_utilizado" id="como_recurso_sera_utilizado" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Detalhe: contratação de equipe, P&D, marketing, infraestrutura, validação de protótipo...">{{ old('como_recurso_sera_utilizado', $empresa->como_recurso_sera_utilizado) }}</textarea>
                        </div>
                        <div>
                            <label for="publico_alvo_empresa" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Público-alvo da empresa</label>
                            <input type="text" name="publico_alvo_empresa" id="publico_alvo_empresa" value="{{ old('publico_alvo_empresa', $empresa->publico_alvo_empresa) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Ex: Prefeituras, PMEs, consumidor final, agronegócio...">
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-xs text-gray-400 max-w-md">A IA utilizará todos esses dados para gerar propostas alinhadas aos editais de fomento.</p>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Salvar Todos os Dados
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faturamentoInput = document.getElementById('faturamento_anual');
            const cnpjInput = document.getElementById('cnpj');
            const telefoneInput = document.getElementById('telefone');

            // --- Máscara de Moeda (Faturamento) ---
            const formatCurrency = (value) => {
                value = value.replace(/\D/g, '');
                value = (value / 100).toFixed(2) + '';
                value = value.replace(".", ",");
                value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
                return value !== 'NaN' ? 'R$ ' + value : '';
            };

            faturamentoInput.addEventListener('input', (e) => {
                e.target.value = formatCurrency(e.target.value);
            });

            // Formata valor inicial se existir
            if (faturamentoInput.value) {
                faturamentoInput.value = formatCurrency(faturamentoInput.value);
            }

            // --- Máscara de CNPJ ---
            const formatCNPJ = (value) => {
                return value.replace(/\D/g, '')
                            .replace(/^(\d{2})(\d)/, '$1.$2')
                            .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                            .replace(/\.(\d{3})(\d)/, '.$1/$2')
                            .replace(/(\d{4})(\d)/, '$1-$2')
                            .replace(/(-\d{2})\d+?$/, '$1');
            };

            cnpjInput.addEventListener('input', (e) => {
                e.target.value = formatCNPJ(e.target.value);
            });

            // --- Máscara de Telefone ---
            const formatTelefone = (value) => {
                return value.replace(/\D/g, '')
                            .replace(/^(\d{2})(\d)/g, '($1) $2')
                            .replace(/(\d)(\d{4})$/, '$1-$2')
                            .replace(/(-\d{4})\d+?$/, '$1');
            };

            telefoneInput.addEventListener('input', (e) => {
                e.target.value = formatTelefone(e.target.value);
            });
        });
    </script>
</x-app-layout>
