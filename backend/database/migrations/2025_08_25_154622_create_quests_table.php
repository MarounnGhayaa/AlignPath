<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("path_id");
            $table->foreign("path_id")->references("id")->on("paths")->onDelete("cascade");
            $table->string('title');
            $table->string('subtitle');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->decimal('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('quests');
    }
};
