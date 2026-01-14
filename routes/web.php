<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('layouts.dashboard');
    })->name('dashboard');
    Route::get('/user', function () {
        return view('users.index');
    })->name('user');
});
//Yahya Route
Route::get('/user/create', function () {
    return view('users.create');
})->name('user.create');
// Route::get('/', function () {
// return view('layouts.dashboard');
// })->name('dashboard');
// // ->middleware('auth');
// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');
// Route::get('/user', function () {
//     return view('users.index');
// })->name('user');
