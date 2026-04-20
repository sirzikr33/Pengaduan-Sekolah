@extends('layouts.admin')

@section('title', 'Detail Pengaduan')
@section('page_title', 'Detail Pengaduan')
@section('breadcrumb', 'Data / Pengaduan / Detail')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        max-width: 780px;
    }
    @media (max-width: 640px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
    .detail-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
    }
    .detail-card.full { grid-column: 1 / -1; }
    .detail-card-title {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid var(--border);
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.5rem 0;
    }
    .detail-row + .detail-row {
        border-top: 1px solid rgba(255,255,255,0.03);
    }
    .detail-label {
        font-size: 0.8rem;
        color: var(--muted);
        font-weight: 500;
        min-width: 120px;
    }
    .detail-value {
        font-size: 0.875rem;
        color: var(--text);
        text-align: right;
        font-weight: 500;
    }

    /* Status badges */
    .badge-pending  { background: rgba(217,119,6,0.1);   color: #92400e; }
    .badge-proses   { background: rgba(59,130,246,0.1);  color: #1e40af; }
    .badge-selesai  { background: rgba(34,166,69,0.1);   color: #166534; }
    .badge-berat    { background: rgba(220,38,38,0.08);  color: #991b1b; }
    .badge-sedang   { background: rgba(217,119,6,0.1);   color: #92400e; }
    .badge-ringan   { background: rgba(59,130,246,0.1);  color: #1e40af; }

    .photo-preview {
        width: 100%;
        max-width: 320px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--surface2);
        margin-top: 0.5rem;
    }

    .actions-row {
        display: flex;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }
</style>
@endpush

@section('content')
    <div class="detail-grid">
        {{-- Pengaduan Info --}}
        <div class="detail-card">
            <div class="detail-card-title">Informasi Pengaduan</div>
            <div class="detail-row">
                <span class="detail-label">Nama Pengaduan</span>
                <span class="detail-value">{{ $pengaduan->nama_pengaduan }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kategori</span>
                <span class="detail-value">
                    <span class="badge badge-gray">{{ $pengaduan->kategori->nama_kategori ?? '-' }}</span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $pengaduan->status }}">{{ ucfirst($pengaduan->status) }}</span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kondisi</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $pengaduan->kondisi_pengaduan }}">{{ ucfirst($pengaduan->kondisi_pengaduan) }}</span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal</span>
                <span class="detail-value">{{ $pengaduan->tanggal_pengaduan->format('d F Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Deskripsi</span>
                <span class="detail-value" style="text-align:left; max-width:60%;">{{ $pengaduan->deskripsi ?: '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Catatan Admin</span>
                <span class="detail-value" style="text-align:left; max-width:60%;">{{ $pengaduan->catatan ?: '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dibuat</span>
                <span class="detail-value">{{ $pengaduan->created_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Siswa Info --}}
        <div class="detail-card">
            <div class="detail-card-title">Informasi Siswa</div>
            <div class="detail-row">
                <span class="detail-label">Nama</span>
                <span class="detail-value">{{ $pengaduan->siswa->nama ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">NISN</span>
                <span class="detail-value" style="font-family: monospace;">{{ $pengaduan->siswa->nisn ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kelas</span>
                <span class="detail-value">{{ $pengaduan->siswa->kelas ?? '-' }}</span>
            </div>
        </div>

        {{-- Photo --}}
        <div class="detail-card full">
            <div class="detail-card-title">Foto Pengaduan</div>
            @if($pengaduan->foto_pengaduan)
                <img src="{{ asset('storage/pengaduan/' . $pengaduan->foto_pengaduan) }}"
                     alt="Foto {{ $pengaduan->nama_pengaduan }}"
                     class="photo-preview"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display:none; color:var(--muted); font-size:0.82rem; padding:1rem 0;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;opacity:0.5;"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                    {{ $pengaduan->foto_pengaduan }}
                </div>
            @else
                <div style="color:var(--muted); font-size:0.84rem; padding:1rem 0;">Tidak ada foto.</div>
            @endif
        </div>
    </div>

    <div class="actions-row">
        <a href="{{ route('admin.pengaduan.edit', $pengaduan) }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            Edit Pengaduan
        </a>
        <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-ghost">Kembali</a>
    </div>
@endsection
