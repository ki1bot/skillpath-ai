<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use App\Models\LearningMaterial;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class AssessmentEnhancementSeeder extends Seeder
{
    public function run(): void
    {
        $practicalSkills = [
            'git-github',
            'sql',
            'deployment-basics',
            'react',
            'accessibility',
            'pandas',
            'data-visualization',
        ];

        $caseSkills = [
            'http-fundamentals',
            'database-fundamentals',
            'rest-api',
            'authentication-authorization',
            'state-management',
            'web-performance',
            'statistics-fundamentals',
            'data-cleaning',
            'sql-analytics',
        ];

        AssessmentQuestion::query()
            ->with([
                'skill.materials',
            ])
            ->get()
            ->each(
                function (
                    AssessmentQuestion $question,
                ) use (
                    $practicalSkills,
                    $caseSkills,
                ): void {
                    $skill = $question->skill;

                    if (! $skill instanceof Skill) {
                        return;
                    }

                    $questionType = 'multiple_choice';

                    if (
                        in_array(
                            $skill->slug,
                            $practicalSkills,
                            true,
                        )
                    ) {
                        $questionType = 'practical';
                    } elseif (
                        in_array(
                            $skill->slug,
                            $caseSkills,
                            true,
                        )
                    ) {
                        $questionType = 'case';
                    }

                    $practicalInstructions = null;
                    $evidenceRequired = false;

                    if (
                        $questionType
                        === 'practical'
                    ) {
                        $coreMaterial = $skill
                            ->materials
                            ->first(
                                fn (
                                    LearningMaterial $material,
                                ): bool => (
                                    $material->material_type
                                    === 'core'
                                    && $material->is_active
                                ),
                            );

                        if (
                            $coreMaterial
                            instanceof LearningMaterial
                        ) {
                            $practicalInstructions = $coreMaterial
                                ->practice_task;
                        }

                        $practicalInstructions ??= (
                            'Kerjakan tugas praktik yang relevan dengan keterampilan ini, '
                            .'jelaskan hasilnya, dan sertakan tautan bukti pekerjaan Anda.'
                        );

                        $evidenceRequired = true;
                    }

                    $question->update([
                        'question_type' => $questionType,

                        'practical_instructions' => $practicalInstructions,

                        'evidence_required' => $evidenceRequired,
                    ]);
                },
            );
    }
}
