<?php

use App\Http\Controllers\Api\MapController;
use Illuminate\Support\Facades\Route;

Route::prefix('map')->name('map.')->group(function (): void {
    Route::get('/facilities', [MapController::class, 'facilities'])->name('facilities');
    Route::get('/hazards', [MapController::class, 'hazards'])->name('hazards');
    Route::get('/points/{type}/{id}', [MapController::class, 'point'])->name('points.show');
    Route::get('/overview', [MapController::class, 'overview'])->name('overview');
});
