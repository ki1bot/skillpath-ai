<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Skill;
use App\Support\AcademicAssessmentCatalog;
use RuntimeException;

class AssessmentQuestionBankSynchronizer
{
    public function __construct(
        private readonly AssessmentQuestionAttributesFactory $attributesFactory,
    ) {}

    /**
     * @param  array<string, list<array{0: string, 1: string, 2: string, 3: string, 4: string}>>  $questionsBySkill
     */
    public function sync(
        Assessment $assessment,
        string $studyProgram,
        array $questionsBySkill,
    ): void {
        $expectedSkillSlugs = AcademicAssessmentCatalog::skillSlugs(
            $studyProgram,
        );

        if (
            array_keys($questionsBySkill)
            !== $expectedSkillSlugs
        ) {
            throw new RuntimeException(
                'Definisi bank soal '.$studyProgram.' tidak sesuai dengan katalog Assessment.',
            );
        }

        $skills = Skill::query()
            ->whereIn(
                'slug',
                $expectedSkillSlugs,
            )
            ->get()
            ->keyBy('slug');

        if (
            $skills->count()
            !== AcademicAssessmentCatalog::SKILLS_PER_PROGRAM
        ) {
            throw new RuntimeException(
                'Skill untuk bank soal '.$studyProgram.' belum lengkap.',
            );
        }

        $this->removeObsoleteQuestions(
            $assessment,
            $studyProgram,
            $skills
                ->pluck('id')
                ->map(
                    fn ($id) => (int) $id,
                )
                ->values()
                ->all(),
        );

        foreach ($questionsBySkill as $skillSlug => $definitions) {
            /** @var Skill|null $skill */
            $skill = $skills->get(
                $skillSlug,
            );

            if (! $skill) {
                throw new RuntimeException(
                    'Skill '.$skillSlug.' tidak ditemukan.',
                );
            }

            $this->syncSkillQuestions(
                $assessment,
                $studyProgram,
                $skill,
                $skillSlug,
                $definitions,
            );
        }

        $questionCount = $assessment
            ->questions()
            ->count();

        if (
            $questionCount
            !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
        ) {
            throw new RuntimeException(
                'Bank soal '.$studyProgram.' harus memiliki tepat '
                    .AcademicAssessmentCatalog::QUESTION_POOL_SIZE
                    .' soal, tetapi ditemukan '
                    .$questionCount
                    .'.',
            );
        }
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string, 3: string, 4: string}>  $definitions
     */
    private function syncSkillQuestions(
        Assessment $assessment,
        string $studyProgram,
        Skill $skill,
        string $skillSlug,
        array $definitions,
    ): void {
        $expectedCount = AcademicAssessmentCatalog::questionCapacityForSkill(
            $studyProgram,
            $skillSlug,
        );

        if (count($definitions) !== $expectedCount) {
            throw new RuntimeException(
                'Bank soal '.$skillSlug.' harus memiliki tepat '.$expectedCount.' soal.',
            );
        }

        $existingQuestions = $assessment
            ->questions()
            ->where(
                'skill_id',
                $skill->id,
            )
            ->orderBy('id')
            ->get();

        foreach ($definitions as $index => $definition) {
            $attributes = $this->attributesFactory->make(
                $skill,
                $skillSlug,
                $definition,
            );

            /** @var AssessmentQuestion|null $existingQuestion */
            $existingQuestion = $existingQuestions->get(
                $index,
            );

            if ($existingQuestion) {
                $existingQuestion->update(
                    $attributes,
                );

                continue;
            }

            $assessment
                ->questions()
                ->create(
                    $attributes,
                );
        }

        $extraQuestionIds = $existingQuestions
            ->slice(
                count($definitions),
            )
            ->pluck('id')
            ->map(
                fn ($id) => (int) $id,
            )
            ->values()
            ->all();

        $this->deleteUnusedQuestions(
            $extraQuestionIds,
            $studyProgram,
        );
    }

    /**
     * @param  array<int, int>  $targetSkillIds
     */
    private function removeObsoleteQuestions(
        Assessment $assessment,
        string $studyProgram,
        array $targetSkillIds,
    ): void {
        $questionIds = $assessment
            ->questions()
            ->whereNotIn(
                'skill_id',
                $targetSkillIds,
            )
            ->pluck('id')
            ->map(
                fn ($id) => (int) $id,
            )
            ->values()
            ->all();

        $this->deleteUnusedQuestions(
            $questionIds,
            $studyProgram,
        );
    }

    /**
     * @param  array<int, int>  $questionIds
     */
    private function deleteUnusedQuestions(
        array $questionIds,
        string $studyProgram,
    ): void {
        if ($questionIds === []) {
            return;
        }

        $hasHistoricalResults = AssessmentResult::query()
            ->whereIn(
                'assessment_question_id',
                $questionIds,
            )
            ->exists();

        if ($hasHistoricalResults) {
            throw new RuntimeException(
                'Bank soal '.$studyProgram.' memiliki soal lama yang masih digunakan oleh histori Assessment. Soal tersebut tidak dihapus agar histori pengguna tidak rusak.',
            );
        }

        AssessmentQuestion::query()
            ->whereIn(
                'id',
                $questionIds,
            )
            ->delete();
    }
}
