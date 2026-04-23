<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni   = now()->month;
        $tahunIni   = now()->year;
        $bulanLalu  = now()->subMonth()->month;
        $tahunLalu  = now()->subMonth()->year;

        // ── Stat Cards ─────────────────────────────────────────────
        $totalPengaduan  = Pengaduan::count();
        $pendingCount    = Pengaduan::where('status', 'pending')->count();
        $prosesCount     = Pengaduan::where('status', 'proses')->count();
        $selesaiCount    = Pengaduan::where('status', 'selesai')->count();
        $totalSiswa      = Siswa::count();
        $totalKategori   = Kategori::count();

        // Pengaduan bulan ini vs bulan lalu (untuk persentase tren)
        $bulaniniTotal   = Pengaduan::whereMonth('created_at', $bulanIni)
                                     ->whereYear('created_at', $tahunIni)->count();
        $bulanlaluTotal  = Pengaduan::whereMonth('created_at', $bulanLalu)
                                     ->whereYear('created_at', $tahunLalu)->count();
        $trendPct = $bulanlaluTotal > 0
            ? round((($bulaniniTotal - $bulanlaluTotal) / $bulanlaluTotal) * 100, 1)
            : ($bulaniniTotal > 0 ? 100 : 0);

        // ── Chart: Bar Pengaduan per Hari (30 hari terakhir) ──────
        $last30 = Pengaduan::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as tgl, count(*) as total')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('total', 'tgl');

        $days30labels = [];
        $days30vals   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $days30labels[] = now()->subDays($i)->format('d/m');
            $days30vals[]   = $last30[$d] ?? 0;
        }

        // ── Chart: Doughnut Status ────────────────────────────────
        $statusChart = [
            'pending' => $pendingCount,
            'proses'  => $prosesCount,
            'selesai' => $selesaiCount,
        ];

        // ── Chart: Bar Kategori (semua waktu) ────────────────────
        $chartKategori = Pengaduan::join('kategoris', 'pengaduans.kategori_id', '=', 'kategoris.id')
            ->selectRaw('kategoris.nama_kategori, count(pengaduans.id) as total')
            ->groupBy('kategoris.nama_kategori')
            ->orderByDesc('total')
            ->pluck('total', 'nama_kategori');

        // ── Chart: Kondisi kekerasan bulan ini ────────────────────
        $kondisiChart = Pengaduan::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->selectRaw('kondisi_pengaduan, count(*) as total')
            ->groupBy('kondisi_pengaduan')
            ->pluck('total', 'kondisi_pengaduan');

        // ── Chart: Line tren 12 bulan ─────────────────────────────
        $tren12raw = Pengaduan::where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, count(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $tren12labels = [];
        $tren12vals   = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $tren12labels[] = now()->subMonths($i)->translatedFormat('M Y');
            $tren12vals[]   = $tren12raw[$key] ?? 0;
        }

        // ── Pengaduan Terbaru (5) ─────────────────────────────────
        $recentPengaduan = Pengaduan::with(['siswa', 'kategori'])
            ->whereIn('status', ['pending', 'proses'])
            ->latest()
            ->take(6)
            ->get();

        // ── Chat Antrean ──────────────────────────────────────────
        $chatQueue = \App\Models\ChatSession::where('status', 'queued')->count();

        return view('dashboard', compact(
            'totalPengaduan', 'pendingCount', 'prosesCount', 'selesaiCount',
            'totalSiswa', 'totalKategori',
            'bulaniniTotal', 'trendPct',
            'days30labels', 'days30vals',
            'statusChart', 'chartKategori', 'kondisiChart',
            'tren12labels', 'tren12vals',
            'recentPengaduan', 'chatQueue'
        ));
    }
}
