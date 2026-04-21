<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Menyimpan draf data pengaduan (menggantikan cache) agar tidak kadaluwarsa
            $table->json('payload_data')->nullable()->after('resolved_at');
            // Menyimpan path foto sementara (untuk dihapus jika batal)
            $table->string('photo_path', 500)->nullable()->after('payload_data');
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['payload_data', 'photo_path']);
        });
    }
};
