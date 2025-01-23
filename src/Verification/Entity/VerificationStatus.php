<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum VerificationStatus: string
{
    case draft = 'pass';
    case active = 'fail';
}