<?php

namespace App\Support;

final readonly class AiExplanationResult
{
    public function __construct(
        public string $summary,
        public bool $generatedByAi,
        public ?string $model = null,
    ) {}
}
