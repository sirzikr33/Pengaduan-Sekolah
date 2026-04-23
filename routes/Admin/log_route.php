<?php

use App\Http\Controllers\Admin\PengaduanLogController;
use Illuminate\Support\Facades\Route;

// ── Admin Audit Logs Route ─────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/logs', [PengaduanLogController::class, 'index'])->name('logs.index');
});
