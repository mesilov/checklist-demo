<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Repository;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\Verification;
use Symfony\Component\Uid\Uuid;

interface VerificationRepositoryInterface
{
    public function getById(Uuid $id): Verification;
    public function save(Verification $verification):void;
}
