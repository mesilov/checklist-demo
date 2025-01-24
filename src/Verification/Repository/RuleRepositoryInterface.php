<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Repository;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Verification\Entity\Rule;
use Symfony\Component\Uid\Uuid;

interface RuleRepositoryInterface
{
    public function getById(Uuid $id): Rule;
    public function save(Rule $rule):void;
}
