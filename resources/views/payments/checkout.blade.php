<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-8 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl text-center font-bold">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('status'))
                <div class="mb-8 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-2xl text-center font-bold">
                    {{ session('status') }}
                </div>
            @endif

            <div class="text-center mb-16">
                <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-4">Escolha o seu <span class="text-indigo-600">Pacote de Créditos</span></h1>
                <p class="text-slate-500 dark:text-slate-400 text-lg max-w-2xl mx-auto">
                    Acelere suas propostas com a IA mais avançada do mercado. Sem mensalidade obrigatória, pague apenas pelo que usar.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                @foreach($plans as $plan)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 flex flex-col items-center text-center relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                        
                        {{-- Badge Decorativa --}}
                        <div class="absolute -top-12 -right-12 w-24 h-24 bg-{{ $plan['color'] }}-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="w-16 h-16 rounded-2xl bg-{{ $plan['color'] }}-100 dark:bg-{{ $plan['color'] }}-900/30 flex items-center justify-center mb-6">
                             <svg class="w-8 h-8 text-{{ $plan['color'] }}-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
                        </div>
                        
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">{{ $plan['name'] }}</h3>
                        <p class="text-{{ $plan['color'] }}-600 font-bold mb-6">{{ $plan['credits'] }} Créditos IA</p>
                        
                        <div class="mb-8 flex items-baseline gap-1">
                            <span class="text-sm font-bold text-slate-400">R$</span>
                            <span class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($plan['price'], 2, ',', '.') }}</span>
                        </div>

                        <ul class="text-sm text-slate-500 dark:text-slate-400 space-y-3 mb-8 text-start w-full">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Uso ilimitado do Gemini Flash
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Analise editais complexos
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Suporte Prioritário
                            </li>
                        </ul>

                        <form action="{{ route('payments.preference') }}" method="POST" class="w-full mt-auto">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                            <button type="submit" class="w-full bg-slate-900 dark:bg-{{ $plan['color'] }}-600 hover:bg-slate-800 dark:hover:bg-{{ $plan['color'] }}-500 text-white font-black py-4 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                                Comprar Agora
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="bg-indigo-600 rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="z-10">
                    <h3 class="text-2xl font-black mb-2">Já tem uma Chave Google Gemini?</h3>
                    <p class="text-white/80 max-w-lg italic font-medium">Basta configurar sua chave própria nas configurações de perfil para usar o sistema sem gastar seus créditos.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="z-10 bg-white text-indigo-600 px-8 py-4 rounded-2xl font-black shadow-xl hover:scale-105 transition-transform flex items-center justify-center gap-2">
                    Configurar Chave Própria
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </a>
                <svg class="absolute -right-10 -bottom-10 w-64 h-64 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>

            <div class="mt-16 text-center text-slate-400 text-xs">
                <p>Pagamentos processados com segurança via Mercado Pago.</p>
                <div class="flex items-center justify-center gap-4 mt-4 opacity-50">
                    <img src="https://img.icons8.com/color/48/000000/visa.png" class="h-8 shadow-sm">
                    <img src="https://img.icons8.com/color/48/000000/mastercard.png" class="h-8 shadow-sm">
                    <img src="https://img.icons8.com/color/48/000000/pix.png" class="h-8 shadow-sm">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
