<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum LanguageModel: string
{
    case openAiGpt4o = 'openai-gpt-4o';
    case openAiGpt4 = 'openai-gpt-4';
}