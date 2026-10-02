<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Career;
use App\Services\Assessment\AssessmentQuestionBankSynchronizer;
use App\Support\AcademicProgramCatalog;
use Database\Seeders\AssessmentQuestions\AssessmentQuestionBank;
use Database\Seeders\AssessmentQuestions\IlmuKomunikasiQuestionBank;
use Database\Seeders\AssessmentQuestions\ManajemenQuestionBank;
use Database\Seeders\AssessmentQuestions\PsikologiQuestionBank;
use Database\Seeders\AssessmentQuestions\SistemInformasiQuestionBank;
use Database\Seeders\AssessmentQuestions\SistemKomputerQuestionBank;
use Database\Seeders\AssessmentQuestions\TeknikInformatikaQuestionBank;
use Illuminate\Database\Seeder;

class AcademicAssessmentQuestionPoolSeeder extends Seeder
{
    /**
     * @var list<class-string<AssessmentQuestionBank>>
     */
    private const QUESTION_BANKS = [
        SistemInformasiQuestionBank::class,
        ManajemenQuestionBank::class,
        TeknikInformatikaQuestionBank::class,
        SistemKomputerQuestionBank::class,
        PsikologiQuestionBank::class,
        IlmuKomunikasiQuestionBank::class,
    ];

    public function run(
        AssessmentQuestionBankSynchronizer $synchronizer,
    ): void {
        foreach (self::QUESTION_BANKS as $questionBank) {
            $studyProgram = $questionBank::studyProgram();
            $program = AcademicProgramCatalog::program(
                $studyProgram,
            );

            if ($program === null) {
                continue;
            }

            $career = Career::query()
                ->where(
                    'slug',
                    $program['slug'],
                )
                ->where(
                    'name',
                    $studyProgram,
                )
                ->where(
                    'is_active',
                    true,
                )
                ->first();

            if (! $career) {
                continue;
            }

            $assessment = Assessment::query()
                ->where(
                    'career_id',
                    $career->id,
                )
                ->where(
                    'study_program',
                    $studyProgram,
                )
                ->where(
                    'is_active',
                    true,
                )
                ->first();

            if (! $assessment) {
                continue;
            }

            $synchronizer->sync(
                $assessment,
                $studyProgram,
                $questionBank::questions(),
            );
        }
    }
}
