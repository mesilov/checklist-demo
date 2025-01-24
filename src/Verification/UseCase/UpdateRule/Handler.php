<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\UpdateRule;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Documents\Entity\DocumentStatus;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Services\Flusher;
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

        $rule->changeName($command->name);
        $rule->changeRule($command->rule);
        $rule->changePrompt($command->prompt);
        $rule->changeWeight($command->weight);
        $rule->changeComment($command->comment);

        $this->flusher->flush();
    }
}