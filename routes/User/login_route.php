<?php

use App\Http\Controllers\User\LoginController;
use Illuminate\Support\Facades\Route;

// Auth - Login & Logout
// Dihilangkan middleware 'guest' karena default Laravel = redirect ke /dashboard
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Akses darurat untuk logout bila terjebak 403 di HP (Bisa dari ketik URL manual)
Route::get('/logout', [LoginController::class, 'logout']);
