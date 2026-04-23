<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PengaduanExport;
use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Notifications\PengaduanStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PengaduanController extends Controller
{
    /**
     * Daftar semua pengaduan (web view).
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with(['siswa', 'kategori'])->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi_pengaduan', $request->kondisi);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengaduan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('siswa', fn($s) => $s->where('nama', 'like', "%{$search}%"));
            });
        }

        $pengaduans = $query->paginate(10)->withQueryString();
        $totalPengaduan = Pengaduan::count();
        $pendingPengaduan = Pengaduan::where('status', 'pending')->count();
        $prosesPengaduan = Pengaduan::where('status', 'proses')->count();
        $selesaiPengaduan = Pengaduan::where('status', 'selesai')->count();

        return view('admin.pengaduan.index', compact('pengaduans', 'totalPengaduan', 'pendingPengaduan', 'prosesPengaduan', 'selesaiPengaduan'));
    }

    /**
     * Detail pengaduan (web view).
     */
    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load(['siswa', 'kategori']);
        return view('admin.pengaduan.show', compact('pengaduan'));
    }

    /**
     * Form edit pengaduan (web view).
     */
    public function edit(Pengaduan $pengaduan)
    {
        $pengaduan->load(['siswa', 'kategori']);
        return view('admin.pengaduan.edit', compact('pengaduan'));
    }

    /**
     * Update pengaduan (web).
     */
    public function update(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status'  => 'required|in:proses,selesai',
            'catatan' => 'nullable|string|max:2000',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status hanya dapat diubah ke proses atau selesai.',
            'catatan.max'     => 'Catatan maksimal 2000 karakter.',
        ]);

        $statusLama = $pengaduan->status;

        $pengaduan->update([
            'status'  => $request->status,
            'catatan' => $request->catatan,
        ]);

        // Fitur 3: Kirim notifikasi ke siswa jika status berubah
        if ($statusLama !== $request->status && $pengaduan->siswa && $pengaduan->siswa->user) {
            $pengaduan->siswa->user->notify(new PengaduanStatusChanged($pengaduan, $statusLama));
        }

        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan berhasil diperbarui dan siswa telah diberitahu.');
    }

    /**
     * Fitur 2: Export rekap pengaduan ke Excel.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'kondisi', 'search']);
        $filename = 'rekap-pengaduan-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PengaduanExport($filters), $filename);
    }

    /**
     * Hapus pengaduan (web).
     */
    public function destroy(Pengaduan $pengaduan)
    {
        // Bug #1 Fix: Hapus file foto fisik dari storage sebelum hapus record
        if ($pengaduan->foto_pengaduan && $pengaduan->foto_pengaduan !== 'no-image.jpg') {
            $fotoPath = 'pengaduan/' . $pengaduan->foto_pengaduan;
            if (Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
        }

        $pengaduan->delete();

        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    // ════════════════════════════════════════════════════════
    // API Endpoints (JSON)
    // ════════════════════════════════════════════════════════

    /**
     * API: Get all pengaduan.
     */
    public function apiIndex(Request $request)
    {
        $query = Pengaduan::with(['siswa', 'kategori'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi_pengaduan', $request->kondisi);
        }

        $pengaduans = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $pengaduans,
        ]);
    }

    /**
     * API: Get single pengaduan.
     */
    public function apiShow(Pengaduan $pengaduan)
    {
        $pengaduan->load(['siswa', 'kategori']);

        return response()->json([
            'success' => true,
            'data'    => $pengaduan,
        ]);
    }

    /**
     * API: Update pengaduan.
     */
    public function apiUpdate(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status'            => 'sometimes|in:pending,proses,selesai',
            'kondisi_pengaduan' => 'sometimes|in:berat,sedang,ringan',
            'kategori_id'       => 'sometimes|exists:kategoris,id',
        ]);

        $pengaduan->update($request->only(['status', 'kondisi_pengaduan', 'kategori_id']));
        $pengaduan->load(['siswa', 'kategori']);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil diperbarui.',
            'data'    => $pengaduan,
        ]);
    }

    /**
     * API: Delete pengaduan.
     */
    public function apiDestroy(Pengaduan $pengaduan)
    {
        $pengaduan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dihapus.',
        ]);
    }
}
