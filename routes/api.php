<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::get('/public/contests/{contest}/stage', [\App\Http\Controllers\Api\PublicController::class, 'stage']);
Route::get('/public/rankings', [\App\Http\Controllers\Api\RankingController::class, 'indexPublic']);
Route::get('/public/contests/{contest}/ranking', [\App\Http\Controllers\Api\RankingController::class, 'public']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('events', \App\Http\Controllers\Api\EventController::class);
    Route::apiResource('contests', \App\Http\Controllers\Api\ContestController::class);
    Route::apiResource('jurors', \App\Http\Controllers\Api\JurorController::class);
    Route::apiResource('presentations', \App\Http\Controllers\Api\PresentationController::class);
    Route::post('/presentations/{presentation}/evaluate', [\App\Http\Controllers\Api\PresentationController::class, 'evaluate']);
    Route::apiResource('documents', \App\Http\Controllers\Api\DocumentController::class);
    
    Route::get('/contests/{contest}/stage', [\App\Http\Controllers\Api\StageController::class, 'show']);
    Route::post('/contests/{contest}/stage', [\App\Http\Controllers\Api\StageController::class, 'setOnStage']);
    Route::post('/contests/{contest}/finish', [\App\Http\Controllers\Api\StageController::class, 'finishContest']);

    Route::get('/contests/{contest}/ranking/admin', [\App\Http\Controllers\Api\RankingController::class, 'admin']);
    Route::post('/contests/{contest}/release-ranking', [\App\Http\Controllers\Api\RankingController::class, 'toggleRelease']);
    Route::get('/contests/{contest}/evaluation', [\App\Http\Controllers\Api\EvaluationController::class, 'show']);
    Route::post('/contests/{contest}/evaluation', [\App\Http\Controllers\Api\EvaluationController::class, 'submit']);

    Route::post('/checkin', [\App\Http\Controllers\Api\CheckinController::class, 'process']);
});

