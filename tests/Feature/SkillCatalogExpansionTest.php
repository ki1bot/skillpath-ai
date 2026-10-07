<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Support\AcademicAssessmentCatalog;
use App\Support\AcademicProgramCatalog;
use Database\Seeders\AcademicAssessmentQuestionPoolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillCatalogExpansionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_seed_data_contains_fifty_four_skills(): void
    {
        $this->assertSame(
            54,
            Skill::query()->count(),
        );
    }

    public function test_academic_catalog_contains_fifty_four_skills(): void
    {
        $this->assertCount(
            54,
            AcademicProgramCatalog::allSkillSlugs(),
        );

        $this->assertCount(
            54,
            $this->academicSkills(),
        );
    }

    public function test_six_active_programs_each_have_nine_academic_skills(): void
    {
        $programSlugs = collect(
            AcademicProgramCatalog::programs(),
        )
            ->pluck('slug')
            ->values()
            ->all();

        $careers = Career::query()
            ->whereIn(
                'slug',
                $programSlugs,
            )
            ->where(
                'is_active',
                true,
            )
            ->with('skills')
            ->get()
            ->keyBy('name');

        $this->assertCount(
            6,
            $careers,
        );

        $this->assertSame(
            6,
            Career::query()
                ->where(
                    'is_active',
                    true,
                )
                ->count(),
        );

        foreach (
            AcademicProgramCatalog::programs() as $studyProgram => $program
        ) {
            $career = $careers->get(
                $studyProgram,
            );

            $this->assertNotNull(
                $career,
                "Jurusan {$studyProgram} belum tersedia.",
            );

            $this->assertCount(
                AcademicAssessmentCatalog::SKILLS_PER_PROGRAM,
                $career->skills,
                "Jurusan {$studyProgram} harus memiliki tepat 9 skill akademik.",
            );

            $this->assertEqualsCanonicalizing(
                AcademicProgramCatalog::skillSlugs(
                    $studyProgram,
                ),
                $career
                    ->skills
                    ->pluck('slug')
                    ->all(),
                "Skill jurusan {$studyProgram} tidak sesuai katalog akademik.",
            );

            $expectedNames = collect(
                $program['areas'],
            )
                ->flatMap(
                    fn (array $area) => collect(
                        $area['skills'],
                    )->pluck('name'),
                )
                ->values()
                ->all();

            $this->assertEqualsCanonicalizing(
                $expectedNames,
                $career
                    ->skills
                    ->pluck('name')
                    ->all(),
                "Nama skill jurusan {$studyProgram} tidak sesuai struktur jurusan.",
            );
        }
    }

    public function test_each_program_has_fifty_assessment_questions_across_nine_skills(): void
    {
        $this->assertSame(
            50,
            AcademicAssessmentCatalog::QUESTION_POOL_SIZE,
        );

        $this->assertSame(
            50,
            AcademicAssessmentCatalog::QUESTION_LIMIT,
        );

        $careers = Career::query()
            ->where(
                'is_active',
                true,
            )
            ->with(
                'assessments.questions.skill',
            )
            ->get();

        $this->assertSame(
            300,
            AssessmentQuestion::query()
                ->count(),
        );

        foreach ($careers as $career) {
            $assessment = $career
                ->assessments
                ->firstWhere(
                    'study_program',
                    $career->name,
                );

            $this->assertNotNull(
                $assessment,
                "Jurusan {$career->name} belum memiliki Assessment.",
            );

            $expectedSkillSlugs = AcademicAssessmentCatalog::skillSlugs(
                $career->name,
            );

            $this->assertCount(
                AcademicAssessmentCatalog::SKILLS_PER_PROGRAM,
                $expectedSkillSlugs,
            );

            $this->assertCount(
                AcademicAssessmentCatalog::QUESTION_POOL_SIZE,
                $assessment->questions,
                "Bank soal Assessment {$career->name} harus memiliki tepat 50 soal.",
            );

            $actualSkillSlugs = $assessment
                ->questions
                ->pluck('skill.slug')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $this->assertEqualsCanonicalizing(
                $expectedSkillSlugs,
                $actualSkillSlugs,
                "Skill Assessment {$career->name} tidak sesuai dengan 9 skill jurusan.",
            );

            $distribution = $assessment
                ->questions
                ->groupBy('skill_id')
                ->map(
                    fn ($questions) => $questions->count(),
                )
                ->sort()
                ->values()
                ->all();

            $this->assertSame(
                [
                    5,
                    5,
                    5,
                    5,
                    6,
                    6,
                    6,
                    6,
                    6,
                ],
                $distribution,
                "Distribusi bank soal {$career->name} harus terdiri dari empat skill dengan 5 soal dan lima skill dengan 6 soal.",
            );
        }
    }

    public function test_question_pool_seeder_does_not_attach_questions_to_a_wrong_program_career_pair(): void
    {
        $teknikInformatika = Career::query()
            ->where(
                'name',
                'Teknik Informatika',
            )
            ->firstOrFail();

        $psikologi = Career::query()
            ->where(
                'name',
                'Psikologi',
            )
            ->firstOrFail();

        Assessment::query()
            ->where(
                'career_id',
                $teknikInformatika->id,
            )
            ->where(
                'study_program',
                'Teknik Informatika',
            )
            ->delete();

        $wrongAssessment = Assessment::query()
            ->create([
                'career_id' => $psikologi->id,
                'study_program' => 'Teknik Informatika',
                'title' => 'Assessment relasi salah',
                'description' => 'Data pengujian relasi assessment.',
                'duration_minutes' => 50,
                'is_active' => true,
            ]);

        $this->seed(
            AcademicAssessmentQuestionPoolSeeder::class,
        );

        $this->assertSame(
            0,
            $wrongAssessment
                ->questions()
                ->count(),
        );
    }

    public function test_learning_catalog_contains_three_stage_materials_and_reinforcements(): void
    {
        $this->assertSame(
            162,
            LearningMaterial::query()
                ->where(
                    'material_type',
                    'core',
                )
                ->where(
                    'is_active',
                    true,
                )
                ->count(),
        );

        $this->assertSame(
            162,
            LearningMaterial::query()
                ->where(
                    'material_type',
                    'reinforcement',
                )
                ->where(
                    'is_active',
                    true,
                )
                ->count(),
        );
    }

    public function test_all_academic_skills_have_amatir_menengah_and_ahli_materials(): void
    {
        $academicSkills = $this->academicSkills();

        $this->assertCount(
            54,
            $academicSkills,
        );

        $expectedStages = [
            'Amatir',
            'Menengah',
            'Ahli',
        ];

        foreach ($academicSkills as $skill) {
            $coreMaterials = $skill
                ->materials()
                ->where(
                    'material_type',
                    'core',
                )
                ->where(
                    'is_active',
                    true,
                )
                ->get();

            $reinforcementMaterials = $skill
                ->materials()
                ->where(
                    'material_type',
                    'reinforcement',
                )
                ->where(
                    'is_active',
                    true,
                )
                ->get();

            $this->assertCount(
                3,
                $coreMaterials,
                "Skill {$skill->slug} harus memiliki 3 materi utama.",
            );

            $this->assertCount(
                3,
                $reinforcementMaterials,
                "Skill {$skill->slug} harus memiliki 3 materi penguatan.",
            );

            $this->assertEqualsCanonicalizing(
                $expectedStages,
                $coreMaterials
                    ->pluck(
                        'difficulty',
                    )
                    ->all(),
                "Materi utama {$skill->slug} harus terdiri dari Amatir, Menengah, dan Ahli.",
            );

            $this->assertEqualsCanonicalizing(
                $expectedStages,
                $reinforcementMaterials
                    ->pluck(
                        'difficulty',
                    )
                    ->all(),
                "Materi penguatan {$skill->slug} harus terdiri dari Amatir, Menengah, dan Ahli.",
            );

            $this->assertCount(
                3,
                $coreMaterials
                    ->pluck(
                        'practice_task',
                    )
                    ->unique()
                    ->values(),
                "Tugas ketiga tahap {$skill->slug} harus berbeda.",
            );
        }
    }

    public function test_academic_programs_have_three_projects_each_and_each_project_matches_one_area(): void
    {
        $expectedProjects = [
            'Sistem Informasi' => [
                'Sales & Business Intelligence Dashboard',
                'Build Mini Information System',
                'Redesign Digital Product',
            ],
            'Manajemen' => [
                'Digital Marketing Campaign',
                'Financial Health Analysis',
                'Recruitment Strategy',
            ],
            'Teknik Informatika' => [
                'Software Development Project',
                'Company Network & Security Simulation',
                'AI Predictive Project',
            ],
            'Sistem Komputer' => [
                'Mini Computer Architecture Design',
                'Smart IoT System',
                'Secure Network Design',
            ],
            'Psikologi' => [
                'Employee & Organizational Assessment',
                'Counseling Case Simulation',
                'Mini Psychological Research',
            ],
            'Ilmu Komunikasi' => [
                'Crisis Communication Simulation',
                'News Reporting Project',
                'Digital Content Campaign',
            ],
        ];

        $careers = Career::query()
            ->where(
                'is_active',
                true,
            )
            ->with(
                'projects.skills',
            )
            ->get();

        $this->assertSame(
            18,
            PortfolioProject::query()
                ->count(),
        );

        foreach ($careers as $career) {
            $this->assertCount(
                3,
                $career->projects,
                "Jurusan {$career->name} harus memiliki tepat 3 proyek.",
            );

            $this->assertEqualsCanonicalizing(
                $expectedProjects[
                    $career->name
                ],
                $career
                    ->projects
                    ->pluck('title')
                    ->all(),
                "Daftar proyek {$career->name} tidak sesuai katalog proyek.",
            );

            $program = AcademicProgramCatalog::program(
                $career->name,
            );

            $this->assertNotNull(
                $program,
            );

            $expectedAreaSkillSets = collect(
                $program[
                    'areas'
                ],
            )
                ->map(
                    fn (array $area) => collect(
                        $area[
                            'skills'
                        ],
                    )
                        ->pluck(
                            'slug',
                        )
                        ->sort()
                        ->values()
                        ->implode('|'),
                )
                ->sort()
                ->values()
                ->all();

            $actualProjectSkillSets = $career
                ->projects
                ->map(
                    fn ($project) => $project
                        ->skills
                        ->pluck(
                            'slug',
                        )
                        ->sort()
                        ->values()
                        ->implode('|'),
                )
                ->sort()
                ->values()
                ->all();

            $this->assertSame(
                $expectedAreaSkillSets,
                $actualProjectSkillSets,
                "Setiap proyek {$career->name} harus mewakili tepat satu dari tiga bidang jurusan.",
            );

            foreach ($career->projects as $project) {
                $this->assertCount(
                    3,
                    $project->skills,
                    "Proyek {$project->title} harus terhubung ke tepat 3 skill bidangnya.",
                );
            }

            $this->assertCount(
                3,
                $career
                    ->projects
                    ->pluck(
                        'problem_statement',
                    )
                    ->unique()
                    ->values(),
                "Tiga proyek {$career->name} harus mempunyai tugas yang berbeda.",
            );
        }
    }

    private function academicSkills()
    {
        return Skill::query()
            ->whereIn(
                'slug',
                AcademicProgramCatalog::allSkillSlugs(),
            )
            ->get();
    }
}
