<?php

namespace App\Support;

final readonly class AiCompletionResult
{
    public function __construct(
        public string $content,
        public string $model,
    ) {}
}
