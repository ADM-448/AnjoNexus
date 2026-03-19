<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Criar nova conta</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Junte-se ao ecossistema AnjoNexus e comece a captar hoje.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome Completo')" />
            <x-text-input id="name" class="block mt-1 w-full bg-slate-50 dark:bg-slate-900/50" type="text" name="name" :value="old('name')" required autofocus placeholder="Seu nome" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('E-mail Institucional')" />
            <x-text-input id="email" class="block mt-1 w-full bg-slate-50 dark:bg-slate-900/50" type="email" name="email" :value="old('email')" required placeholder="exemplo@organizacao.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full bg-slate-50 dark:bg-slate-900/50"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-slate-50 dark:bg-slate-900/50"
                            type="password"
                            name="password_confirmation" required
                            placeholder="Repita sua senha" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4 mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-indigo-600 hover:bg-indigo-500 group">
                <span class="flex items-center gap-2">
                    {{ __('Finalizar Cadastro') }}
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </span>
            </x-primary-button>

            <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                Já possui uma conta? 
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Fazer login</a>
            </p>
        </div>
    </form>
</x-guest-layout>
