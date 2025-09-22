<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('learning_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("path_id");
            $table->foreign("path_id")->references("id")->on("paths")->onDelete("cascade");
            $table->string('name');
            $table->string('description');
            $table->enum('type', ['documentation', 'video', 'community'])->default('documentation');
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('resources');
    }
};
