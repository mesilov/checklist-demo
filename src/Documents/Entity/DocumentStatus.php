<?php

declare(strict_types=1);

namespace B24io\Checklist\Documents\Entity;

enum DocumentStatus: string
{
    case new = 'new';
    case inProgress = 'in_progress';
    case done = 'done';
    case failure = 'failure';
}