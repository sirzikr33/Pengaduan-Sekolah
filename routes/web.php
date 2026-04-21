<?php

use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\PengaduanController as SiswaPengaduan;
use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\Siswa;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// ── Auth Routes ───────────────────────────────────────────────
require __DIR__ . '/User/login_route.php';

// ──────────────────────────────────────────────────────────────
// ADMIN Routes
// ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalKategori'   => Kategori::count(),
            'totalPengaduan'  => Pengaduan::count(),
            'totalSiswa'      => Siswa::count(),
            'latestKategoris' => Kategori::withCount('pengaduans')->latest()->take(5)->get(),
        ]);
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('kategori', KategoriController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('pengaduan', PengaduanController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy']);

        // ── Admin Chat Routes ──
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/',                              [AdminChatController::class, 'index'])->name('index');
            Route::get('/queue-count',                   [AdminChatController::class, 'queueCount'])->name('queue-count');
            Route::post('/{chatSession}/accept',         [AdminChatController::class, 'accept'])->name('accept');
            Route::get('/{chatSession}',                 [AdminChatController::class, 'show'])->name('show');
            Route::post('/{chatSession}/send',           [AdminChatController::class, 'sendMessage'])->name('send');
            Route::get('/{chatSession}/messages',        [AdminChatController::class, 'getMessages'])->name('messages');
            Route::post('/{chatSession}/resolve',        [AdminChatController::class, 'resolve'])->name('resolve');
        });
    });
});

// ──────────────────────────────────────────────────────────────
// SISWA Routes
// ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

    Route::resource('pengaduan', SiswaPengaduan::class)
        ->only(['index', 'create', 'store', 'show']);

    // ── Siswa Chat Routes ──
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/',             [ChatController::class, 'index'])->name('index');
        Route::post('/start',       [ChatController::class, 'startSession'])->name('start');
        Route::post('/send',        [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/upload-photo',[ChatController::class, 'uploadPhoto'])->name('upload-photo');
        Route::get('/messages',     [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/escalate',    [ChatController::class, 'escalate'])->name('escalate');
        Route::get('/history',              [ChatController::class, 'history'])->name('history');
        Route::get('/history/{chatSession}',[ChatController::class, 'showHistory'])->name('history.show');
    });
});
