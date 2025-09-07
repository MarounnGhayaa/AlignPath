<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("path_id");
            $table->foreign("path_id")->references("id")->on("paths")->onDelete("cascade");
            $table->string('title');
            $table->string('subtitle');
            $table->string('first_answer');
            $table->string('second_answer');
            $table->string('third_answer');
            $table->string('correct_answer');
            $table->integer('points');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('problems');
    }
};
