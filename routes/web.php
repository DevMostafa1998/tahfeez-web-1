<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.dashboard');
})->name('dashboard');
// ->middleware('auth');
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/user', function () {
    return view('users.index');
})->name('user');
Route::get('/user/create', function () {
    return view('users.create');
})->name('user.create');
