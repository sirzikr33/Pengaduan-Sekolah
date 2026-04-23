@extends('layouts.siswa')

@section('title', 'Buat Pengaduan')
@section('page_title', 'Buat Pengaduan')
@section('breadcrumb', 'Siswa / Pengaduan / Buat')

@section('content')
    <div class="form-card">
        <div style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
            <div style="font-size:0.72rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;">
                Form Pengaduan Baru
            </div>
            <div style="font-size:0.78rem; color:var(--muted); margin-top:0.25rem;">
                Isi data pengaduan dengan lengkap dan benar
            </div>
        </div>

        <form method="POST" action="{{ route('siswa.pengaduan.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="nama_pengaduan">Nama / Judul Pengaduan <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="nama_pengaduan"
                    name="nama_pengaduan"
                    class="form-control {{ $errors->has('nama_pengaduan') ? 'is-invalid' : '' }}"
                    value="{{ old('nama_pengaduan') }}"
                    placeholder="Contoh: Kursi kelas X rusak..."
                    autofocus
                >
                @error('nama_pengaduan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="deskripsi">Deskripsi Pengaduan <span style="color:var(--danger);">*</span></label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    class="form-control {{ $errors->has('deskripsi') ? 'is-invalid' : '' }}"
                    rows="4"
                    placeholder="Jelaskan detail kejadian atau masalah yang dilaporkan..."
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="lokasi">Lokasi Pengaduan <span style="color:var(--danger);">*</span></label>
                <input
                    type="text"
                    id="lokasi"
                    name="lokasi"
                    class="form-control {{ $errors->has('lokasi') ? 'is-invalid' : '' }}"
                    value="{{ old('lokasi') }}"
                    placeholder="Contoh: Lab Komputer, Ruang Kelas X RPL 1"
                >
                @error('lokasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="kategori_id">Kategori <span style="color:var(--danger);">*</span></label>
                <select id="kategori_id" name="kategori_id" class="form-control {{ $errors->has('kategori_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="kondisi_pengaduan">Kondisi <span style="color:var(--danger);">*</span></label>
                    <select id="kondisi_pengaduan" name="kondisi_pengaduan" class="form-control {{ $errors->has('kondisi_pengaduan') ? 'is-invalid' : '' }}">
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="ringan" {{ old('kondisi_pengaduan') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="sedang" {{ old('kondisi_pengaduan') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="berat"  {{ old('kondisi_pengaduan') == 'berat'  ? 'selected' : '' }}>Berat</option>
                    </select>
                    @error('kondisi_pengaduan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="tanggal_pengaduan">Tanggal Kejadian <span style="color:var(--danger);">*</span></label>
                    <input
                        type="date"
                        id="tanggal_pengaduan"
                        name="tanggal_pengaduan"
                        class="form-control {{ $errors->has('tanggal_pengaduan') ? 'is-invalid' : '' }}"
                        value="{{ old('tanggal_pengaduan', date('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}"
                    >
                    @error('tanggal_pengaduan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="foto_pengaduan">Foto Bukti <span style="font-size:0.72rem; color:var(--muted);">(opsional, maks 2MB)</span></label>
                <input
                    type="file"
                    id="foto_pengaduan"
                    name="foto_pengaduan"
                    class="form-control {{ $errors->has('foto_pengaduan') ? 'is-invalid' : '' }}"
                    accept="image/jpg,image/jpeg,image/png,image/webp"
                >
                @error('foto_pengaduan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            <div style="display:flex; gap:0.5rem; margin-top:1.5rem;">

                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    Kirim Pengaduan
                </button>
                <a href="{{ route('siswa.pengaduan.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
