<?php

namespace App\Notifications;

use App\Models\Pengaduan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengaduanStatusChanged extends Notification
{
    use Queueable;

    public function __construct(protected Pengaduan $pengaduan, protected string $statusLama)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusBaru = ucfirst($this->pengaduan->status);
        $statusIcon = match($this->pengaduan->status) {
            'proses'  => '🔄',
            'selesai' => '✅',
            default   => '📋',
        };

        return [
            'type'          => 'pengaduan_status',
            'pengaduan_id'  => $this->pengaduan->id,
            'judul'         => $this->pengaduan->nama_pengaduan,
            'status_lama'   => ucfirst($this->statusLama),
            'status_baru'   => $statusBaru,
            'pesan'         => "{$statusIcon} Status pengaduanmu \"{$this->pengaduan->nama_pengaduan}\" berubah menjadi **{$statusBaru}**.",
            'url'           => route('siswa.pengaduan.show', $this->pengaduan->id),
        ];
    }
}
