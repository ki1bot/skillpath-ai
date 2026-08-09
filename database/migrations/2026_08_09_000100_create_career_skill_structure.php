<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline');
            $table->text('description');
            $table->json('responsibilities')->nullable();
            $table->string('difficulty')->default('Menengah');
            $table->string('accent')->default('#C7FF5E');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');
            $table->text('description');
            $table->string('difficulty')->default('Dasar');
            $table->timestamps();
        });

        Schema::create('career_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('target_level');
            $table->decimal('importance_weight', 4, 2)->default(1);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            $table->unique(['career_id', 'skill_id']);
        });

        Schema::create('skill_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prerequisite_skill_id')->constrained('skills')->cascadeOnDelete();
            $table->decimal('factor', 4, 2)->default(1.20);
            $table->timestamps();
            $table->unique(['skill_id', 'prerequisite_skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_prerequisites');
        Schema::dropIfExists('career_skill');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('careers');
    }
};
