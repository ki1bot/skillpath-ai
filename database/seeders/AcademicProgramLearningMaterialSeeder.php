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

            $quiz = $this->quizData(
                $skill,
                $question,
            );

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
                    'quiz_question' => $quiz['question'],
                    'quiz_options' => $quiz['options'],
                    'quiz_answer' => $quiz['answer'],
                    'quiz_explanation' => $quiz['explanation'],
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
                    'quiz_question' => $quiz['question'],
                    'quiz_options' => $quiz['options'],
                    'quiz_answer' => $quiz['answer'],
                    'quiz_explanation' => $quiz['explanation'],
                ],
            );
        }
    }

    /**
     * @return array{
     *     question: string,
     *     options: array<string, string>,
     *     answer: string,
     *     explanation: string
     * }
     */
    private function quizData(
        Skill $skill,
        ?AssessmentQuestion $question,
    ): array {
        if ($question) {
            $options = $this->questionOptions(
                $question,
            );

            if ($options !== null) {
                return [
                    'question' => $question->prompt,
                    'options' => $options,
                    'answer' => $question->correct_answer,
                    'explanation' => $question->explanation
                        ?? 'Jawaban dinilai berdasarkan pemahaman konsep '.$skill->name.'.',
                ];
            }
        }

        return [
            'question' => 'Pernyataan mana yang paling tepat menggambarkan cara mempelajari '.$skill->name.'?',
            'options' => [
                'A' => 'Memahami konsep utamanya, melihat penerapan, lalu mencoba latihan yang relevan.',
                'B' => 'Menghafal istilah tanpa memahami konteks atau penerapannya.',
                'C' => 'Mengabaikan konsep dasar dan langsung meniru hasil akhir.',
                'D' => 'Menghindari latihan dan evaluasi agar tidak menemukan kesalahan.',
            ],
            'answer' => 'A',
            'explanation' => 'Pembelajaran '.$skill->name.' perlu mencakup pemahaman konsep, konteks penerapan, dan latihan yang dapat dievaluasi.',
        ];
    }

    /**
     * @return array<string, string>|null
     */
    private function questionOptions(
        AssessmentQuestion $question,
    ): ?array {
        $rawOptions = $question->getAttribute(
            'options',
        );

        if (
            ! is_array($rawOptions)
            || $rawOptions === []
        ) {
            return null;
        }

        $options = [];

        foreach ($rawOptions as $key => $value) {
            if (! is_string($value)) {
                return null;
            }

            $options[
                (string) $key
            ] = $value;
        }

        return $options;
    }
}
