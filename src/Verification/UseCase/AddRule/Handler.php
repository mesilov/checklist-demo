<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddRule;

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
        $this->ruleRepository->save(
            new Rule(
                $command->id,
                $command->clientId,
                $command->groupId,
                $command->documentTypeIds,
                new CarbonImmutable(),
                new CarbonImmutable(),
                $command->status,
                $command->name,
                $command->rule,
                $command->prompt,
                $command->expectedResult,
                $command->weight,
                $command->comment
            )
        );
        $this->flusher->flush();
    }
}