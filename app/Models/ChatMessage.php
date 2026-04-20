<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasUuids;

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'sender_id',
        'message',
        'attachment',
        'attachment_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // ── Relations ──

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── Helpers ──

    public function isFromUser(): bool  { return $this->sender_type === 'user'; }
    public function isFromBot(): bool   { return $this->sender_type === 'bot'; }
    public function isFromAdmin(): bool { return $this->sender_type === 'admin'; }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }
}
