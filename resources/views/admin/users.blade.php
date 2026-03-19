<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Gerenciamento de <span class="text-indigo-600">Usuários</span></h1>
                    <p class="text-slate-500 dark:text-slate-400">Controle de acesso, créditos e status de conta.</p>
                </div>
                <a href="{{ route('admin.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Voltar ao Painel &laquo;</a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-start flex-1">Nome / Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-start">Créditos</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-start">Chave Própria</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-start">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 font-bold text-indigo-600 text-xs flex items-center justify-center">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400">
                                        {{ $user->creditos_ia }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->google_gemini_key)
                                        <span class="text-green-500 text-xs font-medium flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            Configurada
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs font-medium italic">Padrão</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.users.credits', $user->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="credits" value="10" class="w-16 px-2 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded-lg dark:bg-slate-900 text-slate-700 dark:text-white" required min="1">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-1 px-3 rounded-lg text-xs shadow transition">
                                            + Créditos
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
