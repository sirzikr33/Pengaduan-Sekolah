@extends('layouts.siswa')

@section('title', 'Pengaduan Saya')
@section('page_title', 'Pengaduan Saya')
@section('breadcrumb', 'Siswa / Pengaduan')

@section('content')
    <div class="section-header">
        <div>
            <div class="section-title">Riwayat Pengaduan</div>
            <div class="section-sub">{{ $pengaduans->total() }} pengaduan tercatat</div>
        </div>
        <a href="{{ route('siswa.chat.index') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Buat Pengaduan
        </a>
    </div>

    <div class="table-wrap">
        @if($pengaduans->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg>
                <p>Belum ada pengaduan. <a href="{{ route('siswa.pengaduan.create') }}" style="color:var(--accent);">Buat pengaduan pertama</a>.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:44px;">#</th>
                        <th>Nama Pengaduan</th>
                        <th>Lokasi</th>
                        <th>Kategori</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="width:90px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaduans as $i => $p)
                        <tr>
                            <td style="color:var(--muted); font-size:0.78rem;">{{ $pengaduans->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $p->nama_pengaduan }}</strong>
                                <div style="font-size:0.75rem; color:var(--muted); margin-top:0.15rem; max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $p->deskripsi ?: '-' }}
                                </div>
                            </td>
                            <td style="color:var(--muted); font-size:0.82rem; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $p->lokasi ?: '-' }}
                            </td>
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

    @if($pengaduans->hasPages())
        <div class="pagination">
            @if($pengaduans->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $pengaduans->previousPageUrl() }}">‹</a>
            @endif

            @foreach($pengaduans->getUrlRange(1, $pengaduans->lastPage()) as $page => $url)
                @if($page == $pengaduans->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($pengaduans->hasMorePages())
                <a href="{{ $pengaduans->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    @endif
@endsection
