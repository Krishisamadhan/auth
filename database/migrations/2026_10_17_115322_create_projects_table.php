<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('project_title');
            $table->text('project_description');
            $table->string('project_location');
            $table->date('started_date');
            $table->date('end_date');
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'ongoing', 'completed'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
