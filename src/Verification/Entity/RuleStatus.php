<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum RuleStatus: string
{
    case draft = 'draft';
    case active = 'active';
    case disabled = 'disabled';
    case deleted = 'deleted';
}