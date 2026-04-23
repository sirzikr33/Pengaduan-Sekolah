@extends('layouts.admin')

@section('title', 'Audit Log Pengaduan')
@section('page_title', 'Audit Log')
@section('breadcrumb', 'Sistem / Audit Log')

@push('styles')
<style>
    .badge-pending  { background: rgba(217,119,6,0.1);   color: #92400e; }
    .badge-proses   { background: rgba(59,130,246,0.1);  color: #1e40af; }
    .badge-selesai  { background: rgba(34,166,69,0.1);   color: #166534; }
    .table-responsive { overflow-x: auto; }
</style>
@endpush

@section('content')
    <div class="section-header">
        <div>
            <div class="section-title">Riwayat Perubahan Status</div>
        </div>
    </div>

    <div class="table-wrap">
        @if($logs->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                <p>Belum ada riwayat log perubahan status.</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width:44px;">#</th>
                            <th>Waktu (WIB)</th>
                            <th>ID Pengaduan</th>
                            <th>Siswa Pelapor</th>
                            <th>Kategori</th>
                            <th>Status Lama</th>
                            <th>Status Baru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $i => $log)
                            <tr>
                                <td style="color:var(--muted); font-size:0.78rem;">{{ $logs->firstItem() + $i }}</td>
                                <td style="color:var(--muted); font-size:0.82rem;">{{ $log->changed_at->format('d M Y H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('admin.pengaduan.show', $log->pengaduan_id) }}" style="color: var(--accent); font-weight: 500; text-decoration: none;">
                                        #{{ substr($log->pengaduan_id, 0, 8) }}
                                    </a>
                                </td>
                                <td>
                                    <div style="line-height:1.3;">
                                        @if($log->pengaduan && $log->pengaduan->siswa)
                                            <div style="font-weight:500;">{{ $log->pengaduan->siswa->nama }}</div>
                                            <div style="font-size:0.72rem; color:var(--muted);">{{ $log->pengaduan->siswa->kelas }}</div>
                                        @else
                                            <div style="font-weight:500; color:var(--muted);">-</div>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="badge badge-gray">{{ $log->pengaduan && $log->pengaduan->kategori ? $log->pengaduan->kategori->nama_kategori : '-' }}</span></td>
                                <td>
                                    <span class="badge badge-{{ strtolower($log->status_lama) }}">
                                        {{ ucfirst($log->status_lama) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ strtolower($log->status_baru) }}">
                                        {{ ucfirst($log->status_baru) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($logs->hasPages())
        <div class="pagination">
            @if($logs->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}">‹</a>
            @endif

            @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                @if($page == $logs->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    @endif
@endsection
