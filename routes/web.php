<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SR-11: Profile Management
Route::get('/profil', function () {
    return view('profile.index');
})->name('profile.index');

// SR-11: Identity Verification
Route::get('/verifikasi-identitas', function () {
    return view('profile.verifikasi-identitas');
})->name('profile.verifikasi');
