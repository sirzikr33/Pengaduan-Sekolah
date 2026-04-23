<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan. Hubungi Admin.');
        }

        // ── Stat Cards (perspektif siswa) ─────────────────────────
        $total   = Pengaduan::where('siswa_id', $siswa->id)->count();
        $pending = Pengaduan::where('siswa_id', $siswa->id)->where('status', 'pending')->count();
        $proses  = Pengaduan::where('siswa_id', $siswa->id)->where('status', 'proses')->count();
        $selesai = Pengaduan::where('siswa_id', $siswa->id)->where('status', 'selesai')->count();

        // Tingkat penyelesaian (%)
        $resolutionRate = $total > 0 ? round(($selesai / $total) * 100) : 0;

        // ── Pengaduan Aktif (terbaru yang belum selesai) ──────────
        $activePengaduan = Pengaduan::with('kategori')
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['pending', 'proses'])
            ->latest()
            ->first();

        // ── Kalender Heatmap (bulan ini) ──────────────────────────
        $calendarDataRaw = Pengaduan::where('siswa_id', $siswa->id)
            ->where('tanggal_pengaduan', '>=', now()->startOfMonth())
            ->where('tanggal_pengaduan', '<=', now()->endOfMonth())
            ->selectRaw('DATE(tanggal_pengaduan) as date, count(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // ── Chart: Pie Kondisi (siswa ini) ────────────────────────
        $chartKondisi = Pengaduan::where('siswa_id', $siswa->id)
            ->selectRaw('kondisi_pengaduan, count(*) as total')
            ->groupBy('kondisi_pengaduan')
            ->pluck('total', 'kondisi_pengaduan');

        // ── Chart: Bar Kategori (siswa ini) ───────────────────────
        $chartKategori = Pengaduan::where('siswa_id', $siswa->id)
            ->join('kategoris', 'pengaduans.kategori_id', '=', 'kategoris.id')
            ->selectRaw('kategoris.nama_kategori, count(pengaduans.id) as total')
            ->groupBy('kategoris.nama_kategori')
            ->orderByDesc('total')
            ->pluck('total', 'nama_kategori');

        // ── Chart: Line Tren Bulanan Tahun Ini (siswa ini) ────────
        $chartBulan = Pengaduan::where('siswa_id', $siswa->id)
            ->whereYear('tanggal_pengaduan', date('Y'))
            ->selectRaw('MONTH(tanggal_pengaduan) as bulan, count(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $bulans = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulans[$i] = $chartBulan[$i] ?? 0;
        }

        // ── Riwayat Terbaru ───────────────────────────────────────
        $recentPengaduan = Pengaduan::with('kategori')
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->take(5)
            ->get();

        // ── Sesi Chat Aktif ───────────────────────────────────────
        $activeChat = ChatSession::where('user_id', Auth::id())
            ->whereIn('status', ['active', 'queued'])
            ->latest()
            ->first();

        return view('siswa.dashboard', compact(
            'siswa', 'total', 'pending', 'proses', 'selesai',
            'resolutionRate', 'activePengaduan',
            'calendarDataRaw', 'chartKondisi', 'chartKategori', 'bulans',
            'recentPengaduan', 'activeChat'
        ));
    }
}
