<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

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

        $pengaduan->update([
            'status'  => $request->status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan berhasil diperbarui.');
    }

    /**
     * Hapus pengaduan (web).
     */
    public function destroy(Pengaduan $pengaduan)
    {
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
