<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'pengaduan_id',
        'status',
        'admin_id',
        'queue_position',
        'queued_at',
        'accepted_at',
        'resolved_at',
    ];

    protected $casts = [
        'queued_at'    => 'datetime',
        'accepted_at'  => 'datetime',
        'resolved_at'  => 'datetime',
    ];

    // ── Status helpers ──

    public function isChatbot(): bool  { return $this->status === 'chatbot'; }
    public function isQueued(): bool   { return $this->status === 'queued'; }
    public function isActive(): bool   { return $this->status === 'active'; }
    public function isResolved(): bool { return $this->status === 'resolved'; }

    // ── Relations ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
