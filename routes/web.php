<?php

use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

/**
 * Rotas da SPA Vue.js
 * Todas as rotas que não são da API ou de arquivos estáticos 
 * são tratadas pelo Vue Router no frontend.
 */

// Download de documentos (protegido por auth web para compatibilidade de links diretos)
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});

// Catch-all route para servir a SPA Vue em qualquer URL
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|sanctum|up).*$');
