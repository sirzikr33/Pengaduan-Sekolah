<?php

use App\Http\Controllers\Admin\PengaduanController;
use Illuminate\Support\Facades\Route;

// ── Pengaduan API ────────────────────────────────────────
Route::prefix('pengaduan')->group(function () {
    Route::get('/',         [PengaduanController::class, 'apiIndex'])->name('api.pengaduan.index');
    Route::get('/{pengaduan}',  [PengaduanController::class, 'apiShow'])->name('api.pengaduan.show');
    Route::put('/{pengaduan}',  [PengaduanController::class, 'apiUpdate'])->name('api.pengaduan.update');
    Route::delete('/{pengaduan}', [PengaduanController::class, 'apiDestroy'])->name('api.pengaduan.destroy');
});
