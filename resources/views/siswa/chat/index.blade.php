@extends('layouts.siswa')

@section('title', 'Chat Pengaduan')
@section('page_title', 'Chat Pengaduan')
@section('breadcrumb', 'Siswa / Chat Pengaduan')

@push('styles')
<style>
    /* ── Chat Container ── */
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 130px);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(34,139,34,0.08);
    }

    /* ── Chat Header ── */
    .chat-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, var(--accent), #16a34a);
        color: #fff;
        flex-shrink: 0;
    }
    .chat-bot-avatar {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .chat-header-info h3 { font-size: 0.95rem; font-weight: 600; margin: 0; }
    .chat-header-info p { font-size: 0.75rem; opacity: 0.85; margin: 0; }
    .chat-header-actions { margin-left: auto; display: flex; gap: 0.5rem; }
    .chat-header-actions button {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: all 0.15s;
    }
    .chat-header-actions button:hover { background: rgba(255,255,255,0.25); }

    /* ── Chat Messages ── */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        background: var(--surface2);
        scroll-behavior: smooth;
    }
    .chat-messages::-webkit-scrollbar { width: 5px; }
    .chat-messages::-webkit-scrollbar-thumb { background: rgba(34,139,34,0.15); border-radius: 10px; }

    /* ── Message Bubble ── */
    .msg {
        display: flex;
        gap: 0.5rem;
        max-width: 80%;
        animation: msgIn 0.3s ease;
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
    .msg.bot .msg-avatar { background: linear-gradient(135deg, var(--accent), #16a34a); color: #fff; }
    .msg.admin .msg-avatar { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; }
    .msg.user .msg-avatar { background: var(--border); color: var(--text); }

    .msg-bubble {
        padding: 0.7rem 1rem;
        border-radius: 14px;
        font-size: 0.875rem;
        line-height: 1.55;
        word-wrap: break-word;
        position: relative;
    }
    .msg.bot .msg-bubble, .msg.admin .msg-bubble {
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
        margin-top: 0.25rem;
    }
    .msg.user .msg-time { text-align: right; }

    .msg-bubble img.chat-photo {
        max-width: 220px;
        border-radius: 8px;
        margin-top: 0.4rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .msg-bubble img.chat-photo:hover { transform: scale(1.05); }

    /* ── Options Buttons ── */
    .msg-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.6rem;
    }
    .msg-option-btn {
        padding: 0.45rem 0.85rem;
        border: 1px solid rgba(34,166,69,0.3);
        background: rgba(34,166,69,0.06);
        color: var(--accent);
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: all 0.15s;
    }
    .msg-option-btn:hover {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(34,166,69,0.3);
    }
    .msg-option-btn.selected {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
        pointer-events: none;
    }

    /* ── Typing indicator ── */
    .typing-indicator {
        display: none;
        align-self: flex-start;
        padding: 0.6rem 1rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        border-top-left-radius: 4px;
    }
    .typing-indicator.show { display: flex; gap: 0.3rem; align-items: center; }
    .typing-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--muted);
        animation: typingBounce 1.4s ease-in-out infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-4px); opacity: 1; }
    }

    /* ── Chat Input ── */
    .chat-input-area {
        padding: 0.85rem 1rem;
        background: var(--surface);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    .chat-input-area input[type="text"] {
        flex: 1;
        padding: 0.65rem 1rem;
        border: 1px solid var(--border);
        border-radius: 24px;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
        background: var(--surface2);
        outline: none;
        transition: border-color 0.18s;
        color: var(--text);
    }
    .chat-input-area input[type="text"]::placeholder { color: rgba(26,46,26,0.3); }
    .chat-input-area input[type="text"]:focus { border-color: var(--accent); }

    .chat-btn {
        width: 38px; height: 38px;
        border-radius: 50%;
        border: none;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .chat-btn svg { width: 18px; height: 18px; }
    .chat-btn-send {
        background: var(--accent);
        color: #fff;
        box-shadow: 0 2px 8px rgba(34,166,69,0.3);
    }
    .chat-btn-send:hover { background: var(--accent-h); transform: scale(1.05); }
    .chat-btn-send:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .chat-btn-camera {
        background: var(--surface2);
        color: var(--muted);
        border: 1px solid var(--border);
    }
    .chat-btn-camera:hover { background: var(--accent-lt); color: var(--accent); border-color: rgba(34,166,69,0.3); }
    .chat-btn-upload {
        background: var(--surface2);
        color: var(--muted);
        border: 1px solid var(--border);
    }
    .chat-btn-upload:hover { background: var(--accent-lt); color: var(--accent); border-color: rgba(34,166,69,0.3); }

    /* ── Welcome Screen ── */
    .chat-welcome {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 3rem;
        background: var(--surface2);
    }
    .chat-welcome-icon {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, var(--accent), #16a34a);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1.25rem;
        box-shadow: 0 8px 24px rgba(34,166,69,0.2);
    }
    .chat-welcome-icon svg { width: 36px; height: 36px; fill: #fff; }
    .chat-welcome h2 { font-size: 1.2rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem; }
    .chat-welcome p { font-size: 0.875rem; color: var(--muted); max-width: 360px; line-height: 1.6; margin-bottom: 1.5rem; }
    .chat-welcome .btn-start {
        padding: 0.75rem 1.5rem;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 24px;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 16px rgba(34,166,69,0.3);
        transition: all 0.2s;
    }
    .chat-welcome .btn-start:hover { background: var(--accent-h); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,166,69,0.35); }
    .chat-welcome .btn-start svg { width: 18px; height: 18px; fill: currentColor; }

    /* ── Queue Status ── */
    .queue-banner {
        padding: 0.75rem 1.25rem;
        background: rgba(59,130,246,0.08);
        border-bottom: 1px solid rgba(59,130,246,0.15);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.82rem;
        color: #1e40af;
        flex-shrink: 0;
    }
    .queue-banner svg { width: 16px; height: 16px; flex-shrink: 0; }
    .queue-pulse {
        width: 8px; height: 8px;
        background: #3b82f6;
        border-radius: 50%;
        animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }

    /* ── Photo Preview Modal ── */
    .photo-modal {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.8);
        display: none; align-items: center; justify-content: center;
        z-index: 300;
        cursor: pointer;
    }
    .photo-modal.show { display: flex; }
    .photo-modal img { max-width: 90vw; max-height: 90vh; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }

    @keyframes msgIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Camera preview */
    .camera-modal {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.9);
        display: none; flex-direction: column;
        align-items: center; justify-content: center;
        z-index: 300;
        gap: 1rem;
    }
    .camera-modal.show { display: flex; }
    .camera-modal video {
        max-width: 90vw; max-height: 60vh;
        border-radius: 12px;
        border: 2px solid rgba(255,255,255,0.2);
    }
    .camera-modal canvas { display: none; }
    .camera-actions { display: flex; gap: 0.75rem; }
    .camera-actions button {
        padding: 0.6rem 1.2rem;
        border-radius: 24px;
        border: none;
        font-size: 0.875rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .camera-capture { background: var(--accent); color: #fff; }
    .camera-cancel { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3) !important; }
</style>
@endpush

@section('content')
<div class="chat-container" id="chatContainer">
    <!-- Header -->
    <div class="chat-header">
        <div class="chat-bot-avatar">🤖</div>
        <div class="chat-header-info">
            <h3>Asisten Pengaduan</h3>
            <p id="chatStatus">Siap membantu kamu</p>
        </div>
        <div class="chat-header-actions">
            <button onclick="startNewChat()" id="btnNewChat" title="Mulai chat baru">
                <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14" style="width:14px;height:14px;margin-right:2px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Baru
            </button>
        </div>
    </div>

    <!-- Queue Banner -->
    <div class="queue-banner" id="queueBanner" style="display:none;">
        <div class="queue-pulse"></div>
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <span id="queueText">Menunggu admin...</span>
    </div>

    <!-- Welcome Screen (shown when no active session) -->
    <div class="chat-welcome" id="chatWelcome" style="{{ $session ? 'display:none;' : '' }}">
        <div class="chat-welcome-icon">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
        </div>
        <h2>Layanan Pengaduan Sekolah</h2>
        <p>Sampaikan keluhan, aspirasi, atau permasalahan yang kamu alami di sekolah. Asisten kami akan memandu kamu langkah demi langkah.</p>
        <button class="btn-start" onclick="startNewChat()">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            Mulai Chat
        </button>
    </div>

    <!-- Messages Area -->
    <div class="chat-messages" id="chatMessages" style="{{ !$session ? 'display:none;' : '' }}">
        <!-- Messages loaded dynamically -->
    </div>

    <!-- Typing Indicator -->
    <div class="typing-indicator" id="typingIndicator">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
    </div>

    <!-- Input Area -->
    <div class="chat-input-area" id="chatInputArea" style="{{ !$session ? 'display:none;' : '' }}">
        <button class="chat-btn chat-btn-camera" onclick="openCamera()" title="Ambil foto">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zM9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9z"/></svg>
        </button>
        <button class="chat-btn chat-btn-upload" onclick="document.getElementById('fileInput').click()" title="Upload gambar">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
        </button>
        <input type="file" id="fileInput" accept="image/*" style="display:none;" onchange="handleFileUpload(this)">
        <input type="text" id="chatInput" placeholder="Ketik pesan..." onkeydown="if(event.key==='Enter')sendChatMessage()">
        <button class="chat-btn chat-btn-send" onclick="sendChatMessage()" id="btnSend">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>
    </div>
</div>

<!-- Photo Preview Modal -->
<div class="photo-modal" id="photoModal" onclick="this.classList.remove('show')">
    <img id="photoModalImg" src="" alt="Preview">
</div>

<!-- Camera Modal -->
<div class="camera-modal" id="cameraModal">
    <video id="cameraVideo" autoplay playsinline></video>
    <canvas id="cameraCanvas"></canvas>
    <div class="camera-actions">
        <button class="camera-capture" onclick="capturePhoto()">📸 Ambil Foto</button>
        <button class="camera-cancel" onclick="closeCamera()">Batal</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    const ROUTES = {
        start:      '{{ route("siswa.chat.start") }}',
        send:       '{{ route("siswa.chat.send") }}',
        uploadPhoto:'{{ route("siswa.chat.upload-photo") }}',
        messages:   '{{ route("siswa.chat.messages") }}',
        escalate:   '{{ route("siswa.chat.escalate") }}',
    };

    let sessionId = '{{ $session?->id ?? "" }}';
    let currentStep = null;
    let pollInterval = null;
    let lastMessageTime = null;

    // ── Init ──
    document.addEventListener('DOMContentLoaded', function () {
        if (sessionId) {
            loadMessages();
            startPolling();
        }
    });

    // ── Start New Chat ──
    async function startNewChat() {
        try {
            const res = await fetch(ROUTES.start, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            const data = await res.json();
            if (data.success) {
                sessionId = data.session_id;
                document.getElementById('chatWelcome').style.display = 'none';
                document.getElementById('chatMessages').style.display = 'flex';
                document.getElementById('chatInputArea').style.display = 'flex';
                document.getElementById('chatMessages').innerHTML = '';
                renderMessages(data.messages);
                startPolling();
            }
        } catch (e) { console.error(e); }
    }

    // ── Send Message ──
    async function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const msg = input.value.trim();
        if (!msg || !sessionId) return;

        input.value = '';

        // Find current step from last bot options message
        const step = currentStep;

        showTyping();

        try {
            const res = await fetch(ROUTES.send, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ session_id: sessionId, message: msg, step: step, value: msg }),
            });
            const data = await res.json();
            hideTyping();
            if (data.messages) {
                renderMessages(data.messages);
            }
            // Reload all messages to keep in sync
            loadMessages();
        } catch (e) {
            hideTyping();
            console.error(e);
        }
    }

    // ── Select Option ──
    async function selectOption(step, value, label, btnEl) {
        // Disable all buttons in this group
        const parent = btnEl.closest('.msg-options');
        if (parent) {
            parent.querySelectorAll('.msg-option-btn').forEach(b => {
                b.classList.remove('selected');
                b.style.pointerEvents = 'none';
                b.style.opacity = '0.5';
            });
            btnEl.classList.add('selected');
            btnEl.style.opacity = '1';
        }

        showTyping();

        try {
            const res = await fetch(ROUTES.send, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ session_id: sessionId, message: label, step: step, value: value }),
            });
            const data = await res.json();
            hideTyping();
            if (data.messages) {
                renderMessages(data.messages);
            }

            // Handle special cases
            if (step === 'selesai' && value === 'baru') {
                startNewChat();
            }

            loadMessages();
        } catch (e) {
            hideTyping();
            console.error(e);
        }
    }

    // ── Upload Photo ──
    async function handleFileUpload(input) {
        const file = input.files[0];
        if (!file || !sessionId) return;

        const formData = new FormData();
        formData.append('session_id', sessionId);
        formData.append('photo', file);

        showTyping();

        try {
            const res = await fetch(ROUTES.uploadPhoto, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body: formData,
            });
            const data = await res.json();
            hideTyping();
            if (data.bot_messages) {
                renderMessages(data.bot_messages);
            }
            loadMessages();
        } catch (e) {
            hideTyping();
            console.error(e);
        }

        input.value = '';
    }

    // ── Camera ──
    let cameraStream = null;

    async function openCamera() {
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            document.getElementById('cameraVideo').srcObject = cameraStream;
            document.getElementById('cameraModal').classList.add('show');
        } catch (e) {
            alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
        }
    }

    function closeCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(t => t.stop());
            cameraStream = null;
        }
        document.getElementById('cameraModal').classList.remove('show');
    }

    function capturePhoto() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(async (blob) => {
            closeCamera();
            const formData = new FormData();
            formData.append('session_id', sessionId);
            formData.append('photo', blob, 'camera_' + Date.now() + '.jpg');

            showTyping();
            try {
                const res = await fetch(ROUTES.uploadPhoto, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF },
                    body: formData,
                });
                const data = await res.json();
                hideTyping();
                if (data.bot_messages) {
                    renderMessages(data.bot_messages);
                }
                loadMessages();
            } catch (e) {
                hideTyping();
                console.error(e);
            }
        }, 'image/jpeg', 0.85);
    }

    // ── Escalate to Admin ──
    async function requestEscalation() {
        if (!sessionId) return;
        try {
            const res = await fetch(ROUTES.escalate, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ session_id: sessionId }),
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('queueBanner').style.display = 'flex';
                document.getElementById('queueText').textContent = `Posisi antrean: #${data.position} — Menunggu admin...`;
                loadMessages();
            }
        } catch (e) { console.error(e); }
    }

    // ── Load / Poll Messages ──
    async function loadMessages() {
        if (!sessionId) return;
        try {
            let url = ROUTES.messages + `?session_id=${sessionId}`;
            const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': CSRF } });
            const data = await res.json();
            if (data.success) {
                // Clear and re-render all
                const container = document.getElementById('chatMessages');
                container.innerHTML = '';
                renderMessages(data.messages);

                // Update status
                if (data.status === 'queued') {
                    document.getElementById('queueBanner').style.display = 'flex';
                    document.getElementById('chatStatus').textContent = 'Menunggu admin...';
                } else if (data.status === 'active') {
                    document.getElementById('queueBanner').style.display = 'none';
                    document.getElementById('chatStatus').textContent = 'Terhubung dengan admin';
                } else if (data.status === 'resolved') {
                    document.getElementById('chatStatus').textContent = 'Chat selesai';
                    document.getElementById('queueBanner').style.display = 'none';
                } else {
                    document.getElementById('queueBanner').style.display = 'none';
                    document.getElementById('chatStatus').textContent = 'Siap membantu kamu';
                }

                // Track last message time
                if (data.messages.length > 0) {
                    lastMessageTime = data.messages[data.messages.length - 1].created_at;
                }
            }
        } catch (e) { console.error(e); }
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            if (!sessionId) return;
            try {
                let url = ROUTES.messages + `?session_id=${sessionId}`;
                if (lastMessageTime) url += `&after=${encodeURIComponent(lastMessageTime)}`;
                const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': CSRF } });
                const data = await res.json();
                if (data.success && data.messages.length > 0) {
                    // Only render new messages
                    renderMessages(data.messages);
                    lastMessageTime = data.messages[data.messages.length - 1].created_at;
                }

                // Status updates
                if (data.status === 'active') {
                    document.getElementById('queueBanner').style.display = 'none';
                    document.getElementById('chatStatus').textContent = 'Terhubung dengan admin';
                } else if (data.status === 'resolved') {
                    document.getElementById('chatStatus').textContent = 'Chat selesai';
                    document.getElementById('queueBanner').style.display = 'none';
                }
            } catch (e) {}
        }, 3000); // Poll every 3s
    }

    // ── Render Messages ──
    function renderMessages(messages) {
        const container = document.getElementById('chatMessages');

        messages.forEach(msg => {
            // Skip if message already rendered
            if (document.getElementById('msg-' + msg.id)) return;

            const div = document.createElement('div');
            div.className = `msg ${msg.sender_type}`;
            div.id = 'msg-' + msg.id;

            const avatarLabel = msg.sender_type === 'bot' ? '🤖' :
                                msg.sender_type === 'admin' ? '👨‍💼' :
                                '{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}';

            let bubbleContent = '';

            // Format message text
            if (msg.message && msg.message !== '__SELECT_KATEGORI__') {
                let text = msg.message
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');
                bubbleContent += text;
            }

            // Attachment
            if (msg.attachment) {
                bubbleContent += `<br><img class="chat-photo" src="/storage/${msg.attachment}" alt="Foto" onclick="showPhoto('/storage/${msg.attachment}')">`;
            }

            // Track currentStep from ANY bot message with metadata.step
            if (msg.metadata && msg.metadata.step) {
                currentStep = msg.metadata.step;
            }

            // Options buttons
            if (msg.metadata && msg.metadata.type === 'options' && msg.metadata.options) {
                bubbleContent += '<div class="msg-options">';
                msg.metadata.options.forEach(opt => {
                    bubbleContent += `<button class="msg-option-btn" onclick="selectOption('${msg.metadata.step}', '${opt.id}', '${opt.label.replace(/'/g, "\\'")}', this)">${opt.label}</button>`;
                });
                bubbleContent += '</div>';
            }

            // Time
            const time = new Date(msg.created_at);
            const timeStr = time.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            div.innerHTML = `
                <div class="msg-avatar">${avatarLabel}</div>
                <div>
                    <div class="msg-bubble">${bubbleContent}</div>
                    <div class="msg-time">${timeStr}</div>
                </div>
            `;

            container.appendChild(div);
        });

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }

    // ── Helpers ──
    function showTyping() { document.getElementById('typingIndicator').classList.add('show'); }
    function hideTyping() { document.getElementById('typingIndicator').classList.remove('show'); }

    function showPhoto(src) {
        document.getElementById('photoModalImg').src = src;
        document.getElementById('photoModal').classList.add('show');
    }
</script>
@endpush
