<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use App\Models\LearningMaterial;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class AcademicProgramLearningMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $skills = Skill::query()
            ->where(
                'slug',
                'like',
                'si-%',
            )
            ->orWhere(
                'slug',
                'like',
                'man-%',
            )
            ->orWhere(
                'slug',
                'like',
                'ti-%',
            )
            ->orWhere(
                'slug',
                'like',
                'sk-%',
            )
            ->orWhere(
                'slug',
                'like',
                'psi-%',
            )
            ->orWhere(
                'slug',
                'like',
                'ikom-%',
            )
            ->get();

        foreach ($skills as $skill) {
            $question = AssessmentQuestion::query()
                ->where(
                    'skill_id',
                    $skill->id,
                )
                ->whereHas(
                    'assessment',
                    fn ($query) => $query->where(
                        'is_active',
                        true,
                    ),
                )
                ->first();

            if (! $question) {
                continue;
            }

            $core = LearningMaterial::updateOrCreate(
                [
                    'slug' => 'belajar-'.$skill->slug,
                ],
                [
                    'skill_id' => $skill->id,
                    'material_type' => 'core',
                    'reinforcement_for_material_id' => null,
                    'is_active' => true,
                    'title' => 'Memahami '.$skill->name,
                    'summary' => $skill->description,
                    'learning_objectives' => [
                        'Memahami konsep utama '.$skill->name,
                        'Menghubungkan konsep dengan situasi nyata',
                        'Menerapkan pemahaman melalui latihan sederhana',
                    ],
                    'difficulty' => $skill->difficulty,
                    'estimated_minutes' => $skill->difficulty === 'Dasar'
                        ? 90
                        : 120,
                    'resource_title' => 'Materi pembelajaran SkillPath',
                    'resource_url' => null,
                    'practice_task' => 'Buat latihan sederhana yang menunjukkan pemahamanmu tentang '.$skill->name.'. Jelaskan langkah yang kamu lakukan, hasilnya, dan bagian yang menurutmu masih perlu diperbaiki.',
                    'quiz_question' => $question->prompt,
                    'quiz_options' => $question->options,
                    'quiz_answer' => $question->correct_answer,
                    'quiz_explanation' => $question->explanation,
                ],
            );

            LearningMaterial::updateOrCreate(
                [
                    'slug' => 'penguatan-'.$skill->slug,
                ],
                [
                    'skill_id' => $skill->id,
                    'material_type' => 'reinforcement',
                    'reinforcement_for_material_id' => $core->id,
                    'is_active' => true,
                    'title' => 'Penguatan: '.$skill->name,
                    'summary' => 'Pelajari kembali bagian penting dari '.$skill->name.' dengan ruang lingkup yang lebih kecil sebelum mencoba evaluasi berikutnya.',
                    'learning_objectives' => [
                        'Mengulang konsep yang masih belum dipahami',
                        'Menemukan bagian yang menyebabkan jawaban sebelumnya kurang tepat',
                        'Mencoba kembali konsep melalui latihan yang lebih terarah',
                    ],
                    'difficulty' => $skill->difficulty,
                    'estimated_minutes' => 60,
                    'resource_title' => 'Materi penguatan SkillPath',
                    'resource_url' => null,
                    'practice_task' => 'Ulangi latihan '.$skill->name.' dengan contoh yang lebih sederhana. Tuliskan apa yang sebelumnya kurang tepat dan bagaimana kamu memperbaikinya.',
                    'quiz_question' => $question->prompt,
                    'quiz_options' => $question->options,
                    'quiz_answer' => $question->correct_answer,
                    'quiz_explanation' => $question->explanation,
                ],
            );
        }
    }
}
