<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\DeactivateRule;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Documents\Entity\DocumentStatus;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use B24io\Checklist\Verification\UseCase\AddRule\Command;
use Carbon\CarbonImmutable;

class Handler
{
    public function __construct(
        private RuleRepositoryInterface $ruleRepository,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $rule = $this->ruleRepository->getById($command->uuid);

        $rule->markAsDisabled($command->comment);
        $this->ruleRepository->save($rule);

        $this->flusher->flush();
    }
}