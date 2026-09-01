<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Support\AcademicAssessmentCatalog;
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

    public function test_seed_data_contains_ninety_skills(): void
    {
        $this->assertSame(
            90,
            Skill::query()->count(),
        );
    }

    public function test_academic_catalog_contains_ninety_skills(): void
    {
        $academicSkills = $this->academicSkills();

        $this->assertCount(
            90,
            $academicSkills,
        );
    }

    public function test_six_active_programs_each_have_fifteen_academic_skills(): void
    {
        $programSlugs = [
            'sistem-informasi',
            'manajemen',
            'teknik-informatika',
            'sistem-komputer',
            'psikologi',
            'ilmu-komunikasi',
        ];

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
            ->get();

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

        $this->assertSame(
            0,
            Career::query()
                ->where(
                    'difficulty',
                    'Legacy',
                )
                ->count(),
        );

        foreach ($careers as $career) {
            $this->assertCount(
                15,
                $career->skills,
                "Jurusan {$career->name} harus memiliki tepat 15 skill akademik keseluruhan.",
            );
        }
    }

    public function test_each_program_has_twenty_seven_assessment_questions_across_nine_core_skills(): void
    {
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
            162,
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
                "Jurusan {$career->name} belum memiliki Assesment.",
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
                "Bank soal Assesment {$career->name} harus memiliki tepat 27 soal.",
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
                "Skill Assesment {$career->name} tidak sesuai dengan 9 skill inti jurusan.",
            );

            foreach (
                $assessment
                    ->questions
                    ->groupBy('skill_id') as $questions
            ) {
                $this->assertCount(
                    AcademicAssessmentCatalog::QUESTIONS_PER_SKILL,
                    $questions,
                    "Setiap skill inti Assesment {$career->name} harus memiliki tepat 3 soal.",
                );
            }
        }
    }

    public function test_learning_catalog_contains_ninety_core_and_reinforcement_materials(): void
    {
        $this->assertSame(
            90,
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
            90,
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

    public function test_all_academic_skills_have_learning_materials(): void
    {
        $academicSkills = $this->academicSkills();

        $this->assertCount(
            90,
            $academicSkills,
        );

        foreach ($academicSkills as $skill) {
            $this->assertTrue(
                $skill
                    ->materials()
                    ->where(
                        'material_type',
                        'core',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->exists(),
                "Skill akademik {$skill->slug} belum memiliki materi utama.",
            );

            $this->assertTrue(
                $skill
                    ->materials()
                    ->where(
                        'material_type',
                        'reinforcement',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->exists(),
                "Skill akademik {$skill->slug} belum memiliki materi penguatan.",
            );
        }
    }

    public function test_academic_programs_have_three_pdf_projects_each(): void
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
                "Jurusan {$career->name} harus memiliki tepat 3 proyek Tugas Akhir.",
            );

            $this->assertEqualsCanonicalizing(
                $expectedProjects[
                    $career->name
                ],
                $career
                    ->projects
                    ->pluck('title')
                    ->all(),
                "Daftar proyek {$career->name} tidak sesuai PDF.",
            );

            foreach (
                $career->projects as $project
            ) {
                $this->assertCount(
                    5,
                    $project->skills,
                    "Proyek {$project->title} harus terhubung ke tepat 5 skill bidangnya.",
                );
            }
        }
    }

    private function academicSkills()
    {
        return Skill::query()
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
    }
}
