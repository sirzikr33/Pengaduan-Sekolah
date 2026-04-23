<?php

namespace App\Notifications;

use App\Models\ChatSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminChatAccepted extends Notification
{
    use Queueable;

    public function __construct(
        protected ChatSession $chatSession,
        protected string      $adminName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'chat_accepted',
            'chat_session_id' => $this->chatSession->id,
            'admin_name'      => $this->adminName,
            'pesan'           => "💬 Admin **{$this->adminName}** sudah menerima chat kamu. Silakan buka halaman Chat untuk melanjutkan.",
            'url'             => route('siswa.chat.index'),
        ];
    }
}
