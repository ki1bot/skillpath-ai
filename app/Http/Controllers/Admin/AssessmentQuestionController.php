<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssessmentQuestionController extends Controller
{
    public function store(
        Request $request,
    ): RedirectResponse {
        AssessmentQuestion::create(
            $this->validatedData(
                $request,
            ),
        );

        return back()->with(
            'success',
            'Soal Assesment berhasil ditambahkan.',
        );
    }

    public function update(
        Request $request,
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->update(
            $this->validatedData(
                $request,
            ),
        );

        return back()->with(
            'success',
            'Soal Assesment berhasil diperbarui.',
        );
    }

    public function destroy(
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->delete();

        return back()->with(
            'success',
            'Soal Assesment dihapus.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(
        Request $request,
    ): array {
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
                'in:multiple_choice,case,practical',
            ],
            'prompt' => [
                'required',
                'string',
                'max:2000',
            ],
            'practical_instructions' => [
                'nullable',
                'required_if:question_type,practical',
                'string',
                'max:4000',
            ],
            'evidence_required' => [
                'required',
                'boolean',
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
                'string',
                'max:50',
            ],
        ]);

        $options = array_values(
            $data['options'],
        );

        $data['options'] = [
            'A' => $options[0],
            'B' => $options[1],
            'C' => $options[2],
            'D' => $options[3],
        ];

        if (
            $data['question_type']
            !== 'practical'
        ) {
            $data[
                'practical_instructions'
            ] = null;

            $data[
                'evidence_required'
            ] = false;
        }

        return $data;
    }
}
