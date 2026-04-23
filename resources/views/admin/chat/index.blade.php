@extends('layouts.admin')

@section('title', 'Live Chat')
@section('page_title', 'Live Chat')
@section('breadcrumb', 'Admin / Live Chat')

@push('styles')
<style>
    .chat-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
        padding-bottom: 0;
    }
    .chat-tab {
        padding: 0.6rem 1.25rem;
        font-size: 0.84rem;
        font-weight: 500;
        color: var(--muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        position: relative;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
        font-family: 'Inter', sans-serif;
    }
    .chat-tab:hover { color: var(--accent); }
    .chat-tab.active { color: var(--accent); border-bottom-color: var(--accent); font-weight: 600; }
    .chat-tab .badge-count {
        position: absolute;
        top: 2px; right: 2px;
        background: var(--danger);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px; height: 18px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        line-height: 1;
    }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .chat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.15s;
        margin-bottom: 0.6rem;
        box-shadow: 0 1px 4px rgba(34,139,34,0.04);
    }
    .chat-card:hover { box-shadow: 0 4px 16px rgba(34,139,34,0.08); transform: translateY(-1px); }

    .chat-card-avatar {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, var(--accent), #16a34a);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 1rem;
        flex-shrink: 0;
    }
    .chat-card-info { flex: 1; min-width: 0; }
    .chat-card-name { font-size: 0.9rem; font-weight: 600; color: var(--text); margin-bottom: 0.15rem; }
    .chat-card-meta { font-size: 0.75rem; color: var(--muted); }
    .chat-card-actions { display: flex; gap: 0.4rem; flex-shrink: 0; }
    .chat-card-time { font-size: 0.72rem; color: var(--muted); text-align: right; flex-shrink: 0; min-width: 60px; }

    .badge-queue {
        display: inline-flex; align-items: center;
        padding: 0.15rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(217,119,6,0.1);
        color: #92400e;
    }
    .badge-active-chat {
        background: rgba(34,166,69,0.1);
        color: #166534;
    }
    .badge-resolved-chat {
        background: rgba(26,46,26,0.07);
        color: var(--muted);
    }

    .empty-chat-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--muted);
    }
    .empty-chat-state svg { width: 48px; height: 48px; margin-bottom: 0.75rem; opacity: 0.3; }
    .empty-chat-state p { font-size: 0.875rem; }
</style>
@endpush

@section('content')
    <!-- Tabs -->
    <div class="chat-tabs">
        <button class="chat-tab active" onclick="switchTab('queued', this)">
            Antrean
            @if($queueCount > 0)
                <span class="badge-count">{{ $queueCount }}</span>
            @endif
        </button>
        <button class="chat-tab" onclick="switchTab('active', this)">Chat Aktif</button>
        <button class="chat-tab" onclick="switchTab('recent', this)">Riwayat</button>
    </div>

    <!-- Queued Chats -->
    <div class="tab-content active" id="tab-queued">
        @include('admin.chat._queued_list')
    </div>

    <!-- Active Chats -->
    <div class="tab-content" id="tab-active">
        @forelse($activeChats as $chat)
            <div class="chat-card">
                <div class="chat-card-avatar">{{ strtoupper(substr($chat->user->siswa->nama ?? $chat->user->name, 0, 1)) }}</div>
                <div class="chat-card-info">
                    <div class="chat-card-name">{{ $chat->user->siswa->nama ?? $chat->user->name }}</div>
                    <div class="chat-card-meta">
                        <span class="badge-queue badge-active-chat">Aktif</span>
                        · {{ $chat->user->siswa->kelas ?? '-' }}
                    </div>
                </div>
                <div class="chat-card-time">
                    {{ $chat->accepted_at?->diffForHumans() }}
                </div>
                <div class="chat-card-actions">
                    <a href="{{ route('admin.chat.show', $chat) }}" class="btn btn-primary btn-sm">Buka Chat</a>
                </div>
            </div>
        @empty
            <div class="empty-chat-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                <p>Tidak ada chat aktif saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Recent / Resolved -->
    <div class="tab-content" id="tab-recent">
        @forelse($recentChats as $chat)
            <div class="chat-card">
                <div class="chat-card-avatar">{{ strtoupper(substr($chat->user->siswa->nama ?? $chat->user->name, 0, 1)) }}</div>
                <div class="chat-card-info">
                    <div class="chat-card-name">{{ $chat->user->siswa->nama ?? $chat->user->name }}</div>
                    <div class="chat-card-meta">
                        <span class="badge-queue badge-resolved-chat">Selesai</span>
                        · {{ $chat->user->siswa->kelas ?? '-' }}
                    </div>
                </div>
                <div class="chat-card-time">
                    {{ $chat->resolved_at?->diffForHumans() }}
                </div>
                <div class="chat-card-actions">
                    <a href="{{ route('admin.chat.show', $chat) }}" class="btn btn-ghost btn-sm">Lihat</a>
                </div>
            </div>
        @empty
            <div class="empty-chat-state">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                <p>Belum ada riwayat chat.</p>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
    function switchTab(tab, btn) {
        document.querySelectorAll('.chat-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
    }

    // Poll queue count every 10s
    setInterval(async () => {
        try {
            const res = await fetch('{{ route("admin.chat.queue-count") }}');
            const data = await res.json();
            const badge = document.querySelector('.chat-tab .badge-count');
            
            // Update badge
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    const tab = document.querySelector('.chat-tab');
                    const span = document.createElement('span');
                    span.className = 'badge-count';
                    span.textContent = data.count;
                    tab.appendChild(span);
                }
            } else if (badge) {
                badge.remove();
            }

            // Update queue list HTML dynamically
            if (data.html) {
                document.getElementById('tab-queued').innerHTML = data.html;
            }
        } catch (e) {}
    }, 10000);
</script>
@endpush
