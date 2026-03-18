<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', function() {
                if (window.Echo) {
                    window.Echo.channel('editais')
                        .listen('NovoEditalDetectado', (e) => {
                            console.log('Novo edital detectado:', e.edital);
                            
                            // Criar um Toast simples usando Tailwind
                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white p-4 rounded-lg shadow-2xl z-50 transform transition duration-500 hover:scale-105 border-2 border-indigo-400';
                            toast.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 p-2 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm">Novo Edital no Radar!</p>
                                        <p class="text-xs text-indigo-100">${e.edital.titulo.substring(0, 50)}...</p>
                                        <a href="/editais/${e.edital.id}" class="text-[10px] underline font-bold mt-1 block">Ver oportunidade &raquo;</a>
                                    </div>
                                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white/50 hover:text-white">&times;</button>
                                </div>
                            `;
                            document.body.appendChild(toast);
                            
                            // Som de notificação (opcional)
                            // new Audio('/notification.mp3').play();
                        });
                }
            });
        </script>
    </body>
</html>
