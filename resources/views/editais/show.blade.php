<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $edital->titulo }}
            </h2>
            <a href="{{ route('editais.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
                &lsaquo; Voltar ao Radar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
                    <p class="font-bold">Sucesso</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
                    <p class="font-bold">Erro</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Coluna Esquerda: Detalhes -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-8">
                            <div class="flex items-center gap-4 mb-6">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 text-sm font-bold rounded-full">
                                    {{ $edital->orgao }}
                                </span>
                                <span class="text-sm text-gray-500">Publicado em: {{ \Carbon\Carbon::parse($edital->data_abertura)->format('d/m/Y') }}</span>
                            </div>

                            <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight">
                                {{ $edital->titulo }}
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                                
                                {{-- Descrição básica do edital --}}
                                <div class="mb-6">
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Consulte os detalhes técnicos e anexos diretamente no site oficial do órgão emissor. 
                                        A estrutura abaixo foi mapeada para auxiliar na sua proposta.
                                    </p>
                                </div>

                                {{-- Informações Adicionais --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Modalidade</span>
                                        <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                                            {{ ($edital->modalidade && $edital->modalidade !== 'Geral') ? $edital->modalidade : ($edital->payload_origem['modalidade'] ?? 'Não especificada') }}
                                        </span>
                                    </div>
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Público-alvo</span>
                                        <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                                            {{ ($edital->publico_alvo && $edital->publico_alvo !== 'Consulte o edital oficial') ? $edital->publico_alvo : ($edital->payload_origem['publico_alvo'] ?? 'Ver site oficial') }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($edital->orcamento_global)
                                    <p class="text-lg"><strong>Orçamento Total:</strong> <span class="text-green-600 font-bold">R$ {{ number_format($edital->orcamento_global, 2, ',', '.') }}</span></p>
                                @endif
                                
                                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold px-2 py-1 rounded {{ $edital->status === 'Aberto' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' }}">
                                                Status: {{ $edital->status ?? 'Não especificado' }}
                                            </span>
                                            @if($edital->ia_enriquecido)
                                                <span class="text-xs text-indigo-500 ml-2">✨ Enriquecido por IA</span>
                                            @endif
                                        </div>
                                        <a href="{{ $edital->url_oficial }}" target="_blank" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-bold hover:underline text-sm">
                                            Ir para site oficial &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seções do Edital / Questionário -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Estrutura da Proposta</h3>
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-500">{{ $edital->secoes->count() }} Seções Detectadas</span>
                            </div>

                            @if($edital->secoes->count() > 0)
                                <div class="space-y-4">
                                    @foreach($edital->secoes as $secao)
                                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition shadow-sm">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">{{ $secao->ordem }}. {{ $secao->titulo }}</h4>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $secao->perguntas->count() }} perguntas obrigatórias nesta parte.</p>
                                                </div>
                                                <div class="flex gap-2">
                                                    <!-- Botão para responder via IA individualmente -->
                                                    <a href="{{ route('openai.index', ['edital_id' => $edital->id, 'search' => 'Me ajude a escrever a seção "' . $secao->titulo . '" para o edital ' . $edital->titulo]) }}" class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300 transition">
                                                        Sugerir Texto IA
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    <h4 class="font-bold text-gray-600">Nenhuma seção mapeada ainda.</h4>
                                    <p class="text-sm text-gray-400 mt-1 max-w-xs mx-auto">Use a IA para extrair automaticamente a estrutura do documento oficial.</p>
                                    
                                    <form action="/editais/{{ $edital->id }}/analyze" method="POST">
                                        @csrf
                                        <button type="submit" class="mt-4 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition flex items-center mx-auto gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            IA: Mapear Estrutura do Edital
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: Ações Rápidas -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-xl overflow-hidden text-white">
                        <div class="p-8">
                            <h3 class="text-xl font-bold mb-4">Pronto para aplicar?</h3>
                            <p class="text-indigo-100 text-sm mb-6 leading-relaxed">
                                Nossa inteligência artificial pode gerar o documento completo da sua proposta baseado no seu perfil empresarial.
                            </p>
                            
                            <a href="{{ route('openai.index', ['edital_id' => $edital->id]) }}" class="block w-full text-center bg-white text-indigo-700 font-extrabold py-3 rounded-lg shadow-lg hover:bg-indigo-50 transition transform hover:-translate-y-1">
                                Iniciar Proposta com Gemini
                            </a>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-4">Dicas do Anjo</h4>
                        <ul class="text-sm space-y-3 text-gray-600 dark:text-gray-400">
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-1">&check;</span>
                                <span>Verifique o TRL mínimo exigido para este edital.</span>
                            </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-green-500 mt-1">&check;</span>
                                    <span>Mantenha o foco nos temas abordados pelo edital.</span>
                                </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-1">&check;</span>
                                <span>Prepare os documentos de regularidade fiscal da empresa.</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
