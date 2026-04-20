@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Dashboard')
@section('breadcrumb', 'Beranda / Dashboard')

@section('content')
    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon gray">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg>
            </div>
            <div class="stat-label">Total Pengaduan</div>
            <div class="stat-value">{{ $totalPengaduan }}</div>
            <div class="stat-sub">semua pengaduan</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $pending }}</div>
            <div class="stat-sub">belum diproses</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <div class="stat-label">Diproses</div>
            <div class="stat-value">{{ $proses }}</div>
            <div class="stat-sub">sedang ditangani</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
            </div>
            <div class="stat-label">Selesai</div>
            <div class="stat-value">{{ $selesai }}</div>
            <div class="stat-sub">sudah ditangani</div>
        </div>
    </div>

    {{-- Pengaduan Terbaru --}}
    <div class="section-header">
        <div>
            <div class="section-title">Pengaduan Terbaru</div>
            <div class="section-sub">5 pengaduan terakhir Anda</div>
        </div>
        <a href="{{ route('siswa.pengaduan.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Buat Pengaduan
        </a>
    </div>

    <div class="table-wrap">
        @if($recentPengaduan->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg>
                <p>Belum ada pengaduan. <a href="{{ route('siswa.pengaduan.create') }}" style="color:var(--accent);">Buat pengaduan pertama</a>.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nama Pengaduan</th>
                        <th>Kategori</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPengaduan as $p)
                        <tr>
                            <td><strong>{{ $p->nama_pengaduan }}</strong></td>
                            <td><span class="badge badge-gray">{{ $p->kategori->nama_kategori ?? '-' }}</span></td>
                            <td><span class="badge badge-{{ $p->kondisi_pengaduan }}">{{ ucfirst($p->kondisi_pengaduan) }}</span></td>
                            <td><span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                            <td style="color:var(--muted); font-size:0.82rem;">{{ $p->tanggal_pengaduan->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('siswa.pengaduan.show', $p) }}" class="btn btn-ghost btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($totalPengaduan > 5)
        <div style="margin-top: 0.75rem;">
            <a href="{{ route('siswa.pengaduan.index') }}" class="btn btn-ghost btn-sm">
                Lihat semua pengaduan →
            </a>
        </div>
    @endif
@endsection
