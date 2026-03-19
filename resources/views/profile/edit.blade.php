<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header de Perfil --}}
            <div class="mb-10 flex flex-col md:flex-row items-center gap-6">
                <div class="w-24 h-24 rounded-3xl bg-indigo-600 shadow-xl shadow-indigo-500/20 flex items-center justify-center text-white text-4xl font-black">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ auth()->user()->name }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold uppercase rounded-md tracking-widest">
                            {{ auth()->user()->is_admin ? 'Dev Master' : 'Usuário' }}
                        </span>
                        &bull; {{ auth()->user()->email }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Informações do Perfil --}}
                <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none sm:rounded-3xl border border-slate-100 dark:border-slate-700/50">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Segurança --}}
                <div class="space-y-8">
                    <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none sm:rounded-3xl border border-slate-100 dark:border-slate-700/50">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none sm:rounded-3xl border border-slate-100 dark:border-slate-700/50 opacity-80 hover:opacity-100 transition">
                        <div class="max-w-xl text-red-600">
                             @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
