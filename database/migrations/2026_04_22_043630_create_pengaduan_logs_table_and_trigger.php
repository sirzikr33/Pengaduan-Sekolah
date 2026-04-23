<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel log
        Schema::create('pengaduan_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('pengaduan_id');
            $table->string('status_lama');
            $table->string('status_baru');
            $table->timestamp('changed_at')->useCurrent();

            $table->foreign('pengaduan_id')
                  ->references('id')
                  ->on('pengaduans')
                  ->onDelete('cascade'); // Jika pengaduan dihapus, lognya juga ikut terhapus
        });

        // 2. Buat Trigger AFTER UPDATE di tabel pengaduans
        DB::unprepared('
            CREATE TRIGGER trg_pengaduan_status_audit
            AFTER UPDATE ON pengaduans
            FOR EACH ROW
            BEGIN
                IF OLD.status <> NEW.status THEN
                    INSERT INTO pengaduan_logs (pengaduan_id, status_lama, status_baru, changed_at)
                    VALUES (NEW.id, OLD.status, NEW.status, NOW());
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus trigger
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengaduan_status_audit');

        // 2. Hapus tabel log
        Schema::dropIfExists('pengaduan_logs');
    }
};
