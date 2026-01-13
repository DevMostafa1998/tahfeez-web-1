<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('layouts.dashboard');
});
// ->middleware('auth');
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
