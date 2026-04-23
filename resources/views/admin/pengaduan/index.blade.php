@extends('layouts.admin')

@section('title', 'Pengaduan')
@section('page_title', 'Pengaduan')
@section('breadcrumb', 'Data / Pengaduan')

@push('styles')
<style>
    /* ── Filters ── */
    .filters-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        align-items: center;
        margin-bottom: 1.25rem;
    }
    .filter-input {
        padding: 0.5rem 0.75rem;
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-size: 0.82rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
    }
    .filter-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(34,166,69,0.1);
    }
    .filter-input::placeholder { color: rgba(26,46,26,0.25); }
    select.filter-input { cursor: pointer; min-width: 120px; }
    select.filter-input option { background: var(--surface); color: var(--text); }

    /* ── Status badges ── */
    .badge-pending  { background: rgba(217,119,6,0.1);   color: #92400e; }
    .badge-proses   { background: rgba(59,130,246,0.1);  color: #1e40af; }
    .badge-selesai  { background: rgba(34,166,69,0.1);   color: #166534; }

    /* ── Kondisi badges ── */
    .badge-berat    { background: rgba(220,38,38,0.08);  color: #991b1b; }
    .badge-sedang   { background: rgba(217,119,6,0.1);   color: #92400e; }
    .badge-ringan   { background: rgba(59,130,246,0.1);  color: #1e40af; }

    /* ── Thumbnail ── */
    .thumb {
        width: 40px; height: 40px;
        border-radius: 8px;
        object-fit: cover;
        background: var(--surface2);
        border: 1px solid var(--border);
    }

    /* ── Stats row ── */
    .pengaduan-stats {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .pengaduan-stat {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 1px 6px rgba(34,139,34,0.05);
    }
    .pengaduan-stat .dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pengaduan-stat .dot.pending  { background: #d97706; }
    .pengaduan-stat .dot.proses   { background: #3b82f6; }
    .pengaduan-stat .dot.selesai  { background: var(--accent); }
    .pengaduan-stat .dot.total    { background: var(--muted); }
    .pengaduan-stat .stat-info span { font-size: 0.68rem; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
    .pengaduan-stat .stat-info strong { display: block; font-size: 1.15rem; color: var(--text); margin-top: 1px; }

    /* ── Responsive table ── */
    .table-responsive { overflow-x: auto; }
</style>
@endpush

@section('content')
    {{-- Stats --}}
    <div class="pengaduan-stats">
        <div class="pengaduan-stat">
            <div class="dot total"></div>
                <div class="stat-info"><span>Total</span><strong>{{ $totalPengaduan }}</strong></div>
        </div>
        <div class="pengaduan-stat">
            <div class="dot pending"></div>
                <div class="stat-info"><span>Pending</span><strong>{{ $pendingPengaduan }}</strong></div>
        </div>
        <div class="pengaduan-stat">
            <div class="dot proses"></div>
                <div class="stat-info"><span>Proses</span><strong>{{ $prosesPengaduan }}</strong></div>
        </div>
        <div class="pengaduan-stat">
            <div class="dot selesai"></div>
                <div class="stat-info"><span>Selesai</span><strong>{{ $selesaiPengaduan }}</strong></div>
        </div>
    </div>

    {{-- Filters --}}
    <form class="filters-bar" method="GET" action="{{ route('admin.pengaduan.index') }}">
        <input type="text" name="search" class="filter-input" placeholder="Cari pengaduan / siswa…"
               value="{{ request('search') }}" style="flex:1; min-width:180px;">
        <select name="status" class="filter-input" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
            <option value="proses"  {{ request('status')=='proses'  ? 'selected' : '' }}>Proses</option>
            <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
        <select name="kondisi" class="filter-input" onchange="this.form.submit()">
            <option value="">Semua Kondisi</option>
            <option value="berat"  {{ request('kondisi')=='berat'  ? 'selected' : '' }}>Berat</option>
            <option value="sedang" {{ request('kondisi')=='sedang' ? 'selected' : '' }}>Sedang</option>
            <option value="ringan" {{ request('kondisi')=='ringan' ? 'selected' : '' }}>Ringan</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            Cari
        </button>
        @if(request()->hasAny(['search','status','kondisi']))
            <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-ghost btn-sm">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="section-header">
        <div>
            <div class="section-title">Daftar Pengaduan</div>
            <div class="section-sub">{{ $totalPengaduan }} pengaduan ditemukan</div>
        </div>
        {{-- Fitur 2: Tombol Export Excel --}}
        <a href="{{ route('admin.pengaduan.export', request()->only(['status','kondisi','search'])) }}"
           class="btn btn-ghost btn-sm"
           title="Export data ke Excel sesuai filter aktif">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;color:#166534;">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15l1.41-1.41L11 15.17V11h2v4.17l1.59-1.59L16 15l-4 4-4-4z"/>
            </svg>
            Export Excel
        </a>
    </div>

    <div class="table-wrap">
        @if($pengaduans->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/></svg>
                <p>Tidak ada pengaduan ditemukan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width:44px;">#</th>
                            <th>Pengaduan</th>
                            <th>Lokasi</th>
                            <th>Siswa</th>
                            <th>Kategori</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th style="width:150px; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengaduans as $i => $p)
                            <tr>
                                <td style="color:var(--muted); font-size:0.78rem;">{{ $pengaduans->firstItem() + $i }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.6rem;">
                                        <div>
                                            <strong>{{ $p->nama_pengaduan }}</strong>
                                            <div style="font-size:0.75rem; color:var(--muted); margin-top:0.15rem; max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                {{ $p->deskripsi ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color:var(--muted); font-size:0.82rem; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $p->lokasi ?: '-' }}
                                </td>
                                <td>
                                    <div style="line-height:1.3;">
                                        @if($p->siswa)
                                            <div style="font-weight:500;">{{ $p->siswa->nama ?? '-' }}</div>
                                            <div style="font-size:0.72rem; color:var(--muted);">{{ $p->siswa->kelas ?? '' }}</div>
                                        @else
                                            <div style="font-weight:500; color:var(--muted);">-</div>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="badge badge-gray">{{ $p->kategori->nama_kategori ?? '-' }}</span></td>
                                <td>
                                    <span class="badge badge-{{ $p->kondisi_pengaduan }}">
                                        {{ ucfirst($p->kondisi_pengaduan) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $p->status }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td style="color:var(--muted); font-size:0.82rem;">
                                    {{ $p->tanggal_pengaduan->format('d M Y') }}
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex; gap:0.35rem; justify-content:flex-end;">
                                        <a href="{{ route('admin.pengaduan.show', $p) }}" class="btn btn-ghost btn-sm" title="Detail">
                                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.pengaduan.edit', $p) }}" class="btn btn-ghost btn-sm" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete"
                                            data-url="{{ route('admin.pengaduan.destroy', $p) }}"
                                            data-name="{{ $p->nama_pengaduan }}">
                                            <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
