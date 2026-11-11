<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/rute', 'rute');
Route::view('/rute/bandingkan', 'rute.bandingkan');
