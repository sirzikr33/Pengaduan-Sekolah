@extends('layouts.siswa')

@section('title', 'Riwayat Chat')
@section('page_title', 'Riwayat Chat')
@section('breadcrumb', 'Siswa / Chat / Riwayat')

@push('styles')
<style>
    /* ── Page Header ── */
    .history-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    /* ── History Card List ── */
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .history-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: var(--text);
        transition: all 0.2s;
        box-shadow: 0 1px 6px rgba(34,139,34,0.05);
        position: relative;
        overflow: hidden;
    }
    .history-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--accent), #16a34a);
        border-radius: 2px 0 0 2px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .history-card:hover {
        border-color: rgba(34,166,69,0.3);
        box-shadow: 0 4px 16px rgba(34,139,34,0.1);
        transform: translateY(-1px);
    }
    .history-card:hover::before { opacity: 1; }

    /* ── Card Icon ── */
    .card-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(34,166,69,0.12), rgba(22,163,74,0.08));
        border: 1px solid rgba(34,166,69,0.18);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.3rem;
    }

    /* ── Card Content ── */
    .card-body { flex: 1; min-width: 0; }
    .card-title-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
        flex-wrap: wrap;
    }
    .card-title-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .card-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .card-meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: var(--muted);
    }
    .card-meta-item svg { width: 13px; height: 13px; flex-shrink: 0; }

    /* ── Badge Tipe Chat ── */
    .badge-chatbot  { background: rgba(34,166,69,0.1);  color: #166534; }
    .badge-admin    { background: rgba(59,130,246,0.1); color: #1e40af; }
    .badge-pengaduan { background: rgba(217,119,6,0.1); color: #92400e; }

    /* ── Arrow ── */
    .card-arrow {
        color: var(--muted);
        flex-shrink: 0;
        transition: transform 0.2s, color 0.2s;
    }
    .history-card:hover .card-arrow { color: var(--accent); transform: translateX(3px); }

    /* ── Empty ── */
    .empty-history {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
    }
    .empty-history-icon {
        width: 72px; height: 72px;
        background: rgba(34,166,69,0.06);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
    }
    .empty-history-icon svg { width: 34px; height: 34px; color: var(--accent); opacity: 0.5; }
    .empty-history h3 { font-size: 1rem; font-weight: 600; margin-bottom: 0.4rem; }
    .empty-history p { font-size: 0.84rem; color: var(--muted); max-width: 320px; margin: 0 auto 1.5rem; }

    /* ── Stats Bar ── */
    .stats-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .stat-pill {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 0.35rem 0.85rem;
        font-size: 0.78rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .stat-pill strong { color: var(--text); font-weight: 600; }
    .stat-pill svg { width: 13px; height: 13px; }
</style>
@endpush

@section('content')
<div class="history-header">
    <div>
        <div class="section-title">Riwayat Chat</div>
        <div class="section-sub">Semua percakapan yang telah selesai</div>
    </div>
    <div style="display:flex; gap:0.5rem;">
        <a href="{{ route('siswa.chat.index') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            Chat Baru
        </a>
    </div>
</div>

{{-- Stats Bar --}}
@if(!$histories->isEmpty())
<div class="stats-bar">
    <div class="stat-pill">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        <strong>{{ $histories->total() }}</strong> total percakapan
    </div>
    <div class="stat-pill">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
        <strong>{{ $histories->where('pengaduan_id', '!=', null)->count() }}</strong> menghasilkan pengaduan
    </div>
    <div class="stat-pill">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        <strong>{{ $histories->where('admin_id', '!=', null)->count() }}</strong> ditangani admin
    </div>
</div>
@endif

{{-- History List --}}
@if($histories->isEmpty())
    <div class="empty-history">
        <div class="empty-history-icon">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
        </div>
        <h3>Belum Ada Riwayat Chat</h3>
        <p>Percakapan yang sudah selesai akan muncul di sini. Mulai percakapan baru untuk menyampaikan pengaduanmu.</p>
        <a href="{{ route('siswa.chat.index') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            Mulai Chat
        </a>
    </div>
@else
    <div class="history-list">
        @foreach($histories as $session)
            @php
                $hasPengaduan = $session->pengaduan_id !== null;
                $hadAdmin     = $session->admin_id !== null;
                $resolvedAt   = $session->resolved_at ?? $session->updated_at;
                $msgCount     = $session->messages_count;
            @endphp
            <a href="{{ route('siswa.chat.history.show', $session) }}" class="history-card">
                {{-- Icon --}}
                <div class="card-icon">
                    {{ $hasPengaduan ? '📋' : ($hadAdmin ? '👨‍💼' : '🤖') }}
                </div>

                {{-- Body --}}
                <div class="card-body">
                    <div class="card-title-row">
                        <span class="card-title-text">
                            @if($hasPengaduan && $session->pengaduan)
                                {{ $session->pengaduan->nama_pengaduan }}
                            @else
                                Percakapan {{ $resolvedAt->translatedFormat('d M Y') }}
                            @endif
                        </span>
                        @if($hasPengaduan)
                            <span class="badge badge-pengaduan">Pengaduan dikirim</span>
                        @elseif($hadAdmin)
                            <span class="badge badge-admin">Dengan admin</span>
                        @else
                            <span class="badge badge-chatbot">Chatbot</span>
                        @endif
                    </div>
                    <div class="card-meta">
                        {{-- Tanggal --}}
                        <div class="card-meta-item">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L11 14.67V7h1.5v7.12l4.74 2.82-1.01 1.06z"/></svg>
                            {{ $resolvedAt->translatedFormat('d F Y, H:i') }}
                        </div>
                        {{-- Jumlah pesan --}}
                        <div class="card-meta-item">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            {{ $msgCount }} pesan
                        </div>
                        {{-- Kategori pengaduan jika ada --}}
                        @if($hasPengaduan && $session->pengaduan?->kategori)
                            <div class="card-meta-item">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L14.98 10H9.02L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5S15.01 22 17.5 22s4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5S16.12 15 17.5 15s2.5 1.12 2.5 2.5S18.88 20 17.5 20zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>
                                {{ $session->pengaduan->kategori->nama_kategori }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Arrow --}}
                <div class="card-arrow">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($histories->hasPages())
        <div class="pagination" style="margin-top:1.25rem;">
            @if($histories->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $histories->previousPageUrl() }}">‹</a>
            @endif

            @foreach($histories->getUrlRange(1, $histories->lastPage()) as $page => $url)
                @if($page == $histories->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($histories->hasMorePages())
                <a href="{{ $histories->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    @endif
@endif
@endsection
