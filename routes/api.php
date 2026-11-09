<?php

use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\RouteScoreController;
use Illuminate\Support\Facades\Route;

Route::post('/routes/score', RouteScoreController::class)
    ->middleware('throttle:30,1')
    ->name('routes.score');

Route::prefix('map')->name('map.')->group(function (): void {
    Route::get('/facilities', [MapController::class, 'facilities'])->name('facilities');
    Route::get('/hazards', [MapController::class, 'hazards'])->name('hazards');
    Route::get('/points/{type}/{id}', [MapController::class, 'point'])->name('points.show');
    Route::get('/overview', [MapController::class, 'overview'])->name('overview');
});
