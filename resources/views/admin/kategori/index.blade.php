@extends('layouts.admin')

@section('title', 'Kategori')
@section('page_title', 'Kategori')
@section('breadcrumb', 'Data / Kategori')

@section('content')
    <div class="section-header">
        <div>
            <div class="section-title">Daftar Kategori</div>
            <div class="section-sub">{{ $kategoris->total() }} kategori terdaftar</div>
        </div>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Tambah Kategori
        </a>
    </div>

    <div class="table-wrap">
        @if($kategoris->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84z"/></svg>
                <p>Belum ada kategori. <a href="{{ route('admin.kategori.create') }}" style="color:var(--accent);">Tambah sekarang</a>.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>Nama Kategori</th>
                        <th>Pengaduan</th>
                        <th>Dibuat</th>
                        <th style="width:120px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategoris as $i => $k)
                        <tr>
                            <td style="color:var(--muted); font-size:0.8rem;">{{ $kategoris->firstItem() + $i }}</td>
                            <td><strong>{{ $k->nama_kategori }}</strong></td>
                            <td>
                                <span class="badge {{ $k->pengaduans_count > 0 ? 'badge-blue' : 'badge-gray' }}">
                                    {{ $k->pengaduans_count }}
                                </span>
                            </td>
                            <td style="color:var(--muted); font-size:0.82rem;">{{ $k->created_at->format('d M Y') }}</td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:0.35rem; justify-content:flex-end;">
                                    <a href="{{ route('admin.kategori.edit', $k) }}" class="btn btn-ghost btn-sm">
                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                        Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete"
                                        data-url="{{ route('admin.kategori.destroy', $k) }}"
                                        data-name="{{ $k->nama_kategori }}">
                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($kategoris->hasPages())
        <div class="pagination">
            {{-- Previous --}}
            @if($kategoris->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $kategoris->previousPageUrl() }}">‹</a>
            @endif

            {{-- Pages --}}
            @foreach($kategoris->getUrlRange(1, $kategoris->lastPage()) as $page => $url)
                @if($page == $kategoris->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($kategoris->hasMorePages())
                <a href="{{ $kategoris->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    @endif
@endsection
