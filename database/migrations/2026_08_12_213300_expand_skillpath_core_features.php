<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table
                ->string('question_type')
                ->default('multiple_choice')
                ->index();

            $table
                ->text('practical_instructions')
                ->nullable();

            $table
                ->boolean('evidence_required')
                ->default(false);
        });

        Schema::table('assessment_results', function (Blueprint $table) {
            $table
                ->foreignId('assessment_question_id')
                ->nullable()
                ->constrained('assessment_questions')
                ->nullOnDelete();

            $table
                ->text('response_text')
                ->nullable();

            $table
                ->text('evidence_url')
                ->nullable();

            $table
                ->text('experience_notes')
                ->nullable();

            $table
                ->text('experience_evidence_url')
                ->nullable();
        });

        Schema::table('learning_materials', function (Blueprint $table) {
            $table
                ->string('material_type')
                ->default('core')
                ->index();

            $table
                ->foreignId('reinforcement_for_material_id')
                ->nullable()
                ->constrained('learning_materials')
                ->nullOnDelete();

            $table
                ->boolean('is_active')
                ->default(true);
        });

        Schema::table('roadmap_items', function (Blueprint $table) {
            $table
                ->unsignedSmallInteger('evaluation_attempts')
                ->default(0);

            $table
                ->unsignedSmallInteger('reinforcement_count')
                ->default(0);

            $table
                ->foreignId('reinforcement_for_roadmap_item_id')
                ->nullable()
                ->constrained('roadmap_items')
                ->nullOnDelete();
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table
                ->decimal('knowledge_score', 5, 2)
                ->default(0);

            $table
                ->decimal('evidence_score', 5, 2)
                ->default(0);

            $table
                ->decimal('reflection_score', 5, 2)
                ->default(0);

            $table
                ->text('evidence_url')
                ->nullable();

            $table
                ->text('reflection')
                ->nullable();
        });

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->string('category')
                ->index();

            $table->string('subject');

            $table->text('message');

            $table
                ->unsignedTinyInteger('rating')
                ->nullable();

            $table
                ->string('status')
                ->default('pending')
                ->index();

            $table
                ->text('admin_response')
                ->nullable();

            $table
                ->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table
                ->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'knowledge_score',
                'evidence_score',
                'reflection_score',
                'evidence_url',
                'reflection',
            ]);
        });

        Schema::table('roadmap_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'reinforcement_for_roadmap_item_id',
            );

            $table->dropColumn([
                'evaluation_attempts',
                'reinforcement_count',
            ]);
        });

        Schema::table('learning_materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'reinforcement_for_material_id',
            );

            $table->dropColumn([
                'material_type',
                'is_active',
            ]);
        });

        Schema::table('assessment_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'assessment_question_id',
            );

            $table->dropColumn([
                'response_text',
                'evidence_url',
                'experience_notes',
                'experience_evidence_url',
            ]);
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_type',
                'practical_instructions',
                'evidence_required',
            ]);
        });
    }
};
