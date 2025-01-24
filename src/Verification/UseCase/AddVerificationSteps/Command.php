<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddVerificationSteps;

use B24io\Checklist\Verification\Entity\RuleStatus;
use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $clientId,
        public UUid $verificationId
    ) {
    }
}