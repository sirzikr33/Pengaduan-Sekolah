<?php

use App\Http\Controllers\Admin\KategoriController;
use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\Siswa;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// ── User Auth Routes ──────────────────────────────────────────
require __DIR__ . '/User/login_route.php';

// ── Protected Routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalKategori'   => Kategori::count(),
            'totalPengaduan'  => Pengaduan::count(),
            'totalSiswa'      => Siswa::count(),
            'latestKategoris' => Kategori::withCount('pengaduans')->latest()->take(5)->get(),
        ]);
    })->name('dashboard');

    // ── Admin: Kategori CRUD ──────────────────────────────────
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('kategori', KategoriController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
});
