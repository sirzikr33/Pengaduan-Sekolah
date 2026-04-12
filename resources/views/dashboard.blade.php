@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('breadcrumb', 'Beranda / Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5S15.01 22 17.5 22s4.5-2.01 4.5-4.5S19.99 13 17.5 13zm0 7c-1.38 0-2.5-1.12-2.5-2.5S16.12 15 17.5 15s2.5 1.12 2.5 2.5S18.88 20 17.5 20zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>
            </div>
            <div class="stat-label">Total Kategori</div>
            <div class="stat-value">{{ $totalKategori }}</div>
            <div class="stat-sub">jenis pengaduan</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            </div>
            <div class="stat-label">Total Pengaduan</div>
            <div class="stat-value">{{ $totalPengaduan }}</div>
            <div class="stat-sub">laporan masuk</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            </div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalSiswa }}</div>
            <div class="stat-sub">pengguna terdaftar</div>
        </div>
    </div>

    <div class="table-wrap">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div class="section-title">Kategori Terbaru</div>
                <div class="section-sub">Daftar kategori pengaduan</div>
            </div>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>

        @if($latestKategoris->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84z"/></svg>
                <p>Belum ada kategori.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Pengaduan</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestKategoris as $i => $k)
                        <tr>
                            <td style="color:var(--muted); font-size:0.8rem;">{{ $i + 1 }}</td>
                            <td><strong>{{ $k->nama_kategori }}</strong></td>
                            <td><span class="badge badge-blue">{{ $k->pengaduans_count }}</span></td>
                            <td style="color:var(--muted);">{{ $k->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
