<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            {{ __('Gerador de Propostas Finep — Inteligência Artificial') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen relative overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-transparent blur-3xl -z-10"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-blue-500/20 to-transparent blur-3xl rounded-full -z-10"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
            
            @if (session('error'))
                <div class="bg-red-500/10 backdrop-blur-md border border-red-500/50 text-red-700 dark:text-red-300 p-4 rounded-xl shadow-lg mb-6 flex items-center gap-3 animate-pulse" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold">Erro no processamento</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Formulário de Input --}}
            <div class="p-8 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border border-white/20 dark:border-slate-700/50 shadow-2xl sm:rounded-2xl transition-all duration-300 hover:shadow-indigo-500/5" id="form-container">
                <div class="max-w-4xl mx-auto">
                    <form id="ai-form" class="space-y-8" action="{{ route('openai.generate') }}" method="POST">
                        @csrf
                        
                        @if(isset($editalObj) || !empty($selectedEditalName))
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-[1px] rounded-xl shadow-md">
                                <div class="bg-white dark:bg-slate-900 rounded-xl p-5 flex items-start gap-4">
                                    <div class="bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-lg text-indigo-600 dark:text-indigo-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-slate-800 dark:text-slate-200 text-lg">Edital Vinculado</h3>
                                        @if(isset($editalObj))
                                            <h4 class="text-indigo-600 dark:text-indigo-400 font-medium text-sm mt-1">{{ $editalObj->titulo }}</h4>
                                            <div class="mt-2 flex gap-4 text-xs text-slate-500 dark:text-slate-400">
                                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> {{ $editalObj->modalidade ?? 'Geral' }}</span>
                                                <span class="truncate max-w-xs">{{ $editalObj->temas ?? 'Não especificado' }}</span>
                                            </div>
                                        @else
                                            <p class="text-indigo-600 dark:text-indigo-400 font-medium text-sm mt-1">{{ $selectedEditalName }}</p>
                                        @endif
                                        <input type="hidden" name="edital_id" value="{{ request('edital_id') }}">
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-amber-500/10 border border-amber-500/20 p-4 rounded-xl flex items-start gap-4">
                                <span class="bg-amber-100 text-amber-600 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                                <p class="text-sm text-amber-700 dark:text-amber-300 my-auto">
                                    <strong>Dica:</strong> Para melhores resultados, gere propostas a partir do <a href="{{ route('editais.index') }}" class="underline font-bold hover:text-amber-800 transition">Radar de Editais</a>.
                                </p>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <label class="block font-bold text-slate-700 dark:text-slate-200 text-lg" for="search">
                                Pitch da Solução Tecnológica:
                            </label>
                            <div class="relative group">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl blur opacity-30 group-hover:opacity-60 transition duration-500"></div>
                                <textarea rows="6" class="relative bg-white dark:bg-slate-900 border-0 ring-1 ring-slate-200 dark:ring-slate-700 focus:ring-2 focus:ring-indigo-500 rounded-xl w-full p-5 text-slate-800 dark:text-slate-200 shadow-sm transition-all placeholder-slate-400 resize-y" id="search" name="search" required autofocus placeholder="Ex: Plataforma de gestão inteligente usando IoT e IA para descarbonização...">{{ request()->get('search', $defaultSearch ?? '') }}</textarea>
                            </div>
                            <p class="text-sm text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                                A IA usará este escopo e cruzará as informações com os dados da sua empresa para gerar uma proposta personalizada.
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <input type="hidden" name="model" value="gemini-flash-latest">
                            
                            <button type="submit" id="btn-submit" class="relative group overflow-hidden px-8 py-3 bg-slate-900 dark:bg-indigo-600 rounded-xl font-bold text-white shadow-[0_0_40px_-10px_rgba(79,70,229,0.5)] hover:shadow-[0_0_60px_-15px_rgba(79,70,229,0.7)] transition-all duration-300">
                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="relative flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-pulse" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                    </svg>
                                    Sintetizar Proposta Finep
                                </span>
                            </button>
                            <a href="{{ route('editais.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition">
                                Cancelar
                            </a>
                        </div>
                    </form>
                    
                    <!-- Loader Animado (Oculto por padrão) -->
                    <div id="loading-state" class="hidden py-10 flex flex-col items-center justify-center text-center space-y-6">
                        <div class="relative w-24 h-24">
                            <div class="absolute inset-0 rounded-full border-t-4 border-indigo-500 border-opacity-30 blur-sm animate-spin"></div>
                            <div class="absolute inset-0 rounded-full border-t-4 border-indigo-600 border-r-4 border-r-transparent border-b-4 border-b-transparent animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-indigo-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100" id="loading-title">Inicializando Motor de IA</h3>
                            <p class="text-slate-500 dark:text-slate-400 mt-2" id="loading-desc">Estabelecendo conexão segura com endpoints.</p>
                        </div>
                        
                        <!-- Barra de progresso visual -->
                        <div class="w-full max-w-md bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                            <div id="progress-bar" class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2.5 rounded-full" style="width: 5%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resultado formatado --}}
            @isset($result)
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-white/20 dark:border-slate-700 shadow-2xl rounded-2xl overflow-hidden mt-12 transform transition-all" id="result-container">
                
                {{-- Header Result --}}
                <div class="bg-slate-900 border-b border-indigo-500/20 px-8 py-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 blur-3xl rounded-full"></div>
                    <div class="flex flex-wrap items-center justify-between gap-4 relative z-10">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Documento Validado e Gerado
                            </span>
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                Proposta de Inovação Integrada
                            </h2>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-indigo-300 bg-indigo-900/40 px-3 py-1.5 rounded-lg border border-indigo-500/20 shadow-inner">
                                {!! join(' • ', $info) !!}
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Conteúdo do Documento Mestre --}}
                <div class="p-8 sm:px-12 py-10">
                    <div class="prose prose-indigo dark:prose-invert max-w-none
                        prose-headings:text-slate-800 dark:prose-headings:text-slate-100 prose-headings:font-bold
                        prose-h1:text-3xl prose-h1:border-b-2 prose-h1:border-indigo-500/30 prose-h1:pb-4 prose-h1:mb-8
                        prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                        prose-h3:text-xl prose-h3:mt-8
                        prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-p:mb-6
                        prose-li:text-slate-700 dark:prose-li:text-slate-300 prose-li:my-1
                        prose-strong:text-slate-900 dark:prose-strong:text-white prose-strong:font-bold
                        prose-table:w-full prose-table:rounded-xl prose-table:overflow-hidden prose-table:shadow-sm
                        prose-thead:bg-slate-100 dark:prose-thead:bg-slate-800
                        prose-tr:border-b prose-tr:border-slate-200 dark:prose-tr:border-slate-700
                        prose-td:py-3 prose-td:px-4 prose-th:py-3 prose-th:px-4 prose-th:text-left
                    ">
                        {!! \Illuminate\Support\Str::markdown($result) !!}
                    </div>
                </div>

                {{-- Footer com ações (Sticky) --}}
                <div class="bg-slate-100 dark:bg-slate-800/80 backdrop-blur-md border-t border-slate-200 dark:border-slate-700 p-4 sm:px-12 flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-0 z-20">
                        <span class="text-indigo-500">Documento gerado por Inteligência Artificial.</span> Revisão humana recomendada.
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button onclick="navigator.clipboard.writeText(document.querySelector('.prose').innerText).then(() => alert('Texto copiado com sucesso!'))" class="flex-1 sm:flex-none justify-center px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 rounded-xl font-bold shadow-sm transition flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            Copiar
                        </button>
                        <button onclick="window.print()" class="flex-1 sm:flex-none justify-center px-5 py-2.5 bg-slate-900 border border-transparent dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 text-white rounded-xl font-bold shadow-md transition flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Exportar PDF
                        </button>
                    </div>
                </div>
            </div>
            
            <script>
                // Rolagem automática para o resultado quando gerado
                document.addEventListener('DOMContentLoaded', function() {
                    const resultContainer = document.getElementById('result-container');
                    if(resultContainer) {
                        resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            </script>
            @endisset

        </div>
    </div>

    <!-- Script de Simulação Visual da IA -->
    <script>
        document.getElementById('ai-form').addEventListener('submit', function() {
            // Esconde o formulário, mostra o loader
            const btn = document.getElementById('btn-submit');
            const formInputs = this.querySelectorAll('textarea, input, button');
            formInputs.forEach(input => input.style.pointerEvents = 'none');
            
            this.classList.add('opacity-50', 'pointer-events-none');
            
            const loader = document.getElementById('loading-state');
            loader.classList.remove('hidden');
            
            // Textos dinâmicos para dar sensação de trabalho complexo
            const steps = [
                { title: 'Conectando ao Motor de IA...', desc: 'Inicializando motor de geração...', progress: 15 },
                { title: 'Analisando Edital e Contexto', desc: 'Cruzando dados da sua empresa com as regras do edital.', progress: 35 },
                { title: 'Estruturando Proposta', desc: 'Definindo cronograma e justificativas...', progress: 50 },
                { title: 'Otimizando Linguagem Técnica', desc: 'Concatenando regras da Finep e Prompt Mestre...', progress: 75 },
                { title: 'Finalizando Proposta', desc: 'Redigindo texto final e modelagem do rascunho.', progress: 95 }
            ];
            
            const titleEl = document.getElementById('loading-title');
            const descEl = document.getElementById('loading-desc');
            const barEl = document.getElementById('progress-bar');
            
            let currentStep = 0;
            
            const interval = setInterval(() => {
                if (currentStep >= steps.length) {
                    clearInterval(interval);
                    return;
                }
                
                titleEl.innerText = steps[currentStep].title;
                descEl.innerText = steps[currentStep].desc;
                barEl.style.width = steps[currentStep].progress + '%';
                
                currentStep++;
            }, 3000); // Muda a mensagem a cada 3 segundos
        });
    </script>
</x-app-layout>
