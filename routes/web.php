<?php

use App\Http\Controllers\SecurityReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Fitur laporan keamanan
Route::middleware('auth')->group(function () {
    Route::post('/laporan', [SecurityReportController::class, 'store'])
        ->name('laporan.store');

    Route::get('/laporan/terakhir', [SecurityReportController::class, 'latest'])
        ->name('laporan.latest');
});
