<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Proposta Documental
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700 transition">
                &larr; Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen relative overflow-hidden">
        <!-- Background elements -->
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-transparent blur-3xl -z-10"></div>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
            
            {{-- Header Project --}}
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-xl rounded-2xl overflow-hidden p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 rounded-full font-bold text-xs uppercase tracking-wide">
                            {{ $projeto->status ?? 'Em revisão' }}
                        </span>
                        <h1 class="text-3xl font-black text-slate-900 dark:text-white mt-4">{{ $projeto->titulo }}</h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Vinculado ao Edital: <span class="text-indigo-600 dark:text-indigo-400">{{ $projeto->edital->titulo ?? 'Nenhum' }}</span></p>
                    </div>
                </div>
            </div>

            @foreach($projeto->documentosGerados as $index => $doc)
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-2xl rounded-2xl overflow-hidden mt-8 transform transition-all group">
                    
                    {{-- Header Document --}}
                    <div class="bg-slate-900 border-b border-indigo-500/20 px-8 py-6 relative overflow-hidden flex items-center justify-between">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 blur-3xl rounded-full"></div>
                        <div class="relative z-10 flex items-center gap-4">
                            <div class="bg-indigo-600 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Versão Completa da Proposta</h2>
                                <p class="text-xs text-indigo-300 mt-1">Status do Documento: {{ $doc->status }} | Criado em {{ $doc->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>


                    {{-- Prompt Expandible --}}
                    @if($doc->prompt_utilizado)
                        <div x-data="{ open: false }" class="border-b border-slate-200 dark:border-slate-700/50">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 bg-slate-100 dark:bg-slate-800/40 hover:bg-slate-200 dark:hover:bg-slate-700/60 transition text-sm font-semibold text-slate-600 dark:text-slate-300">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path></svg>
                                    Ver Resumo/Prompt do Cliente
                                </span>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700 font-mono text-sm text-slate-500 whitespace-pre-wrap">{{ $doc->prompt_utilizado }}</div>
                        </div>
                    @endif

                    {{-- Conteúdo Markdown --}}
                    <div class="p-8 sm:px-12 py-10 doc-content">
                        <div class="prose prose-indigo dark:prose-invert max-w-none
                            prose-headings:text-slate-800 dark:prose-headings:text-slate-100 prose-headings:font-bold
                            prose-h1:text-3xl prose-h1:border-b-2 prose-h1:border-indigo-500/30 prose-h1:pb-4 prose-h1:mb-8
                            prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                            prose-h3:text-xl prose-h3:mt-8
                            prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-p:mb-6
                            prose-strong:text-slate-900 dark:prose-strong:text-white prose-strong:font-bold
                            prose-table:w-full prose-table:rounded-xl prose-table:overflow-hidden
                            prose-thead:bg-slate-100 dark:prose-thead:bg-slate-800
                        ">
                            {!! \Illuminate\Support\Str::markdown($doc->conteudo_ia) !!}
                        </div>
                    </div>

                    {{-- Ações --}}
                    <div class="bg-slate-100 dark:bg-slate-800/80 p-6 sm:px-12 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 dark:border-slate-700 flex-wrap">
                        <p class="text-xs text-slate-500">Pronto para submissão oficial ao Finep.</p>
                        <div class="flex gap-3">
                            <button onclick="navigator.clipboard.writeText(document.querySelectorAll('.doc-content')[{{ $index }}].innerText).then(() => alert('Documento Copiado!'))" class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 font-bold text-slate-700 dark:text-white rounded-xl shadow-sm hover:bg-slate-50 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                Copiar
                            </button>
                            <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 font-bold text-white rounded-xl shadow hover:bg-indigo-500 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Imprimir PDF
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($projeto->documentosGerados->isEmpty())
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center shadow">
                    <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">Projeto ainda vazio</h3>
                    <p class="text-slate-500">Nenhum documento IA foi gerado para este projeto ainda.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
