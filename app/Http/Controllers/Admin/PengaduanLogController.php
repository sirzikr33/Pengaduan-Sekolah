<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaduanLog;

class PengaduanLogController extends Controller
{
    public function index()
    {
        // Mengambil data log beserta relasi tabel pengaduan dan data siswa yang melapor
        $logs = PengaduanLog::with(['pengaduan.siswa', 'pengaduan.kategori'])
            ->orderBy('changed_at', 'desc')
            ->paginate(15);

        return view('admin.log.index', compact('logs'));
    }
}
