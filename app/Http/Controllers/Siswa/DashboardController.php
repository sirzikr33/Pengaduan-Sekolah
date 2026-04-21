<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        $totalPengaduan = Pengaduan::where('siswa_id', $siswa->id)->count();
        $pending        = Pengaduan::where('siswa_id', $siswa->id)->where('status', 'pending')->count();
        $proses         = Pengaduan::where('siswa_id', $siswa->id)->where('status', 'proses')->count();
        $selesai        = Pengaduan::where('siswa_id', $siswa->id)->where('status', 'selesai')->count();

        // 1. Data untuk "Progress Perbaikan Aktif" (Pengaduan terbaru yang pending/proses)
        $activePengaduan = Pengaduan::with('kategori')
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['pending', 'proses'])
            ->latest()
            ->first();

        // 2. Data Kalender model GitHub (Heatmap)
        // Ambil semua tanggal pengaduan dalam setahun terakhir, kelompokkan per hari
        $calendarDataRaw = Pengaduan::where('siswa_id', $siswa->id)
            ->where('tanggal_pengaduan', '>=', now()->subMonths(12))
            ->selectRaw('DATE(tanggal_pengaduan) as date, count(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // 3. Data untuk Chart (Pie, Bar, Line)
        // Pie: Berdasarkan kondisi
        $chartKondisi = Pengaduan::where('siswa_id', $siswa->id)
            ->selectRaw('kondisi_pengaduan, count(*) as total')
            ->groupBy('kondisi_pengaduan')
            ->pluck('total', 'kondisi_pengaduan');

        // Bar: Berdasarkan Kategori
        $chartKategori = Pengaduan::where('siswa_id', $siswa->id)
            ->join('kategoris', 'pengaduans.kategori_id', '=', 'kategoris.id')
            ->selectRaw('kategoris.nama_kategori, count(pengaduans.id) as total')
            ->groupBy('kategoris.nama_kategori')
            ->pluck('total', 'nama_kategori');

        // Line: Berdasarkan Bulan di Tahun Ini
        $chartBulan = Pengaduan::where('siswa_id', $siswa->id)
            ->whereYear('tanggal_pengaduan', date('Y'))
            ->selectRaw('MONTH(tanggal_pengaduan) as bulan, count(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Siapkan array bulan 1-12
        $bulans = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulans[$i] = $chartBulan[$i] ?? 0;
        }

        // 4. Tabel Histori (Dikecilkan)
        $recentPengaduan = Pengaduan::with('kategori')
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact(
            'siswa',
            'totalPengaduan',
            'pending',
            'proses',
            'selesai',
            'recentPengaduan',
            'activePengaduan',
            'calendarDataRaw',
            'chartKondisi',
            'chartKategori',
            'bulans'
        ));
    }
}
