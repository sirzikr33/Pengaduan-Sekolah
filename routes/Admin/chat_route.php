<?php

use App\Http\Controllers\Admin\ChatController as AdminChatController;
use Illuminate\Support\Facades\Route;

// ── Admin Live Chat Routes ─────────────────────────────────────
Route::prefix('admin/chat')->name('admin.chat.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/',                              [AdminChatController::class, 'index'])->name('index');
    Route::get('/queue-count',                   [AdminChatController::class, 'queueCount'])->name('queue-count');
    Route::post('/{chatSession}/accept',         [AdminChatController::class, 'accept'])->name('accept');
    Route::get('/{chatSession}',                 [AdminChatController::class, 'show'])->name('show');
    Route::post('/{chatSession}/send',           [AdminChatController::class, 'sendMessage'])->name('send');
    Route::get('/{chatSession}/messages',        [AdminChatController::class, 'getMessages'])->name('messages');
    Route::post('/{chatSession}/resolve',        [AdminChatController::class, 'resolve'])->name('resolve');
});
