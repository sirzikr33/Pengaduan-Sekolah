<?php

use App\Http\Controllers\Admin\KategoriController;
use Illuminate\Support\Facades\Route;

// ── Admin Kategori Routes ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('kategori', KategoriController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});
