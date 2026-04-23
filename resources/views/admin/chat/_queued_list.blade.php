@forelse($queuedChats as $chat)
    <div class="chat-card">
        <div class="chat-card-avatar">{{ strtoupper(substr($chat->user->siswa->nama ?? $chat->user->name, 0, 1)) }}</div>
        <div class="chat-card-info">
            <div class="chat-card-name">{{ $chat->user->siswa->nama ?? $chat->user->name }}</div>
            <div class="chat-card-meta">
                <span class="badge-queue">Antrean #{{ $chat->queue_position }}</span>
                · {{ $chat->user->siswa->kelas ?? '-' }}
            </div>
        </div>
        <div class="chat-card-time">
            {{ $chat->queued_at?->diffForHumans() }}
        </div>
        <div class="chat-card-actions">
            <form action="{{ route('admin.chat.accept', $chat) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Terima Chat</button>
            </form>
        </div>
    </div>
@empty
    <div class="empty-chat-state">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
        <p>Tidak ada chat dalam antrean saat ini.</p>
    </div>
@endforelse
