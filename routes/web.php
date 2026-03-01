<?php

use App\Http\Controllers\Admin\AdminSyncController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\ControleEnvioController;
use App\Http\Controllers\Admin\MantimentoController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\PedidoAjudaPublicController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/doar', [PublicController::class, 'donate'])->name('doar');
Route::get('/preciso-de-ajuda', [PedidoAjudaPublicController::class, 'create'])->name('preciso-ajuda.create');
Route::post('/preciso-de-ajuda', [PedidoAjudaPublicController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('preciso-ajuda.store');
Route::get('/pedido-{codigo}', [PedidoAjudaPublicController::class, 'show'])
    ->where('codigo', '\d{5}')
    ->name('pedido.show');
Route::redirect('/login', '/admin/login')->name('login');

Route::prefix('admin')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:10,1')
            ->name('admin.login.attempt');
    });

    Route::middleware('auth')->name('admin.')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('admin.mantimentos.index');
        })->name('index');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/sync-state', [AdminSyncController::class, 'state'])
            ->middleware('throttle:180,1')
            ->name('sync-state');

        Route::get('/mantimentos', [MantimentoController::class, 'index'])->name('mantimentos.index');
        Route::post('/mantimentos', [MantimentoController::class, 'store'])->name('mantimentos.store');
        Route::put('/mantimentos/salvar', [MantimentoController::class, 'bulkUpdate'])->name('mantimentos.bulk-update');
        Route::put('/mantimentos/{mantimento}', [MantimentoController::class, 'update'])->name('mantimentos.update');
        Route::delete('/mantimentos/{mantimento}', [MantimentoController::class, 'destroy'])->name('mantimentos.destroy');

        Route::get('/controle-envios', [ControleEnvioController::class, 'index'])->name('controle-envios.index');
        Route::post('/controle-envios', [ControleEnvioController::class, 'store'])->name('controle-envios.store');
        Route::put('/controle-envios/salvar', [ControleEnvioController::class, 'bulkUpdate'])->name('controle-envios.bulk-update');
        Route::delete('/controle-envios/{pedido}', [ControleEnvioController::class, 'destroy'])->name('controle-envios.destroy');

        Route::get('/configuracoes', [ConfiguracaoController::class, 'edit'])->name('configuracoes.edit');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');
    });
});
