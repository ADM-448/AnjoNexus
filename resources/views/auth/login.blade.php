<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Seja bem-vindo de volta!</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Acesse sua conta para gerenciar seus projetos de inovação.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('E-mail Institucional')" />
            <x-text-input id="email" class="block mt-1 w-full bg-slate-50 dark:bg-slate-900/50" type="email" name="email" :value="old('email')" required autofocus placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Senha')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Esqueceu a senha?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full bg-slate-50 dark:bg-slate-900/50"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">{{ __('Lembrar de mim') }}</span>
            </label>
        </div>

        <div class="flex flex-col gap-4 mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-indigo-600 hover:bg-indigo-500 group">
                <span class="flex items-center gap-2">
                    {{ __('Entrar no Dashboard') }}
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </span>
            </x-primary-button>
            
            <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                Ainda não tem conta? 
                <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Solicite acesso</a>
            </p>
        </div>
    </form>
</x-guest-layout>
