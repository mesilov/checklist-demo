<?php

declare(strict_types=1);

namespace B24io\Checklist\Services\Prompts;

class PromptBuilder
{
    public function build(array $documents, string $promptTemplate): string
    {
        return $promptTemplate;
    }
}