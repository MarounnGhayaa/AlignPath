<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('chat_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('role', ['user', 'model'])->index();
            $table->longText('content');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['thread_id', 'created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('chat_messages');
    }
};
