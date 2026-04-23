<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// ── Siswa Chat Routes ──────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/',              [ChatController::class, 'index'])->name('index');
        Route::post('/start',        [ChatController::class, 'startSession'])->name('start');
        Route::post('/send',         [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/upload-photo', [ChatController::class, 'uploadPhoto'])->name('upload-photo');
        Route::get('/messages',      [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/escalate',     [ChatController::class, 'escalate'])->name('escalate');
        Route::post('/afk-warning',  [ChatController::class, 'afkWarning'])->name('afk-warning');
        Route::post('/auto-resolve', [ChatController::class, 'autoResolve'])->name('auto-resolve');
        Route::get('/history',       [ChatController::class, 'history'])->name('history');
        Route::get('/history/{chatSession}',[ChatController::class, 'showHistory'])->name('history.show');
    });
});
