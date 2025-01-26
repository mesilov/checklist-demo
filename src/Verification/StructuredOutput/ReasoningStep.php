<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\StructuredOutput;

readonly class ReasoningStep
{
    public function __construct(
        public ?string $explanation,
        public ?string $output,
    ) {
    }
}