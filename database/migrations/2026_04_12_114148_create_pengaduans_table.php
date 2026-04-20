<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignUuid('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('nama_pengaduan');
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->string('foto_pengaduan');
            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending');
            $table->text('catatan')->nullable();
            $table->enum('kondisi_pengaduan', ['berat', 'sedang', 'ringan']);
            $table->date('tanggal_pengaduan');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
