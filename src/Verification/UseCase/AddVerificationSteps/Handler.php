<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddVerificationSteps;

use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Entity\ProcessingStatus;
use B24io\Checklist\Verification\Entity\VerificationStep;
use B24io\Checklist\Verification\Entity\VerificationStepStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use B24io\Checklist\Verification\Repository\VerificationStepRepositoryInterface;
use B24io\Checklist\Verification\UseCase\AddVerificationSteps\Command;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

class Handler
{
    public function __construct(
        private RuleRepositoryInterface $ruleRepository,
        private VerificationRepositoryInterface $verificationRepository,
        private VerificationStepRepositoryInterface $verificationStepRepository,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        // get verification attempt
        $verification = $this->verificationRepository->getById($command->verificationId);
        // get relevant rules list for this verification attempt
        $rules = $this->ruleRepository->getByRuleGroupId($verification->getRuleGroupId());

        foreach ($rules as $rule) {
            // todo
            // build prompt
            // prompt template + document
            $prompt = $rule->getPrompt();

            $this->verificationStepRepository->save(
                new VerificationStep(
                    Uuid::v7(),
                    new CarbonImmutable(),
                    null,
                    $command->clientId,
                    $command->verificationId,
                    $rule->getId(),
                    ProcessingStatus::new,
                    $prompt
                )
            );
        }
        $this->flusher->flush();
        // add tasks to queue
    }
}