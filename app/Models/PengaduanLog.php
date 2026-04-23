<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanLog extends Model
{
    protected $table = 'pengaduan_logs';
    
    // Kita menonaktifkan timestamps bawaan Laravel karena tabel ini 
    // hanya menggunakan 'changed_at' dari MySQL Trigger
    public $timestamps = false; 

    protected $fillable = [
        'pengaduan_id', 
        'status_lama', 
        'status_baru', 
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }
}
