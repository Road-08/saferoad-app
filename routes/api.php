<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

\Illuminate\Support\Facades\Route::get('/routes/compare', [\App\Http\Controllers\RouteComparisonController::class, 'compare']);

\Illuminate\Support\Facades\Route::get('/routes/compare', [\App\Http\Controllers\RouteComparisonController::class, 'compare']);
