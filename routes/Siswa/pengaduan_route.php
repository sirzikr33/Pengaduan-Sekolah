<?php

use App\Http\Controllers\Siswa\PengaduanController as SiswaPengaduan;
use Illuminate\Support\Facades\Route;

// ── Siswa Pengaduan Routes ─────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::resource('pengaduan', SiswaPengaduan::class)
        ->only(['index', 'show']);
});
