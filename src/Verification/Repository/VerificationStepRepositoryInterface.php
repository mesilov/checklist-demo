<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Repository;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Verification\Entity\VerificationStep;
use Symfony\Component\Uid\Uuid;

interface VerificationStepRepositoryInterface
{
    public function getById(Uuid $id): VerificationStep;

    public function save(VerificationStep $verificationStep): void;
}
