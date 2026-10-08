<?php

namespace Database\Seeders;

use App\Models\LearningMaterial;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Seeder;
use RuntimeException;

class AcademicStageLearningMaterialSeeder extends Seeder
{
    private const STAGES = [
        'amatir' => [
            'title' => 'Amatir',
            'estimated_minutes' => 90,
            'reinforcement_minutes' => 60,
        ],
        'menengah' => [
            'title' => 'Menengah',
            'estimated_minutes' => 120,
            'reinforcement_minutes' => 75,
        ],
        'ahli' => [
            'title' => 'Ahli',
            'estimated_minutes' => 150,
            'reinforcement_minutes' => 90,
        ],
    ];

    public function run(): void
    {
        $skillSlugs = AcademicProgramCatalog::allSkillSlugs();

        $skills = Skill::query()
            ->whereIn('slug', $skillSlugs)
            ->get()
            ->keyBy('slug');

        if ($skills->count() !== count($skillSlugs)) {
            throw new RuntimeException(
                'Katalog skill akademik belum lengkap untuk membuat materi bertahap.',
            );
        }

        $canonicalMaterialSlugs = [];

        foreach ($skillSlugs as $skillSlug) {
            $skill = $skills->get($skillSlug);

            if (! $skill) {
                throw new RuntimeException(
                    'Skill '.$skillSlug.' tidak ditemukan.',
                );
            }

            $source = LearningMaterial::query()
                ->where('slug', 'belajar-'.$skillSlug)
                ->first();

            if (! $source) {
                throw new RuntimeException(
                    'Materi dasar '.$skillSlug.' belum tersedia. Jalankan AcademicProgramLearningMaterialSeeder terlebih dahulu.',
                );
            }

            foreach (self::STAGES as $stageSlug => $stage) {
                $coreSlug = 'belajar-'.$stageSlug.'-'.$skillSlug;

                $reinforcementSlug = 'penguatan-'.$stageSlug.'-'.$skillSlug;

                $practiceTask = $this->stageTask(
                    (string) $source->practice_task,
                    $skill->name,
                    $stageSlug,
                );

                $core = LearningMaterial::firstOrCreate(
                    [
                        'slug' => $coreSlug,
                    ],
                    [
                        'skill_id' => $skill->id,
                        'material_type' => 'core',
                        'reinforcement_for_material_id' => null,
                        'is_active' => true,
                        'title' => $stage['title'].': '.$skill->name,
                        'summary' => $this->stageSummary(
                            $skill->name,
                            $stageSlug,
                        ),
                        'learning_objectives' => $this->learningObjectives(
                            $skill->name,
                            $stageSlug,
                        ),
                        'difficulty' => $stage['title'],
                        'estimated_minutes' => $stage['estimated_minutes'],
                        'resource_title' => $source->resource_title,
                        'resource_url' => $source->resource_url,
                        'practice_task' => $practiceTask,
                        'quiz_question' => $source->quiz_question,
                        'quiz_options' => $source->quiz_options,
                        'quiz_answer' => $source->quiz_answer,
                        'quiz_explanation' => $source->quiz_explanation,
                    ],
                );

                if (! $core->is_active) {
                    $core->update([
                        'is_active' => true,
                    ]);
                }

                $reinforcement = LearningMaterial::firstOrCreate(
                    [
                        'slug' => $reinforcementSlug,
                    ],
                    [
                        'skill_id' => $skill->id,
                        'material_type' => 'reinforcement',
                        'reinforcement_for_material_id' => $core->id,
                        'is_active' => true,
                        'title' => 'Penguatan '
                            .$stage['title']
                            .': '
                            .$skill->name,
                        'summary' => 'Materi ini membantu kamu memperbaiki bagian yang masih kurang pada tugas '
                            .$stage['title']
                            .' untuk '
                            .$skill->name
                            .'. Fokuskan pengerjaan pada catatan admin sebelum mengirim ulang.',
                        'learning_objectives' => [
                            'Memahami bagian tugas yang masih belum memenuhi kriteria.',
                            'Memperbaiki hasil berdasarkan catatan admin.',
                            'Menguji kembali hasil sebelum dikumpulkan ulang.',
                        ],
                        'difficulty' => $stage['title'],
                        'estimated_minutes' => $stage['reinforcement_minutes'],
                        'resource_title' => 'Materi penguatan SkillPath',
                        'resource_url' => $source->resource_url,
                        'practice_task' => $this->reinforcementTask(
                            $skill->name,
                            $stage['title'],
                            $practiceTask,
                        ),
                        'quiz_question' => $source->quiz_question,
                        'quiz_options' => $source->quiz_options,
                        'quiz_answer' => $source->quiz_answer,
                        'quiz_explanation' => $source->quiz_explanation,
                    ],
                );

                if (! $reinforcement->is_active) {
                    $reinforcement->update([
                        'is_active' => true,
                    ]);
                }

                $canonicalMaterialSlugs[] = $coreSlug;
                $canonicalMaterialSlugs[] = $reinforcementSlug;
            }
        }

        LearningMaterial::query()
            ->whereIn(
                'skill_id',
                $skills->pluck('id')->all(),
            )
            ->whereNotIn(
                'slug',
                $canonicalMaterialSlugs,
            )
            ->update([
                'is_active' => false,
            ]);
    }

