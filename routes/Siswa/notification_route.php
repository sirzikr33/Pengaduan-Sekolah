<?php

use App\Http\Controllers\Siswa\NotificationController;
use Illuminate\Support\Facades\Route;

// ── Siswa Notification Routes ──────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',          [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-read',[NotificationController::class, 'markRead'])->name('mark-read');
        Route::post('/mark-all', [NotificationController::class, 'markAll'])->name('mark-all');
    });
});
