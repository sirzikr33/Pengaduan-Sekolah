<?php

use App\Http\Controllers\Admin\PengaduanController;
use Illuminate\Support\Facades\Route;

// ── Admin Pengaduan Routes ─────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Export Excel (HARUS di atas route resource agar tidak dianggap ID pengaduan)
    Route::get('pengaduan/export', [PengaduanController::class, 'export'])
        ->name('pengaduan.export');

    Route::resource('pengaduan', PengaduanController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy']);
});
