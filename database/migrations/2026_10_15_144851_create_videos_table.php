<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('original_path');
            $table->string('processed_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('status')->default('pending'); // pending|processing|ready|failed
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};