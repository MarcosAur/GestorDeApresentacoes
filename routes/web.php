<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Document download (shared check in controller)
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/events', \App\Livewire\EventList::class)->name('events.index');
        Route::get('/contests', \App\Livewire\ContestManager::class)->name('contests.index');
        Route::get('/jurors', \App\Livewire\JurorManager::class)->name('jurors.index');
        Route::get('/analyzer', \App\Livewire\Admin\PresentationAnalyzer::class)->name('admin.analyzer');
        Route::get('/checkin', \App\Livewire\Admin\CheckinScanner::class)->name('admin.checkin');
    });

    // Competitor only routes
    Route::middleware(['role:competidor'])->group(function () {
        Route::get('/enrollment', \App\Livewire\Competitor\EnrollmentPanel::class)->name('competitor.enrollment');
    });
});
