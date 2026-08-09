<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->text('problem_statement');
            $table->string('difficulty')->default('Pemula');
            $table->json('minimum_features');
            $table->json('stretch_features')->nullable();
            $table->json('completion_criteria');
            $table->unsignedSmallInteger('estimated_hours')->default(8);
            $table->timestamps();
        });

        Schema::create('portfolio_project_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('required_level')->default(60);
            $table->decimal('weight', 4, 2)->default(1);
            $table->timestamps();
            $table->unique(['portfolio_project_id', 'skill_id']);
        });

        Schema::create('user_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('portfolio_project_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('planned');
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->text('repository_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'portfolio_project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_projects');
        Schema::dropIfExists('portfolio_project_skill');
        Schema::dropIfExists('portfolio_projects');
    }
};
