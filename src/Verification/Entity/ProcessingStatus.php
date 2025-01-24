<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum ProcessingStatus: string
{
    case new = 'new';
    case inProgress = 'in_progress';
    case finished = 'finished';
    case failure = 'failure';
}