    private function stageSummary(
        string $skillName,
        string $stage,
    ): string {
        return match ($stage) {
            'amatir' => 'Tahap Amatir berfokus pada pemahaman dasar '
                .$skillName
                .' dan penerapannya pada kasus yang masih sederhana dan terarah.',

            'menengah' => 'Tahap Menengah melatih penerapan '
                .$skillName
                .' pada kasus yang lebih lengkap, termasuk validasi hasil, variasi kondisi, dan alasan pemilihan pendekatan.',

            'ahli' => 'Tahap Ahli menempatkan '
                .$skillName
                .' pada situasi yang lebih mendekati kondisi nyata dengan batasan, kondisi tidak ideal, pengujian, dan pertimbangan keputusan.',

            default => throw new RuntimeException(
                'Tahap materi tidak dikenali.',
            ),
        };
    }

    /**
     * @return list<string>
     */
    private function learningObjectives(
        string $skillName,
        string $stage,
    ): array {
        return match ($stage) {
            'amatir' => [
                'Memahami konsep dasar '.$skillName.'.',
                'Menerapkan konsep pada satu kasus sederhana.',
                'Menjelaskan langkah pengerjaan dan hasil yang diperoleh.',
            ],

            'menengah' => [
                'Menerapkan '.$skillName.' tanpa bergantung pada contoh langkah demi langkah.',
                'Memeriksa hasil dan menangani kesalahan yang umum terjadi.',
                'Membandingkan alternatif pendekatan dan menjelaskan pilihan yang digunakan.',
            ],

            'ahli' => [
                'Menerapkan '.$skillName.' pada studi kasus dengan beberapa batasan.',
                'Menguji kondisi normal, kondisi batas, dan kondisi yang berpotensi gagal.',
                'Menjelaskan trade-off, kelemahan, serta kemungkinan pengembangan solusi.',
            ],

            default => throw new RuntimeException(
                'Tahap materi tidak dikenali.',
            ),
        };
    }

    private function stageTask(
        string $baseTask,
        string $skillName,
        string $stage,
    ): string {
        return match ($stage) {
            'amatir' => 'Kerjakan tugas dasar '
                .$skillName
                .' berikut. Pada tahap ini, fokus utama adalah memahami alur kerja dengan benar dan menghasilkan bukti pengerjaan yang dapat diperiksa. '
                .$baseTask
                .' Tidak perlu menambah fitur atau ruang lingkup di luar tugas sebelum bagian dasarnya selesai dan dapat dijelaskan dengan baik.',

            'menengah' => 'Pada tahap Menengah, gunakan kemampuan '
                .$skillName
                .' pada kasus yang berbeda dari hasil tahap Amatir. Kerjakan ketentuan inti berikut sebagai dasar: '
                .$baseTask
                .' Setelah bagian utama selesai, tambahkan satu variasi kondisi atau data, lakukan validasi terhadap hasil, lalu jelaskan minimal satu masalah yang kamu temukan dan bagaimana kamu memperbaikinya. Sertakan alasan mengapa pendekatan yang kamu gunakan lebih sesuai dibandingkan alternatif yang paling masuk akal.',

            'ahli' => 'Pada tahap Ahli, kerjakan '
                .$skillName
                .' sebagai studi kasus yang lebih mendekati kondisi nyata. Gunakan ruang lingkup inti berikut sebagai titik awal: '
                .$baseTask
                .' Setelah hasil utama selesai, tambahkan batasan atau kondisi yang tidak ideal, uji minimal tiga skenario termasuk satu kondisi batas, dan dokumentasikan hasil pengujiannya. Jelaskan keputusan penting yang kamu ambil, trade-off yang muncul, kelemahan solusi saat ini, serta satu rekomendasi pengembangan jika pekerjaan ini dilanjutkan.',

            default => throw new RuntimeException(
                'Tahap tugas tidak dikenali.',
            ),
        };
    }

    private function reinforcementTask(
        string $skillName,
        string $stageTitle,
        string $practiceTask,
    ): string {
        return 'Buka kembali hasil tugas '
            .$stageTitle
            .' untuk '
            .$skillName
            .'. Baca catatan admin dan tandai bagian yang belum memenuhi kriteria. Jangan memulai dari nol jika bagian sebelumnya sudah benar. Perbaiki bagian yang bermasalah, jalankan kembali pemeriksaan atau pengujian yang relevan, lalu sertakan catatan singkat mengenai perubahan yang kamu lakukan. Tugas utama yang menjadi acuan adalah: '
            .$practiceTask;
    }
}
