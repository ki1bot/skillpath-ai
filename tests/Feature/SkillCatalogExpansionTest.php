<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
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
                "Jurusan {$career->name} harus memiliki tepat 15 skill.",
            );
        }
    }

    public function test_each_program_has_thirty_assessment_questions_in_question_bank(): void
    {
        $careers = Career::query()
            ->where(
                'is_active',
                true,
            )
            ->with(
                'assessments.questions',
            )
            ->get();

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

            $this->assertCount(
                30,
                $assessment->questions,
                "Bank soal Assesment {$career->name} harus memiliki tepat 30 soal.",
            );

            $this->assertSame(
                15,
                $assessment
                    ->questions
                    ->pluck('skill_id')
                    ->unique()
                    ->count(),
                "Bank soal Assesment {$career->name} harus mencakup seluruh 15 skill jurusan.",
            );

            foreach (
                $assessment
                    ->questions
                    ->groupBy('skill_id')
                as $questions
            ) {
                $this->assertCount(
                    2,
                    $questions,
                    "Setiap skill pada Assesment {$career->name} harus memiliki tepat 2 soal di bank soal.",
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

    public function test_academic_skills_have_assessment_and_learning_materials(): void
    {
        $academicSkills = $this->academicSkills();

        $this->assertCount(
            90,
            $academicSkills,
        );

        foreach ($academicSkills as $skill) {
            $this->assertTrue(
                AssessmentQuestion::query()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->exists(),
                "Skill akademik {$skill->slug} belum memiliki soal Assesment.",
            );

            $this->assertSame(
                2,
                AssessmentQuestion::query()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->count(),
                "Skill akademik {$skill->slug} harus memiliki tepat 2 soal Assesment.",
            );

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
