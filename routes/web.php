<?php

use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
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

    // Editais
    Route::post('/radar/atualizar', [\App\Http\Controllers\EditalController::class, 'manualScrape'])->name('editais.manual_scrape');
    Route::post('/editais/{edital}/analyze', [\App\Http\Controllers\EditalController::class, 'analyzeIA'])->name('editais.analyze');
    Route::resource('editais', \App\Http\Controllers\EditalController::class);
});

require __DIR__.'/auth.php';
