<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chat_session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'bot', 'admin']);
            $table->unsignedBigInteger('sender_id')->nullable(); // null for bot
            $table->text('message')->nullable();
            $table->string('attachment')->nullable(); // file path for photos
            $table->enum('attachment_type', ['image', 'file'])->nullable();
            $table->json('metadata')->nullable(); // for bot buttons/options
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
