<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('readiness_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_id')->nullable()->constrained()->nullOnDelete();
            $table->string('trigger');
            $table->decimal('score', 5, 2);
            $table->decimal('skill_mastery', 5, 2);
            $table->decimal('roadmap_completion', 5, 2);
            $table->decimal('project_score', 5, 2);
            $table->decimal('consistency', 5, 2);
            $table->decimal('evaluation_score', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readiness_snapshots');
    }
};
