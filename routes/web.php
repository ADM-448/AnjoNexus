<?php

use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProjetoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Webhook do Mercado Pago (Deve ser PÚBLICO para receber POSTs sem sessão do usuário)
Route::post('/payments/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payments.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // IA — acessível apenas via link do edital (sem nav)
    // 1. Rota GET para ABRIR a página (vindo do Dashboard/Radar)
    Route::get('/openai', [OpenAIController::class, 'index'])->name('openai.index');

    // 2. Rota POST para ENVIAR o texto gigante para o Gemini
    Route::post('/openai', [OpenAIController::class, 'index'])->name('openai.generate');

    // Empresa
    Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

    // Projetos e Propostas
    Route::get('/projetos/{projeto}', [ProjetoController::class, 'show'])->name('projetos.show');

    // Editais
    Route::post('/radar/atualizar', [\App\Http\Controllers\EditalController::class, 'manualScrape'])->name('editais.manual_scrape');
    Route::post('/editais/{edital}/analyze', [\App\Http\Controllers\EditalController::class, 'analyzeIA'])->name('editais.analyze');
    Route::resource('editais', \App\Http\Controllers\EditalController::class);

    // Pagamentos (Mercado Pago)
    Route::get('/payments/checkout', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/preference', [\App\Http\Controllers\PaymentController::class, 'createPreference'])->name('payments.preference');
    Route::get('/payments/success', [\App\Http\Controllers\PaymentController::class, 'successReturn'])->name('payments.success');

    // --- PAINEL ADMIN (DEV MASTER) ---
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/credits', [\App\Http\Controllers\AdminController::class, 'addCredits'])->name('users.credits');
        // Futura Rota de Planos
        Route::get('/plans', function() { return "Painel de Planos (Mercado Pago API - Em breve)"; })->name('plans');
    });
});

require __DIR__.'/auth.php';
