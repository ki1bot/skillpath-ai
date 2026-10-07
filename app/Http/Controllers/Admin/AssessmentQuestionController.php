<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Support\AcademicProgramCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AssessmentQuestionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $assessment = Assessment::query()
            ->with('career')
            ->whereKey($data['assessment_id'])
            ->firstOrFail();

        if ($this->isAcademicAssessment($assessment)) {
            throw ValidationException::withMessages([
                'assessment_id' => 'Assessment akademik sudah mempunyai 50 soal. Perbarui soal yang tersedia tanpa menambah jumlahnya.',
            ]);
        }

        AssessmentQuestion::create($data);

        return back()->with(
            'success',
            'Soal Assessment berhasil ditambahkan.',
        );
    }

    public function update(
        Request $request,
        AssessmentQuestion $question,
    ): RedirectResponse {
        $data = $this->validatedData($request);

        $question->loadMissing('assessment.career');

        $originalAssessment = $question->assessment;

        $targetAssessment = Assessment::query()
            ->with('career')
            ->whereKey($data['assessment_id'])
            ->firstOrFail();

        if (
            $this->isAcademicAssessment($originalAssessment)
            || $this->isAcademicAssessment($targetAssessment)
        ) {
            if (
                (int) $data['assessment_id']
                    !== (int) $question->assessment_id
                || (int) $data['skill_id']
                    !== (int) $question->skill_id
            ) {
                throw ValidationException::withMessages([
                    'assessment_id' => 'Soal akademik tidak dapat dipindahkan ke Assessment atau kemampuan lain.',
                ]);
            }
        }

        $question->update($data);

        return back()->with(
            'success',
            'Soal Assessment berhasil diperbarui.',
        );
    }

    public function destroy(
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->loadMissing('assessment.career');

        if ($this->isAcademicAssessment($question->assessment)) {
            throw ValidationException::withMessages([
                'question' => 'Soal akademik tidak dapat dihapus. Setiap jurusan harus mempunyai 50 soal.',
            ]);
        }

        $hasResults = AssessmentResult::query()
            ->where(
                'assessment_question_id',
                $question->id,
            )
            ->exists();

        if ($hasResults) {
            throw ValidationException::withMessages([
                'question' => 'Soal ini sudah mempunyai riwayat jawaban mahasiswa dan tidak dapat dihapus.',
            ]);
        }

        $question->delete();

        return back()->with(
            'success',
            'Soal Assessment berhasil dihapus.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'assessment_id' => [
                'required',
                'integer',
                'exists:assessments,id',
            ],
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],
            'question_type' => [
                'required',
                'in:multiple_choice',
            ],
            'prompt' => [
                'required',
                'string',
                'max:2000',
            ],
            'options' => [
                'required',
                'array',
                'size:4',
            ],
            'options.*' => [
                'required',
                'string',
                'max:500',
            ],
            'correct_answer' => [
                'required',
                'in:A,B,C,D',
            ],
            'explanation' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'difficulty' => [
                'required',
                'in:Amatir,Menengah,Ahli',
            ],
            'evidence_required' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $options = array_values($data['options']);

        $data['options'] = [
            'A' => $options[0],
            'B' => $options[1],
            'C' => $options[2],
            'D' => $options[3],
        ];

        $data['question_type'] = 'multiple_choice';
        $data['practical_instructions'] = null;
        $data['evidence_required'] = false;

        return $data;
    }

    private function isAcademicAssessment(
        Assessment $assessment,
    ): bool {
        $studyProgram = (string) (
            $assessment->study_program ?? ''
        );

        if (
            AcademicProgramCatalog::program(
                $studyProgram,
            ) === null
        ) {
            return false;
        }

        $assessment->loadMissing('career');

        return $assessment->career->name === $studyProgram;
    }
}
