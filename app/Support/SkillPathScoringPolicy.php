<?php

namespace App\Support;

final class SkillPathScoringPolicy
{
    public const ASSESSMENT_CORRECT_SCORE = 100.0;

    public const ASSESSMENT_INCORRECT_SCORE = 0.0;

    public const EMERGING_LEVEL = 33;

    public const FUNCTIONAL_LEVEL = 66;

    public const DEFAULT_IMPORTANCE_WEIGHT = 1.0;

    public const DEFAULT_PROJECT_WEIGHT = 1.0;

    public const HIGH_GAP_THRESHOLD = self::FUNCTIONAL_LEVEL
        - self::EMERGING_LEVEL;
}
