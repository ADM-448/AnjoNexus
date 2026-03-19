<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard — Anjo Inovador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Contadores Rápidos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-b-4 border-indigo-500">
                    <p class="text-[10px] uppercase text-gray-500 dark:text-gray-400 font-bold tracking-widest">Editais Radar</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalEditais }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-b-4 border-green-500">
                    <p class="text-[10px] uppercase text-gray-500 dark:text-gray-400 font-bold tracking-widest">Abertos</p>
                    <p class="text-2xl font-black text-green-600 mt-1">{{ $editaisAbertos }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-b-4 border-purple-500">
                    <p class="text-[10px] uppercase text-gray-500 dark:text-gray-400 font-bold tracking-widest">Propostas</p>
                    <p class="text-2xl font-black text-purple-600 mt-1">{{ $totalPropostas }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-b-4 border-yellow-500">
                    <p class="text-[10px] uppercase text-gray-500 dark:text-gray-400 font-bold tracking-widest">Rascunhos</p>
                    <p class="text-2xl font-black text-yellow-600 mt-1">{{ $rascunhos }}</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl shadow-lg p-5 text-white relative overflow-hidden group">
                    <svg class="absolute -right-2 -bottom-2 w-16 h-16 text-white/10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
                    <div class="flex items-center justify-between mb-2">
                         <p class="text-[10px] uppercase text-white/80 font-bold tracking-widest">Meus Créditos IA</p>
                         <a href="{{ route('payments.checkout') }}" class="bg-white/20 hover:bg-white/30 text-white text-[10px] font-bold px-2 py-1 rounded-md transition backdrop-blur-md">Comprar &rarr;</a>
                    </div>
                    <div class="flex items-baseline gap-1 mt-1">
                        <p class="text-2xl font-black">{{ auth()->user()->creditos_ia }}</p>
                        <span class="text-[10px] font-medium text-white/70">disponíveis</span>
                    </div>
                </div>
            </div>

            {{-- Ações Rápidas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ações Rápidas</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Comece por onde está o dinheiro!</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('editais.index') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-5 rounded-lg shadow-md transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Explorar Radar
                        </a>
                        @if(!$empresa)
                            <a href="{{ route('empresa.edit') }}" class="bg-yellow-500 hover:bg-yellow-400 text-white font-bold py-2 px-5 rounded-lg shadow-md transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Cadastrar Empresa
                            </a>
                        @else
                            <a href="{{ route('empresa.edit') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white font-semibold py-2 px-5 rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                {{ Str::limit($empresa->razao_social, 20) }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Propostas Geradas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Minhas Propostas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Projetos e propostas gerados pela IA a partir dos editais do Radar.</p>
                </div>

                @if($projetos->count() > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($projetos as $projeto)
                            <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-750 transition duration-300 group">
                                <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h4 class="font-bold text-lg text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $projeto->titulo }}</h4>
                                            
                                            <span class="px-3 py-0.5 text-xs font-semibold rounded-full border
                                                {{ $projeto->status == 'Pronto' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' : '' }}
                                                {{ $projeto->status == 'Em rascunho' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800' : '' }}
                                                {{ !in_array($projeto->status, ['Pronto', 'Em rascunho']) ? 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800' : '' }}
                                            ">
                                                {{ $projeto->status }}
                                            </span>
                                        </div>

                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            Edital: <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ Str::limit($projeto->edital->titulo ?? 'Nenhum edital vinculado', 60) }}</span>
                                        </p>

                                        @if($projeto->documentosGerados->count() > 0)
                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-lg">
                                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    {{ $projeto->documentosGerados->count() }} Versão(ões) pela IA
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                                        @if($projeto->edital)
                                            <a href="{{ route('editais.show', $projeto->edital->id) }}" class="w-full sm:w-auto px-4 py-2 border border-slate-300 dark:border-slate-600 font-bold text-xs text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition text-center flex items-center justify-center gap-1">
                                                Ver Edital
                                            </a>
                                        @endif
                                        <a href="{{ route('projetos.show', $projeto->id) }}" class="w-full sm:w-auto px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold text-xs rounded-lg shadow-md transition text-center flex items-center justify-center gap-2 group-hover:scale-105">
                                            Ler Proposta 
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h4 class="text-lg font-bold text-gray-500 dark:text-gray-400">Nenhuma proposta gerada ainda</h4>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1 max-w-md mx-auto">
                            Explore o <strong>Radar de Editais</strong>, encontre uma oportunidade e clique em <em>"Gerar Proposta"</em> para começar.
                        </p>
                        <a href="{{ route('editais.index') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Ir para o Radar &raquo;
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
