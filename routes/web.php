<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/keamanan', 'Keamanan')->name('keamanan');
