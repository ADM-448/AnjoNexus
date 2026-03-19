<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Painel do <span class="text-indigo-600">Dev Master</span></h1>
                <p class="text-slate-500 dark:text-slate-400">Gerenciamento global de recursos e economia do sistema.</p>
            </div>

            {{-- Grid de Estatísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Usuários Totais</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Créditos em Circulação</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">{{ $stats['total_credits'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Propostas Geradas</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">{{ $stats['total_documents'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Editais Ativos</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">{{ $stats['total_editais'] }}</p>
                </div>
            </div>

            {{-- Seções do Admin --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Usuários Recentes --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm overflow-hidden border border-slate-100 dark:border-slate-700">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Usuários Recentes</h3>
                        <a href="{{ route('admin.users') }}" class="text-xs font-bold text-indigo-600 hover:underline">Ver Todos &raquo;</a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($recentUsers as $user)
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-500">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-indigo-600">{{ $user->creditos_ia }} créditos</p>
                                    <p class="text-[10px] text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ações Rápidas --}}
                <div class="space-y-6">
                    <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group">
                        <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        <h3 class="text-xl font-black mb-2">Gestão de Créditos</h3>
                        <p class="text-sm text-white/80 mb-6">Controle manualmente o saldo dos usuários e gere faturas manuais.</p>
                        <a href="{{ route('admin.users') }}" class="bg-white text-indigo-600 font-bold py-2 px-4 rounded-lg text-sm inline-block shadow-md hover:bg-slate-50 transition">Gerenciar Saldo</a>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm transition hover:scale-[1.02]">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Assinaturas SaaS</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Em breve: Integração com Mercado Pago para automação de mensalidades e planos.</p>
                        <a href="{{ route('admin.plans') }}" class="text-indigo-600 font-bold text-sm border border-indigo-100 dark:border-indigo-900/50 py-2 px-4 rounded-lg block text-center opacity-50 cursor-not-allowed">Configurar Planos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
