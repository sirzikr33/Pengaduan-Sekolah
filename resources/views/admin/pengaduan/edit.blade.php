@extends('layouts.admin')

@section('title', 'Edit Pengaduan')
@section('page_title', 'Edit Pengaduan')
@section('breadcrumb', 'Data / Pengaduan / Edit')

@push('styles')
<style>
    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        max-width: 780px;
    }
    @media (max-width: 640px) {
        .edit-grid { grid-template-columns: 1fr; }
    }
    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
    }
    .info-card-title {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid var(--border);
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.45rem 0;
        font-size: 0.84rem;
    }
    .info-row .label { color: var(--muted); }
    .info-row .value { color: var(--text); font-weight: 500; }

    .form-card-wide {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.75rem;
        max-width: 780px;
        margin-top: 1.5rem;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (max-width: 640px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    {{-- Read-only info --}}
    <div class="edit-grid">
        <div class="info-card">
            <div class="info-card-title">Info Pengaduan</div>
            <div class="info-row">
                <span class="label">Nama Pengaduan</span>
                <span class="value">{{ $pengaduan->nama_pengaduan }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal</span>
                <span class="value">{{ $pengaduan->tanggal_pengaduan->format('d M Y') }}</span>
            </div>
            <div class="info-row" style="align-items:flex-start; gap:0.75rem;">
                <span class="label">Deskripsi</span>
                <span class="value" style="text-align:right; max-width:70%;">{{ $pengaduan->deskripsi ?: '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Foto</span>
                <span class="value" style="font-size:0.78rem;">{{ $pengaduan->foto_pengaduan }}</span>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-title">Info Siswa</div>
            <div class="info-row">
                <span class="label">Nama</span>
                <span class="value">{{ $pengaduan->siswa->nama ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">NISN</span>
                <span class="value" style="font-family:monospace;">{{ $pengaduan->siswa->nisn ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Kelas</span>
                <span class="value">{{ $pengaduan->siswa->kelas ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Editable form --}}
    <div class="form-card-wide">
        <div class="info-card-title" style="border-bottom:1px solid var(--border); padding-bottom:0.6rem; margin-bottom:1.25rem;">
            Ubah Data Pengaduan
        </div>
        <form method="POST" action="{{ route('admin.pengaduan.update', $pengaduan) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}">
                        <option value="proses" {{ old('status', $pengaduan->status) == 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ old('status', $pengaduan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="catatan">Catatan</label>
                    <textarea
                        id="catatan"
                        name="catatan"
                        class="form-control {{ $errors->has('catatan') ? 'is-invalid' : '' }}"
                        rows="4"
                        placeholder="Tambahkan catatan proses untuk pengaduan ini..."
                    >{{ old('catatan', $pengaduan->catatan) }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display:flex; gap:0.5rem; margin-top:1rem;">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
