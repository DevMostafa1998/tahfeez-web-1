<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->name('dashboard');
// ->middleware('auth');
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/user', function () {
    return view('users.index');
})->name('user');
