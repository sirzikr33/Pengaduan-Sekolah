@extends('layouts.siswa')

@section('title', 'Detail Pengaduan')
@section('page_title', 'Detail Pengaduan')
@section('breadcrumb', 'Siswa / Pengaduan / Detail')

@push('styles')
<style>
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; max-width: 720px; }
    @media (max-width: 600px) { .detail-grid { grid-template-columns: 1fr; } }
    .detail-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 8px rgba(34,139,34,0.05); }
    .detail-card.full { grid-column: 1 / -1; }
    .card-title { font-size: 0.72rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 1rem; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border); }
    .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 0.45rem 0; }
    .detail-row + .detail-row { border-top: 1px solid rgba(26,46,26,0.04); }
    .detail-label { font-size: 0.8rem; color: var(--muted); font-weight: 500; }
    .detail-value { font-size: 0.875rem; color: var(--text); font-weight: 500; text-align: right; }

    /* Progress tracker */
    .status-track { display: flex; align-items: flex-start; gap: 0; margin: 0.5rem 0; }
    .track-step { flex: 1; text-align: center; position: relative; }
    .track-step::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 50%;
        right: -50%;
        height: 2px;
        background: var(--border);
        z-index: 0;
    }
    .track-step:last-child::before { display: none; }
    .track-dot {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.4rem;
        font-size: 0.72rem; font-weight: 600;
        position: relative; z-index: 1;
        border: 2px solid var(--border);
        background: var(--surface);
        color: var(--muted);
    }
    .track-dot.done   { background: var(--accent); border-color: var(--accent); color: #fff; }
    .track-dot.active { background: #3b82f6; border-color: #3b82f6; color: #fff; }
    .track-label { font-size: 0.69rem; color: var(--muted); font-weight: 500; }
    .track-label.done   { color: var(--accent); font-weight: 600; }
    .track-label.active { color: #3b82f6; font-weight: 600; }
</style>
@endpush

@section('content')
    <div class="detail-grid">
        {{-- Info Pengaduan --}}
        <div class="detail-card">
            <div class="card-title">Informasi Pengaduan</div>
            <div class="detail-row">
                <span class="detail-label">Nama Pengaduan</span>
                <span class="detail-value">{{ $pengaduan->nama_pengaduan }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kategori</span>
                <span class="detail-value"><span class="badge badge-gray">{{ $pengaduan->kategori->nama_kategori ?? '-' }}</span></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kondisi</span>
                <span class="detail-value"><span class="badge badge-{{ $pengaduan->kondisi_pengaduan }}">{{ ucfirst($pengaduan->kondisi_pengaduan) }}</span></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal</span>
                <span class="detail-value">{{ $pengaduan->tanggal_pengaduan->format('d F Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Deskripsi</span>
                <span class="detail-value" style="text-align:left; max-width:58%;">{{ $pengaduan->deskripsi ?: '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dikirim</span>
                <span class="detail-value" style="font-size:0.8rem;">{{ $pengaduan->created_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Status Tracker --}}
        <div class="detail-card">
            <div class="card-title">Status Penanganan</div>
            <div class="status-track">
                @php
                    $statuses = ['pending' => 0, 'proses' => 1, 'selesai' => 2];
                    $current  = $statuses[$pengaduan->status] ?? 0;
                @endphp

                <div class="track-step">
                    <div class="track-dot {{ $current >= 0 ? 'done' : '' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                    <div class="track-label {{ $current >= 0 ? 'done' : '' }}">Diterima</div>
                </div>

                <div class="track-step">
                    <div class="track-dot {{ $current >= 1 ? 'active' : '' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
                    </div>
                    <div class="track-label {{ $current >= 1 ? 'active' : '' }}">Diproses</div>
                </div>

                <div class="track-step">
                    <div class="track-dot {{ $current >= 2 ? 'done' : '' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                    <div class="track-label {{ $current >= 2 ? 'done' : '' }}">Selesai</div>
                </div>
            </div>

            <div style="text-align:center; margin-top:1.25rem;">
                <span class="badge badge-{{ $pengaduan->status }}" style="font-size:0.78rem; padding:0.3rem 0.75rem;">
                    Status: {{ ucfirst($pengaduan->status) }}
                </span>
            </div>

            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
                <div class="detail-label" style="margin-bottom:0.35rem;">Catatan Admin</div>
                <div style="font-size:0.84rem; color:var(--text); line-height:1.5;">{{ $pengaduan->catatan ?: '-' }}</div>
            </div>
        </div>

        {{-- Foto --}}
        <div class="detail-card full">
            <div class="card-title">Foto Bukti</div>
            @if($pengaduan->foto_pengaduan && $pengaduan->foto_pengaduan !== 'no-image.jpg')
                <img src="{{ asset('storage/pengaduan/' . $pengaduan->foto_pengaduan) }}"
                     alt="Foto {{ $pengaduan->nama_pengaduan }}"
                     style="max-width:320px; border-radius:10px; border:1px solid var(--border);"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display:none; color:var(--muted); font-size:0.82rem; padding:0.5rem 0;">
                    {{ $pengaduan->foto_pengaduan }}
                </div>
            @else
                <div style="color:var(--muted); font-size:0.84rem;">Tidak ada foto yang diunggah.</div>
            @endif
        </div>
    </div>

    <div style="display:flex; gap:0.5rem; margin-top:1.5rem; flex-wrap:wrap; align-items:center;">
        <a href="{{ route('siswa.pengaduan.index') }}" class="btn btn-ghost">← Kembali</a>

        @if($pengaduan->chatSession)
            <a href="{{ route('siswa.chat.history.show', $pengaduan->chatSession) }}"
               class="btn btn-ghost"
               style="border-color:rgba(59,130,246,0.3); color:#1e40af; background:rgba(59,130,246,0.05);">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:15px;height:15px;">
                    <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                </svg>
                Lihat Log Obrolan
            </a>
        @endif
    </div>
@endsection
