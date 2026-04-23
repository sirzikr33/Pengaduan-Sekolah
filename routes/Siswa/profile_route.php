<?php

use App\Http\Controllers\Siswa\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Siswa Profile Route ─────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [ProfileController::class, 'update'])->name('profile.update');
});
