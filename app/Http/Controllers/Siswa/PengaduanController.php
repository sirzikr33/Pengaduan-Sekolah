<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * Daftar pengaduan milik siswa yang login.
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;

        $pengaduans = Pengaduan::with('kategori')
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->paginate(10);

        return view('siswa.pengaduan.index', compact('pengaduans', 'siswa'));
    }

    /**
     * Form buat pengaduan baru.
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('siswa.pengaduan.create', compact('kategoris'));
    }

    /**
     * Simpan pengaduan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengaduan'    => 'required|string|max:255',
            'deskripsi'         => 'required|string|max:2000',
            'lokasi'            => 'required|string|max:255',
            'kategori_id'       => 'required|exists:kategoris,id',
            'kondisi_pengaduan' => 'required|in:berat,sedang,ringan',
            'tanggal_pengaduan' => 'required|date',
            'foto_pengaduan'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama_pengaduan.required'    => 'Nama pengaduan wajib diisi.',
            'nama_pengaduan.max'         => 'Nama pengaduan maksimal 255 karakter.',
            'deskripsi.required'         => 'Deskripsi pengaduan wajib diisi.',
            'deskripsi.max'              => 'Deskripsi pengaduan maksimal 2000 karakter.',
            'lokasi.required'            => 'Lokasi pengaduan wajib diisi.',
            'lokasi.max'                 => 'Lokasi pengaduan maksimal 255 karakter.',
            'kategori_id.required'       => 'Kategori wajib dipilih.',
            'kategori_id.exists'         => 'Kategori tidak valid.',
            'kondisi_pengaduan.required' => 'Kondisi pengaduan wajib dipilih.',
            'kondisi_pengaduan.in'       => 'Nilai kondisi tidak valid.',
            'tanggal_pengaduan.required' => 'Tanggal pengaduan wajib diisi.',
            'tanggal_pengaduan.date'     => 'Format tanggal tidak valid.',
            'foto_pengaduan.image'       => 'File harus berupa gambar.',
            'foto_pengaduan.max'         => 'Ukuran foto maksimal 2MB.',
        ]);

        $siswa = Auth::user()->siswa;

        // Upload foto jika ada
        $namaFoto = null;
        if ($request->hasFile('foto_pengaduan')) {
            $namaFoto = $request->file('foto_pengaduan')->store('pengaduan', 'public');
            $namaFoto = basename($namaFoto);
        }

        Pengaduan::create([
            'siswa_id'          => $siswa->id,
            'kategori_id'       => $request->kategori_id,
            'nama_pengaduan'    => $request->nama_pengaduan,
            'deskripsi'         => $request->deskripsi,
            'lokasi'            => $request->lokasi,
            'foto_pengaduan'    => $namaFoto ?? 'no-image.jpg',
            'status'            => 'pending',
            'kondisi_pengaduan' => $request->kondisi_pengaduan,
            'tanggal_pengaduan' => $request->tanggal_pengaduan,
        ]);

        return redirect()->route('siswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dikirim! Status: Pending.');
    }

    /**
     * Detail pengaduan milik siswa.
     */
    public function show(Pengaduan $pengaduan)
    {
        $siswa = Auth::user()->siswa;

        // Pastikan hanya bisa lihat pengaduan milik sendiri
        if ($pengaduan->siswa_id !== $siswa->id) {
            abort(403, 'Pengaduan ini bukan milik Anda.');
        }

        $pengaduan->load('kategori', 'chatSession');
        return view('siswa.pengaduan.show', compact('pengaduan', 'siswa'));
    }
}
