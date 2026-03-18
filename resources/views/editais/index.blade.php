<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Radar de Editais') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
                    <p class="font-bold">Sucesso</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
                    <p class="font-bold">Aviso</p>
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            {{-- Resumo do Radar --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold">Resumo do Radar</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Varredura automática detectou {{ $editais->total() }} oportunidades de fomento.</p>
                    </div>
                    <div class="flex gap-3">
                        <form action="{{ route('editais.manual_scrape') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white font-semibold py-2 px-4 rounded shadow-sm border border-gray-300 dark:border-gray-600 transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Atualizar Radar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Barra de Filtros --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <form action="{{ route('editais.index') }}" method="GET" class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                        {{-- Busca por texto --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                            <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Título ou tema..." class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Órgão --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Órgão</label>
                            <select name="orgao" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach($orgaos as $orgao)
                                    <option value="{{ $orgao }}" {{ request('orgao') == $orgao ? 'selected' : '' }}>{{ Str::limit($orgao, 30) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="Aberto" {{ request('status') == 'Aberto' ? 'selected' : '' }}>🟢 Aberto</option>
                                <option value="Em breve" {{ request('status') == 'Em breve' ? 'selected' : '' }}>🟡 Em breve</option>
                                <option value="Encerrado" {{ request('status') == 'Encerrado' ? 'selected' : '' }}>🔴 Encerrado</option>
                            </select>
                        </div>

                        {{-- Modalidade --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Modalidade</label>
                            <select name="modalidade" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas</option>
                                @foreach($modalidades as $mod)
                                    <option value="{{ $mod }}" {{ request('modalidade') == $mod ? 'selected' : '' }}>{{ Str::limit($mod, 30) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botões --}}
                        <div class="flex gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded shadow-md transition text-sm w-full">
                                Filtrar
                            </button>
                            <a href="{{ route('editais.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white font-semibold py-2 px-3 rounded shadow-sm transition text-sm text-center whitespace-nowrap">
                                Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Grid de Editais --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($editais as $edital)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 transition hover:shadow-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 text-xs font-bold rounded-full">
                                    {{ Str::limit($edital->orgao ?? 'Órgão não definido', 25) }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex flex-col items-end">
                                    <span>Status: <strong class="{{ $edital->status == 'Aberto' ? 'text-green-500' : ($edital->status == 'Em breve' ? 'text-yellow-500' : 'text-red-500') }}">{{ $edital->status }}</strong></span>
                                    @if($edital->last_scanned_at)
                                        <span class="text-[10px] opacity-75">Sincronizado: {{ $edital->last_scanned_at->diffForHumans() }}</span>
                                    @endif
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-tight">
                                <a href="{{ route('openai.index', ['edital_id' => $edital->id]) }}" class="hover:text-indigo-600 transition">
                                    {{ Str::limit($edital->titulo, 80) }}
                                </a>
                            </h3>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-4">
                                Temas: {{ Str::limit($edital->temas ?? 'Não especificado', 60) }} <br>
                                Modalidade: {{ Str::limit($edital->modalidade ?? 'Geral', 40) }}
                            </p>

                            @if($edital->orcamento_global)
                                <div class="mb-4 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Orçamento: R$ {{ number_format($edital->orcamento_global, 2, ',', '.') }}
                                </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <div class="flex flex-col">
                                    <a href="{{ route('openai.index', ['edital_id' => $edital->id]) }}" class="text-indigo-600 dark:text-indigo-400 font-bold text-xs hover:underline">
                                        Gerar Proposta via IA &raquo;
                                    </a>
                                    <a href="{{ $edital->url_oficial }}" target="_blank" class="text-gray-400 hover:text-gray-600 text-[10px] uppercase tracking-tighter mt-1">
                                        Site Oficial &nearrow;
                                    </a>
                                </div>
                                
                               <form action="{{ route('openai.index') }}" method="GET">
                                    <input type="hidden" name="edital_id" value="{{ $edital->id }}">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-3 py-1.5 rounded transition">
                                        Gerar Proposta »
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 p-12 rounded-lg shadow text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-bold text-gray-500 dark:text-gray-400">Nenhum edital encontrado</h3>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Tente mudar os filtros ou atualize o radar.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $editais->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
