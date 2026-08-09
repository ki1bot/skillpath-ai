<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->json('learning_objectives');
            $table->string('difficulty')->default('Dasar');
            $table->unsignedSmallInteger('estimated_minutes')->default(90);
            $table->string('resource_title')->nullable();
            $table->text('resource_url')->nullable();
            $table->text('practice_task');
            $table->text('quiz_question');
            $table->json('quiz_options');
            $table->string('quiz_answer');
            $table->text('quiz_explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('reason')->default('Asesmen awal');
            $table->unsignedSmallInteger('estimated_weeks')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('roadmap_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_material_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('stage')->default(1);
            $table->string('stage_title');
            $table->unsignedSmallInteger('position');
            $table->string('status')->default('locked');
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('evaluation_score', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['roadmap_id', 'learning_material_id']);
        });

        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('roadmap_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('activity_type');
            $table->unsignedSmallInteger('minutes_spent')->default(0);
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->text('obstacle')->nullable();
            $table->text('evidence_url')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('roadmap_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->boolean('passed');
            $table->string('answer')->nullable();
            $table->text('feedback');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('progress_logs');
        Schema::dropIfExists('roadmap_items');
        Schema::dropIfExists('roadmaps');
        Schema::dropIfExists('learning_materials');
    }
};
