<?php

use Illuminate\Support\Facades\Route;

// ── Redirect root ke login ─────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Auth Routes ────────────────────────────────────────────────
require __DIR__ . '/User/login_route.php';

// ══════════════════════════════════════════════════════════════
// ADMIN Routes (dipecah per fitur)
// ══════════════════════════════════════════════════════════════
require __DIR__ . '/Admin/dashboard_route.php';  // Dashboard
require __DIR__ . '/Admin/kategori_route.php';   // Manajemen Kategori
require __DIR__ . '/Admin/pengaduan_route.php';  // Manajemen Pengaduan + Export
require __DIR__ . '/Admin/chat_route.php';       // Live Chat Admin
require __DIR__ . '/Admin/log_route.php';        // Audit Log Pengaduan

// ══════════════════════════════════════════════════════════════
// SISWA Routes (dipecah per fitur)
// ══════════════════════════════════════════════════════════════
require __DIR__ . '/Siswa/dashboard_route.php';      // Dashboard
require __DIR__ . '/Siswa/pengaduan_route.php';      // Pengaduan Siswa
require __DIR__ . '/Siswa/chat_route.php';           // Chat Bot & Live Chat
require __DIR__ . '/Siswa/notification_route.php';   // Notifikasi Lonceng
require __DIR__ . '/Siswa/profile_route.php';        // Profil & Ganti Password
