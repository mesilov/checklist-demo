<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum VerificationStepStatus: string
{
    case fail = 'fail';
    case pass = 'pass';
    case unknown = 'unknown';
}