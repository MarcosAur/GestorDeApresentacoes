<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/events', \App\Livewire\EventList::class)->name('events.index');
        Route::get('/contests', \App\Livewire\ContestManager::class)->name('contests.index');
        Route::get('/jurors', \App\Livewire\JurorManager::class)->name('jurors.index');
    });
});
