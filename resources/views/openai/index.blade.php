<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gerador de Propostas — IA Gemini') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded shadow-sm mb-6 flex items-center gap-3" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold">Erro no processamento</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded shadow-sm mb-6 flex items-center gap-3" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Formulário de Input --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-3xl">
                    <form class="space-y-6"  action="{{ route('openai.generate') }}" method="POST">
                        @csrf
                        @if(isset($editalObj))
                            <div class="bg-indigo-50 dark:bg-indigo-900/50 border-l-4 border-indigo-500 p-4 rounded-r">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <h3 class="font-bold text-indigo-800 dark:text-indigo-200">Edital Vinculado</h3>
                                </div>
                                <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-100 mb-1 leading-tight">{{ $editalObj->titulo }}</h4>
                                <p class="text-xs text-indigo-600 dark:text-indigo-300">
                                    <strong>Temas:</strong> {{ $editalObj->temas ?? 'Não especificado' }} <br>
                                    <strong>Modalidade:</strong> {{ $editalObj->modalidade ?? 'Geral' }}
                                </p>
                                <input type="hidden" name="edital_id" value="{{ request('edital_id') }}">
                            </div>
                        @elseif(!empty($selectedEditalName))
                            <div class="bg-indigo-50 dark:bg-indigo-900/50 border-l-4 border-indigo-500 p-4 rounded-r">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <h3 class="font-bold text-indigo-800 dark:text-indigo-200">Edital Vinculado:</h3>
                                </div>
                                <p class="text-sm text-indigo-700 dark:text-indigo-300 mt-1">{{ $selectedEditalName }}</p>
                                <input type="hidden" name="edital_id" value="{{ request('edital_id') }}">
                            </div>
                        @else
                            <div class="bg-amber-50 dark:bg-amber-900/30 border-l-4 border-amber-400 p-4 rounded-r">
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    <strong>Dica:</strong> Para melhores resultados, gere propostas a partir do <a href="{{ route('editais.index') }}" class="underline font-bold">Radar de Editais</a>. A IA usa o contexto do edital + da sua empresa para criar documentos personalizados.
                                </p>
                            </div>
                        @endif

                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="search">
                                Descreva seu projeto / pitch:
                            </label>
                            <textarea rows="5" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full placeholder-gray-400" id="search" name="search" required autofocus placeholder="Ex: Plataforma de gestão inteligente para prefeituras usando IA para análise de desempenho...">{{ request()->get('search', $defaultSearch ?? '') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">A IA vai gerar um documento estruturado com seções, metodologia, cronograma e orçamento.</p>
                        </div>
                            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Modelo de IA</p>
                                    <p class="font-bold text-sm text-gray-800 dark:text-gray-200">Google Gemini 1.5 Flash (Rápido)</p>
                                </div>
                                <input type="hidden" name="model" value="gemini-flash-latest">
                            </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest shadow-md transition ease-in-out duration-150 gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                Gerar Proposta
                            </button>
                            <a href="{{ route('editais.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">
                                ← Voltar ao Radar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Resultado formatado --}}
            @isset($result)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h2 class="text-lg font-bold text-white">Proposta Gerada pela IA</h2>
                        </div>
                        <span class="text-xs text-indigo-200 bg-indigo-700/50 px-3 py-1 rounded-full">
                            {!! join(' ', $info) !!}
                        </span>
                    </div>
                    @if(!empty($selectedEditalName))
                        <p class="text-sm text-indigo-200 mt-1">Edital: {{ $selectedEditalName }}</p>
                    @endif
                </div>

                @if(!empty($projetoSalvo))
                    <div class="bg-green-50 dark:bg-green-900/30 border-b border-green-200 dark:border-green-800 px-6 py-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold text-green-700 dark:text-green-300">Proposta salva no Dashboard!</span>
                        <a href="{{ route('dashboard') }}" class="text-xs text-green-600 dark:text-green-400 underline ml-auto font-bold">Ver Minhas Propostas →</a>
                    </div>
                @elseif(isset($result) && empty($projetoSalvo))
                    <div class="bg-amber-50 dark:bg-amber-900/30 border-b border-amber-200 dark:border-amber-800 px-6 py-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm text-amber-700 dark:text-amber-300">Proposta não salva. <a href="{{ route('empresa.edit') }}" class="underline font-bold">Cadastre sua empresa</a> e gere a partir de um edital do Radar para salvar automaticamente.</span>
                    </div>
                @endif

                {{-- Conteúdo formatado --}}
                <div class="p-6 sm:p-8">
                    <div class="prose prose-indigo dark:prose-invert max-w-none
                        prose-headings:text-gray-900 dark:prose-headings:text-white
                        prose-h2:text-xl prose-h2:font-bold prose-h2:border-b prose-h2:border-gray-200 dark:prose-h2:border-gray-700 prose-h2:pb-2 prose-h2:mt-8 prose-h2:mb-4
                        prose-h3:text-lg prose-h3:font-semibold prose-h3:mt-6 prose-h3:mb-3
                        prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                        prose-li:text-gray-700 dark:prose-li:text-gray-300
                        prose-strong:text-gray-900 dark:prose-strong:text-white
                        prose-table:border prose-table:border-gray-200 dark:prose-table:border-gray-700
                        prose-th:bg-gray-50 dark:prose-th:bg-gray-700 prose-th:px-4 prose-th:py-2
                        prose-td:px-4 prose-td:py-2 prose-td:border prose-td:border-gray-200 dark:prose-td:border-gray-700
                    ">
                        {!! \Illuminate\Support\Str::markdown($result) !!}
                    </div>
                </div>

                {{-- Footer com ações --}}
                <div class="bg-gray-50 dark:bg-gray-750 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <p class="text-xs text-gray-400">
                            Documento gerado automaticamente pela IA. Revise e ajuste antes de submeter.
                        </p>
                        <div class="flex gap-3">
                            <button onclick="navigator.clipboard.writeText(document.querySelector('.prose').innerText).then(() => alert('Texto copiado!'))" class="text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg transition font-semibold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                Copiar Texto
                            </button>
                            <button onclick="window.print()" class="text-sm bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg transition font-semibold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Imprimir / PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endisset
        </div>
    </div>
</x-app-layout>
