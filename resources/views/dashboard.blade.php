<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard — Anjo Inovador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Contadores Rápidos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider">Editais no Radar</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalEditais }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider">Abertos Agora</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $editaisAbertos }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider">Propostas Geradas</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $totalPropostas }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider">Em Rascunho</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $rascunhos }}</p>
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
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <div class="flex flex-wrap justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $projeto->titulo }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Edital: <span class="text-indigo-600 dark:text-indigo-400">{{ Str::limit($projeto->edital->titulo ?? 'N/A', 50) }}</span>
                                        </p>
                                        @if($projeto->documentosGerados->count() > 0)
                                            <p class="text-xs text-gray-400 mt-2">
                                                {{ $projeto->documentosGerados->count() }} documento(s) gerado(s) pela IA
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            {{ $projeto->status == 'Pronto para envio' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                            {{ $projeto->status == 'Em rascunho' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                            {{ $projeto->status == 'Enviado' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                        ">
                                            {{ $projeto->status }}
                                        </span>
                                        @if($projeto->edital)
                                            <a href="{{ route('editais.show', $projeto->edital->id) }}" class="text-indigo-600 dark:text-indigo-400 text-xs font-bold hover:underline">
                                                Ver Edital &raquo;
                                            </a>
                                        @endif
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
