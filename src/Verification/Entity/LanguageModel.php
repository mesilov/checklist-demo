<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum LanguageModel: string
{
    case gpt4oMini20240718 = 'gpt-4o-mini-2024-07-18';
    case gpt4o20240806 = 'gpt-4o-2024-08-06';
}