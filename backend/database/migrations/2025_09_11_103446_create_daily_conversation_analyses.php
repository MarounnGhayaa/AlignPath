<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('daily_conversation_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('thread_id')->nullable()->constrained('chat_threads')->nullOnDelete();
            $table->date('day')->index();
            $table->text('summary')->nullable();
            $table->json('attributes')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('daily_conversation_analyses');
    }
};
