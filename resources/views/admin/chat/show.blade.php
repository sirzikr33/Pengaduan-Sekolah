@extends('layouts.admin')

@section('title', 'Chat — ' . ($chatSession->user->siswa->nama ?? $chatSession->user->name))
@section('page_title', 'Live Chat')
@section('breadcrumb', 'Admin / Live Chat / Percakapan')

@push('styles')
<style>
    .chat-layout {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 1rem;
        height: calc(100vh - 130px);
    }

    /* ── Chat Panel ── */
    .chat-panel {
        display: flex;
        flex-direction: column;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(34,139,34,0.06);
    }

    .chat-panel-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #fff;
        flex-shrink: 0;
    }
    .chat-panel-avatar {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.9rem;
        flex-shrink: 0;
    }
    .chat-panel-info h3 { font-size: 0.9rem; font-weight: 600; margin: 0; }
    .chat-panel-info p { font-size: 0.72rem; opacity: 0.85; margin: 0; }
    .chat-panel-actions { margin-left: auto; display: flex; gap: 0.4rem; }
    .chat-panel-actions a, .chat-panel-actions button {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        padding: 0.35rem 0.7rem;
        border-radius: 8px;
        font-size: 0.75rem;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        transition: all 0.15s;
    }
    .chat-panel-actions a:hover, .chat-panel-actions button:hover { background: rgba(255,255,255,0.25); }
    .chat-panel-actions .btn-resolve { background: rgba(220,38,38,0.3); border-color: rgba(220,38,38,0.4); }

    /* Messages */
    .admin-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        background: #f8faf8;
    }
    .admin-chat-messages::-webkit-scrollbar { width: 5px; }
    .admin-chat-messages::-webkit-scrollbar-thumb { background: rgba(59,130,246,0.15); border-radius: 10px; }

    .msg {
        display: flex;
        gap: 0.4rem;
        max-width: 75%;
        animation: msgIn 0.25s ease;
    }
    .msg.user { align-self: flex-start; }
    .msg.bot { align-self: flex-start; }
    .msg.admin { align-self: flex-end; flex-direction: row-reverse; }

    .msg-avatar {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    .msg.user .msg-avatar { background: linear-gradient(135deg, var(--accent), #16a34a); color: #fff; }
    .msg.bot .msg-avatar { background: rgba(26,46,26,0.1); color: var(--muted); }
    .msg.admin .msg-avatar { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; }

    .msg-bubble {
        padding: 0.6rem 0.9rem;
        border-radius: 12px;
        font-size: 0.84rem;
        line-height: 1.5;
        word-wrap: break-word;
    }
    .msg.user .msg-bubble {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top-left-radius: 3px;
        color: var(--text);
    }
    .msg.bot .msg-bubble {
        background: rgba(26,46,26,0.04);
        border: 1px solid rgba(26,46,26,0.08);
        border-top-left-radius: 3px;
        color: var(--muted);
        font-style: italic;
    }
    .msg.admin .msg-bubble {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border-top-right-radius: 3px;
    }

    .msg-time {
        font-size: 0.62rem;
        color: var(--muted);
        margin-top: 0.2rem;
    }
    .msg.admin .msg-time { text-align: right; }

    .msg-bubble img.chat-photo {
        max-width: 180px;
        border-radius: 6px;
        margin-top: 0.3rem;
        cursor: pointer;
    }

    /* Input */
    .admin-chat-input {
        padding: 0.75rem 1rem;
        background: var(--surface);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    .admin-chat-input input {
        flex: 1;
        padding: 0.6rem 1rem;
        border: 1px solid var(--border);
        border-radius: 24px;
        font-size: 0.84rem;
        font-family: 'Inter', sans-serif;
        background: var(--surface2);
        outline: none;
        color: var(--text);
    }
    .admin-chat-input input:focus { border-color: #3b82f6; }
    .admin-chat-input button {
        width: 36px; height: 36px;
        border-radius: 50%;
        border: none;
        background: #3b82f6;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .admin-chat-input button:hover { background: #2563eb; }
    .admin-chat-input button svg { width: 16px; height: 16px; }

    /* ── Side Panel ── */
    .chat-side-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(34,139,34,0.06);
    }
    .side-section {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
    }
    .side-section:last-child { border-bottom: none; }
    .side-section-title {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 0.6rem;
    }
    .side-info-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.82rem;
        padding: 0.25rem 0;
    }
    .side-info-row .label { color: var(--muted); }
    .side-info-row .value { font-weight: 500; color: var(--text); }

    .side-user-card {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .side-user-avatar {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, var(--accent), #16a34a);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 1rem;
        flex-shrink: 0;
    }
    .side-user-name { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .side-user-detail { font-size: 0.75rem; color: var(--muted); }

    @keyframes msgIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .disabled-input {
        opacity: 0.5;
        pointer-events: none;
    }

    /* Photo modal */
    .photo-modal {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.8);
        display: none; align-items: center; justify-content: center;
        z-index: 300;
        cursor: pointer;
    }
    .photo-modal.show { display: flex; }
    .photo-modal img { max-width: 90vw; max-height: 90vh; border-radius: 12px; }
</style>
@endpush

@section('content')
<div class="chat-layout">
    <!-- Chat Panel -->
    <div class="chat-panel">
        <div class="chat-panel-header">
            <div class="chat-panel-avatar">{{ strtoupper(substr($chatSession->user->siswa->nama ?? $chatSession->user->name, 0, 1)) }}</div>
            <div class="chat-panel-info">
                <h3>{{ $chatSession->user->siswa->nama ?? $chatSession->user->name }}</h3>
                <p>{{ $chatSession->user->siswa->kelas ?? 'Siswa' }} · {{ ucfirst($chatSession->status) }}</p>
            </div>
            <div class="chat-panel-actions">
                <a href="{{ route('admin.chat.index') }}">← Kembali</a>
                @if($chatSession->isActive())
                <form action="{{ route('admin.chat.resolve', $chatSession) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-resolve" onclick="return confirm('Selesaikan chat ini?')">Selesai</button>
                </form>
                @endif
            </div>
        </div>

        <div class="admin-chat-messages" id="chatMessages">
            @foreach($messages as $msg)
            <div class="msg {{ $msg->sender_type }}" id="msg-{{ $msg->id }}">
                <div class="msg-avatar">
                    @if($msg->sender_type === 'user')
                        {{ strtoupper(substr($chatSession->user->name, 0, 1)) }}
                    @elseif($msg->sender_type === 'bot')
                        🤖
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="msg-bubble">
                        @if($msg->message && $msg->message !== '__SELECT_KATEGORI__')
                            {!! nl2br(e($msg->message)) !!}
                        @endif
                        @if($msg->attachment)
                            <br><img class="chat-photo" src="{{ asset('storage/' . $msg->attachment) }}" onclick="showPhoto('{{ asset('storage/' . $msg->attachment) }}')" alt="Foto">
                        @endif
                    </div>
                    <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Input area (only for active sessions) -->
        <div class="admin-chat-input {{ $chatSession->isActive() ? '' : 'disabled-input' }}">
            <input type="text" id="adminInput" placeholder="{{ $chatSession->isActive() ? 'Ketik balasan...' : 'Chat sudah selesai' }}" onkeydown="if(event.key==='Enter')sendAdminMessage()" {{ $chatSession->isActive() ? '' : 'disabled' }}>
            <button onclick="sendAdminMessage()" {{ $chatSession->isActive() ? '' : 'disabled' }}>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </div>
    </div>

    <!-- Side Panel -->
    <div class="chat-side-panel">
        <div class="side-section">
            <div class="side-section-title">Informasi Siswa</div>
            <div class="side-user-card">
                <div class="side-user-avatar">{{ strtoupper(substr($chatSession->user->siswa->nama ?? $chatSession->user->name, 0, 1)) }}</div>
                <div>
                    <div class="side-user-name">{{ $chatSession->user->siswa->nama ?? $chatSession->user->name }}</div>
                    <div class="side-user-detail">{{ $chatSession->user->siswa->kelas ?? '-' }} · {{ $chatSession->user->email }}</div>
                </div>
            </div>
        </div>

        <div class="side-section">
            <div class="side-section-title">Status Chat</div>
            <div class="side-info-row">
                <span class="label">Status</span>
                <span class="value">{{ ucfirst($chatSession->status) }}</span>
            </div>
            <div class="side-info-row">
                <span class="label">Dimulai</span>
                <span class="value">{{ $chatSession->created_at->format('d/m H:i') }}</span>
            </div>
            @if($chatSession->accepted_at)
            <div class="side-info-row">
                <span class="label">Diterima</span>
                <span class="value">{{ $chatSession->accepted_at->format('d/m H:i') }}</span>
            </div>
            @endif
            @if($chatSession->resolved_at)
            <div class="side-info-row">
                <span class="label">Selesai</span>
                <span class="value">{{ $chatSession->resolved_at->format('d/m H:i') }}</span>
            </div>
            @endif
        </div>

        @if($chatSession->pengaduan)
        <div class="side-section">
            <div class="side-section-title">Pengaduan Terkait</div>
            <div class="side-info-row">
                <span class="label">Judul</span>
                <span class="value">{{ Str::limit($chatSession->pengaduan->nama_pengaduan, 25) }}</span>
            </div>
            <div class="side-info-row">
                <span class="label">Kategori</span>
                <span class="value">{{ $chatSession->pengaduan->kategori->nama_kategori ?? '-' }}</span>
            </div>
            <div class="side-info-row">
                <span class="label">Status</span>
                <span class="value">{{ ucfirst($chatSession->pengaduan->status) }}</span>
            </div>
            <div style="margin-top:0.5rem;">
                <a href="{{ route('admin.pengaduan.show', $chatSession->pengaduan) }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">Lihat Pengaduan</a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Photo modal -->
<div class="photo-modal" id="photoModal" onclick="this.classList.remove('show')">
    <img id="photoModalImg" src="" alt="Preview">
</div>
@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    const sessionId = '{{ $chatSession->id }}';
    const isActive = {{ $chatSession->isActive() ? 'true' : 'false' }};
    let lastMessageTime = null;

    // Scroll to bottom on load
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chatMessages');
        container.scrollTop = container.scrollHeight;

        // Set last message time
        const msgs = container.querySelectorAll('.msg');
        if (msgs.length > 0) {
            lastMessageTime = '{{ $messages->last()?->created_at?->toISOString() }}';
        }

        if (isActive) startPolling();
    });

    async function sendAdminMessage() {
        if (!isActive) return;
        const input = document.getElementById('adminInput');
        const msg = input.value.trim();
        if (!msg) return;

        input.value = '';

        try {
            const res = await fetch('{{ route("admin.chat.send", $chatSession) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ message: msg }),
            });
            const data = await res.json();
            if (data.success && data.message) {
                appendMessage(data.message);
            }
        } catch (e) { console.error(e); }
    }

    function startPolling() {
        setInterval(async () => {
            try {
                let url = '{{ route("admin.chat.messages", $chatSession) }}';
                if (lastMessageTime) url += `?after=${encodeURIComponent(lastMessageTime)}`;
                const res = await fetch(url);
                const data = await res.json();
                if (data.success && data.messages.length > 0) {
                    data.messages.forEach(m => appendMessage(m));
                    lastMessageTime = data.messages[data.messages.length - 1].created_at;
                }
            } catch (e) {}
        }, 3000);
    }

    function appendMessage(msg) {
        if (document.getElementById('msg-' + msg.id)) return;

        const container = document.getElementById('chatMessages');
        const div = document.createElement('div');
        div.className = 'msg ' + msg.sender_type;
        div.id = 'msg-' + msg.id;

        const avatarLabel = msg.sender_type === 'user' ? '{{ strtoupper(substr($chatSession->user->name, 0, 1)) }}' :
                           msg.sender_type === 'bot' ? '🤖' :
                           '{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}';

        let content = '';
        if (msg.message && msg.message !== '__SELECT_KATEGORI__') {
            content += msg.message.replace(/\n/g, '<br>');
        }
        if (msg.attachment) {
            content += `<br><img class="chat-photo" src="/storage/${msg.attachment}" onclick="showPhoto('/storage/${msg.attachment}')" alt="Foto">`;
        }

        const time = new Date(msg.created_at);
        const timeStr = time.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        div.innerHTML = `
            <div class="msg-avatar">${avatarLabel}</div>
            <div>
                <div class="msg-bubble">${content}</div>
                <div class="msg-time">${timeStr}</div>
            </div>
        `;

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function showPhoto(src) {
        document.getElementById('photoModalImg').src = src;
        document.getElementById('photoModal').classList.add('show');
    }
</script>
@endpush
