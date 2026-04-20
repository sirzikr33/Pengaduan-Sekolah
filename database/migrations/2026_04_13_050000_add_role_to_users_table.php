<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'siswa'])->default('admin')->after('email');
            $table->uuid('siswa_id')->nullable()->after('role');

            // Foreign key ke siswas.id (UUID)
            $table->foreign('siswa_id')
                  ->references('id')
                  ->on('siswas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn(['role', 'siswa_id']);
        });
    }
};
