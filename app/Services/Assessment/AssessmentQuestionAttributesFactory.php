<?php

namespace App\Services\Assessment;

use App\Models\Skill;

class AssessmentQuestionAttributesFactory
{
    /**
     * @param  array{0: string, 1: string, 2: string, 3: string, 4: string}  $definition
     * @return array<string, mixed>
     */
    public function make(
        Skill $skill,
        string $skillSlug,
        array $definition,
    ): array {
        [$prompt, $correctOption, $wrongOne, $wrongTwo, $wrongThree] = $definition;

        $options = [
            $correctOption,
            $wrongOne,
            $wrongTwo,
            $wrongThree,
        ];

        $shift = abs(
            crc32(
                $skillSlug
                    .'|'
                    .$prompt,
            ),
        ) % 4;

        return [
            'skill_id' => $skill->id,
            'question_type' => 'multiple_choice',
            'prompt' => $prompt,
            'practical_instructions' => null,
            'evidence_required' => false,
            'options' => [
                'A' => $this->optionForPosition(
                    $options,
                    $shift,
                ),
                'B' => $this->optionForPosition(
                    $options,
                    $shift + 1,
                ),
                'C' => $this->optionForPosition(
                    $options,
                    $shift + 2,
                ),
                'D' => $this->optionForPosition(
                    $options,
                    $shift + 3,
                ),
            ],
            'correct_answer' => $this->answerForIndex(
                (4 - $shift) % 4,
            ),
            'explanation' => 'Jawaban dinilai berdasarkan pemahaman konsep pada skill '.$skill->name.'.',
            'difficulty' => $skill->difficulty,
        ];
    }

    /**
     * @param  array{0: string, 1: string, 2: string, 3: string}  $options
     */
    private function optionForPosition(
        array $options,
        int $position,
    ): string {
        return $options[
            $position % 4
        ];
    }

    private function answerForIndex(
        int $index,
    ): string {
        return match ($index) {
            1 => 'B',
            2 => 'C',
            3 => 'D',
            default => 'A',
        };
    }
}
