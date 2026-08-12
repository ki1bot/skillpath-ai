<?php

namespace Database\Seeders;

use App\Models\LearningMaterial;
use Illuminate\Database\Seeder;

class ReinforcementMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $coreMaterials = LearningMaterial::query()
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->with('skill.prerequisites')
            ->get();

        foreach ($coreMaterials as $coreMaterial) {
            $prerequisiteSkill = $coreMaterial
                ->skill
                ?->prerequisites
                ->first();

            $foundationMaterial = $prerequisiteSkill
                ? LearningMaterial::query()
                    ->where(
                        'skill_id',
                        $prerequisiteSkill->id,
                    )
                    ->where(
                        'material_type',
                        'core',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->first()
                : null;

            $foundationMaterial ??= $coreMaterial;

            $estimatedMinutes = max(
                45,
                min(
                    (int) $coreMaterial->estimated_minutes,
                    90,
                ),
            );

            LearningMaterial::updateOrCreate(
                [
                    'slug' => 'penguatan-'.$coreMaterial->slug,
                ],
                [
                    'skill_id' => $coreMaterial->skill_id,

                    'title' => 'Penguatan: '.$coreMaterial->title,

                    'summary' => 'Materi penguatan otomatis untuk mengulang fondasi yang diperlukan sebelum mencoba evaluasi materi utama kembali.',

                    'learning_objectives' => [
                        'Mengulang konsep inti yang masih lemah.',
                        'Menghubungkan konsep prasyarat dengan materi utama.',
                        'Mengerjakan latihan ulang sebelum evaluasi berikutnya.',
                    ],

                    'difficulty' => $coreMaterial->difficulty,

                    'estimated_minutes' => $estimatedMinutes,

                    'resource_title' => $foundationMaterial
                        ->resource_title,

                    'resource_url' => $foundationMaterial
                        ->resource_url,

                    'practice_task' => $foundationMaterial
                        ->practice_task,

                    'quiz_question' => $foundationMaterial
                        ->quiz_question,

                    'quiz_options' => $foundationMaterial
                        ->quiz_options,

                    'quiz_answer' => $foundationMaterial
                        ->quiz_answer,

                    'quiz_explanation' => $foundationMaterial
                        ->quiz_explanation,

                    'material_type' => 'reinforcement',

                    'reinforcement_for_material_id' => $coreMaterial
                        ->id,

                    'is_active' => true,
                ],
            );
        }
    }
}
