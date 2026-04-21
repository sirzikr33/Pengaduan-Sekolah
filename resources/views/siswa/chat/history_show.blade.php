@extends('layouts.siswa')

@section('title', 'Detail Riwayat Chat')
@section('page_title', 'Detail Riwayat Chat')
@section('breadcrumb', 'Siswa / Chat / Riwayat / Detail')

@push('styles')
<style>
    /* ── Layout Grid ── */
    .history-show-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .history-show-grid { grid-template-columns: 1fr; }
    }

    /* ── Chat Panel ── */
    .chat-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(34,139,34,0.08);
    }

    /* ── Chat Header ── */
    .chat-panel-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #1a2e1a, #2d4a2d);
        color: #fff;
    }
    .panel-avatar {
        width: 38px; height: 38px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .panel-title { font-size: 0.9rem; font-weight: 600; }
    .panel-sub { font-size: 0.72rem; opacity: 0.75; margin-top: 1px; }
    .panel-badge {
        margin-left: auto;
        padding: 0.25rem 0.7rem;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    /* ── Messages Container ── */
    .chat-log {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        background: var(--surface2);
        max-height: 65vh;
        overflow-y: auto;
    }
    .chat-log::-webkit-scrollbar { width: 5px; }
    .chat-log::-webkit-scrollbar-thumb { background: rgba(34,139,34,0.15); border-radius: 10px; }

    /* ── Message Bubble ── */
    .msg {
        display: flex;
        gap: 0.5rem;
        max-width: 80%;
    }
    .msg.bot, .msg.admin { align-self: flex-start; }
    .msg.user { align-self: flex-end; flex-direction: row-reverse; }

    .msg-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        flex-shrink: 0;
        font-weight: 600;
    }
    .msg.bot  .msg-avatar { background: linear-gradient(135deg, var(--accent), #16a34a); color: #fff; }
    .msg.admin .msg-avatar { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; }
    .msg.user  .msg-avatar { background: var(--border); color: var(--text); }

    .msg-bubble {
        padding: 0.65rem 1rem;
        border-radius: 14px;
        font-size: 0.875rem;
        line-height: 1.55;
        word-wrap: break-word;
    }
    .msg.bot  .msg-bubble,
    .msg.admin .msg-bubble {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top-left-radius: 4px;
        color: var(--text);
    }
    .msg.admin .msg-bubble { border-color: rgba(59,130,246,0.2); background: rgba(59,130,246,0.04); }
    .msg.user .msg-bubble {
        background: linear-gradient(135deg, var(--accent), #16a34a);
        color: #fff;
        border-top-right-radius: 4px;
    }

    .msg-time {
        font-size: 0.65rem;
        color: var(--muted);
        margin-top: 0.3rem;
    }
    .msg.user .msg-time { text-align: right; }

    .msg-bubble img {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 0.4rem;
        display: block;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .msg-bubble img:hover { transform: scale(1.04); }

    /* Options in read-only mode */
    .msg-option-chip {
        display: inline-block;
        padding: 0.3rem 0.7rem;
        border: 1px solid rgba(34,166,69,0.25);
        background: rgba(34,166,69,0.06);
        color: var(--accent);
        border-radius: 16px;
        font-size: 0.75rem;
        margin: 0.25rem 0.25rem 0 0;
        cursor: default;
    }
    .msg-option-chip.selected {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }

    /* ── Read-Only Badge ── */
    .readonly-bar {
        padding: 0.6rem 1.25rem;
        background: rgba(217,119,6,0.06);
        border-top: 1px solid rgba(217,119,6,0.15);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        color: #92400e;
    }
    .readonly-bar svg { width: 14px; height: 14px; }

    /* ── Info Sidebar ── */
    .info-sidebar { display: flex; flex-direction: column; gap: 1rem; }

    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 8px rgba(34,139,34,0.05);
    }
    .info-card-header {
        padding: 0.7rem 1rem;
        background: var(--surface2);
        border-bottom: 1px solid var(--border);
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .info-card-body { padding: 0.85rem 1rem; }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.45rem 0;
        gap: 0.5rem;
    }
    .info-row + .info-row { border-top: 1px solid rgba(26,46,26,0.05); }
    .info-label { font-size: 0.75rem; color: var(--muted); flex-shrink: 0; }
    .info-value { font-size: 0.8rem; font-weight: 500; color: var(--text); text-align: right; }

    /* ── Pengaduan Link ── */
    .pengaduan-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        background: rgba(34,166,69,0.05);
        border-radius: 10px;
        text-decoration: none;
        color: var(--text);
        border: 1px solid rgba(34,166,69,0.15);
        transition: all 0.18s;
        margin-top: 0.5rem;
    }
    .pengaduan-link:hover { background: rgba(34,166,69,0.1); border-color: rgba(34,166,69,0.3); }
    .pengaduan-link-icon {
        width: 36px; height: 36px;
        background: rgba(34,166,69,0.12);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .pengaduan-link-icon svg { width: 18px; height: 18px; color: var(--accent); }
    .pengaduan-link-name { font-size: 0.82rem; font-weight: 600; }
    .pengaduan-link-sub  { font-size: 0.72rem; color: var(--muted); }

    /* ── Photo Modal ── */
    .photo-modal {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.85);
        display: none; align-items: center; justify-content: center;
        z-index: 300;
        cursor: pointer;
    }
    .photo-modal.show { display: flex; }
    .photo-modal img { max-width: 90vw; max-height: 90vh; border-radius: 12px; }
</style>
@endpush

@section('content')

{{-- Back Link --}}
<div style="margin-bottom: 1rem;">
    <a href="{{ route('siswa.chat.history') }}" class="btn btn-ghost">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        Kembali ke Riwayat
    </a>
</div>

<div class="history-show-grid">

    {{-- ── Chat Panel ── --}}
    <div class="chat-panel">
        {{-- Header --}}
        <div class="chat-panel-header">
            <div class="panel-avatar">
                @if($chatSession->admin_id) 👨‍💼 @else 🤖 @endif
            </div>
            <div>
                <div class="panel-title">
                    @if($chatSession->admin_id && $chatSession->admin)
                        Chat dengan {{ $chatSession->admin->name }}
                    @else
                        Percakapan dengan Bot
                    @endif
                </div>
                <div class="panel-sub">
                    {{ ($chatSession->resolved_at ?? $chatSession->updated_at)->translatedFormat('d F Y, H:i') }}
                </div>
            </div>
            <span class="panel-badge">📖 Arsip</span>
        </div>

        {{-- Messages --}}
        <div class="chat-log" id="chatLog">
            @forelse($chatSession->messages as $msg)
                @php
                    $type     = $msg->sender_type; // 'user', 'bot', 'admin'
                    $metadata = $msg->metadata;
                    $isOptions = $metadata && isset($metadata['type']) && $metadata['type'] === 'options';
                @endphp
                <div class="msg {{ $type }}" id="msg-{{ $msg->id }}">
                    {{-- Avatar --}}
                    <div class="msg-avatar">
                        @if($type === 'bot')   🤖
                        @elseif($type === 'admin') 👨‍💼
                        @else {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>

                    <div>
                        {{-- Bubble --}}
                        <div class="msg-bubble">
                            @if($msg->message && $msg->message !== '__SELECT_KATEGORI__')
                                {!! nl2br(e(strip_tags($msg->message))) !!}
                            @elseif($msg->message === '__SELECT_KATEGORI__')
                                <em style="color:var(--muted); font-size:0.8rem;">-- Pilih kategori --</em>
                            @endif

                            {{-- Gambar --}}
                            @if($msg->attachment)
                                <img src="{{ asset('storage/' . $msg->attachment) }}"
                                     alt="Foto" onclick="showPhoto(this.src)">
                            @endif

                            {{-- Options (read-only chips) --}}
                            @if($isOptions && isset($metadata['options']))
                                <div style="margin-top:0.4rem;">
                                    @foreach($metadata['options'] as $opt)
                                        <span class="msg-option-chip">{{ $opt['label'] }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Time --}}
                        <div class="msg-time">
                            {{ $msg->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:2rem; color:var(--muted); font-size:0.84rem;">
                    Tidak ada pesan dalam sesi ini.
                </div>
            @endforelse
        </div>

        {{-- Read-Only Bar --}}
        <div class="readonly-bar">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z"/></svg>
            Chat ini sudah selesai. Kamu hanya bisa membaca percakapan ini.
        </div>
    </div>

    {{-- ── Info Sidebar ── --}}
    <div class="info-sidebar">

        {{-- Info Sesi --}}
        <div class="info-card">
            <div class="info-card-header">Info Sesi</div>
            <div class="info-card-body">
                <div class="info-row">
                    <span class="info-label">Tanggal mulai</span>
                    <span class="info-value">{{ $chatSession->created_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Selesai pada</span>
                    <span class="info-value">
                        {{ ($chatSession->resolved_at ?? $chatSession->updated_at)->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total pesan</span>
                    <span class="info-value">{{ $chatSession->messages->count() }} pesan</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Penanganan</span>
                    <span class="info-value">
                        @if($chatSession->admin_id)
                            <span class="badge badge-admin">Admin</span>
                        @else
                            <span class="badge badge-chatbot">Chatbot</span>
                        @endif
                    </span>
                </div>
                @if($chatSession->admin_id && $chatSession->admin)
                <div class="info-row">
                    <span class="info-label">Admin</span>
                    <span class="info-value">{{ $chatSession->admin->name }}</span>
                </div>
                @endif
                @if($chatSession->accepted_at)
                <div class="info-row">
                    <span class="info-label">Diterima admin</span>
                    <span class="info-value">{{ $chatSession->accepted_at->translatedFormat('H:i, d M Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Pengaduan terkait --}}
        @if($chatSession->pengaduan_id && $chatSession->pengaduan)
            @php $p = $chatSession->pengaduan; @endphp
            <div class="info-card">
                <div class="info-card-header">Pengaduan Terkait</div>
                <div class="info-card-body">
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $p->nama_pengaduan }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kategori</span>
                        <span class="info-value">
                            <span class="badge badge-gray">{{ $p->kategori->nama_kategori ?? '-' }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kondisi</span>
                        <span class="info-value">
                            <span class="badge badge-{{ $p->kondisi_pengaduan }}">{{ ucfirst($p->kondisi_pengaduan) }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal</span>
                        <span class="info-value">{{ $p->tanggal_pengaduan->translatedFormat('d M Y') }}</span>
                    </div>

                    <a href="{{ route('siswa.pengaduan.show', $p) }}" class="pengaduan-link">
                        <div class="pengaduan-link-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 14H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        </div>
                        <div>
                            <div class="pengaduan-link-name">Lihat Detail Pengaduan</div>
                            <div class="pengaduan-link-sub">Cek status & catatan admin</div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-left:auto; color:var(--muted);"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                    </a>
                </div>
            </div>
        @else
            <div class="info-card">
                <div class="info-card-header">Pengaduan Terkait</div>
                <div class="info-card-body">
                    <div style="text-align:center; padding:1rem 0; color:var(--muted); font-size:0.82rem;">
                        <svg viewBox="0 0 24 24" fill="currentColor" style="width:32px;height:32px;margin-bottom:0.5rem;opacity:0.3;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                        <p>Tidak ada pengaduan yang terhubung dengan percakapan ini.</p>
                    </div>
                </div>
            </div>
        @endif

    </div>{{-- end info-sidebar --}}
</div>

{{-- Photo Modal --}}
<div class="photo-modal" id="photoModal" onclick="this.classList.remove('show')">
    <img id="photoModalImg" src="" alt="Preview">
</div>
@endsection

@push('scripts')
<script>
    // Scroll chat to bottom on load
    document.addEventListener('DOMContentLoaded', function () {
        const log = document.getElementById('chatLog');
        if (log) log.scrollTop = log.scrollHeight;
    });

    function showPhoto(src) {
        document.getElementById('photoModalImg').src = src;
        document.getElementById('photoModal').classList.add('show');
    }
</script>
@endpush
