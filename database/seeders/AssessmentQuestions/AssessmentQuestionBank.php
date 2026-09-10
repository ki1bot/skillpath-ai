<?php

namespace Database\Seeders\AssessmentQuestions;

interface AssessmentQuestionBank
{
    public static function studyProgram(): string;

    /**
     * @return array<string, list<array{0: string, 1: string, 2: string, 3: string, 4: string}>>
     */
    public static function questions(): array;
}
