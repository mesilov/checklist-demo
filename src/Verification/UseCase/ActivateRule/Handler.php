<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\ActivateRule;

use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use B24io\Checklist\Verification\UseCase\ActivateRule;

class Handler
{
    public function __construct(
        private RuleRepositoryInterface $ruleRepository,
        private Flusher $flusher
    ) {
    }

    public function handle(ActivateRule\Command $command): void
    {
        $rule = $this->ruleRepository->getById($command->ruleId);

        $rule->markAsActive($command->comment);
        $this->ruleRepository->save($rule);

        $this->flusher->flush();
    }
}