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
            'recentPengaduan'
        ));
    }
}
