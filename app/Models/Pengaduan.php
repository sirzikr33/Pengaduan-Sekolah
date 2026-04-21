<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengaduan extends Model
{
    use HasUuids;

    protected $fillable = [
        'siswa_id',
        'kategori_id',
        'nama_pengaduan',
        'deskripsi',
        'lokasi',
        'foto_pengaduan',
        'status',
        'catatan',
        'kondisi_pengaduan',
        'tanggal_pengaduan',
    ];

    protected $casts = [
        'tanggal_pengaduan' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function chatSession(): HasOne
    {
        return $this->hasOne(ChatSession::class, 'pengaduan_id');
    }
}
