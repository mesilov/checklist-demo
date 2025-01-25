<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddVerification;

use B24io\Checklist\Documents\Repository\DocumentRepositoryInterface;
use B24io\Checklist\Services\Prompts\PromptBuilder;
use B24io\Checklist\Verification\Entity\ProcessingStatus;
use B24io\Checklist\Verification\Entity\Verification;
use B24io\Checklist\Verification\Entity\VerificationStep;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use B24io\Checklist\Verification\Repository\VerificationStepRepositoryInterface;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

readonly class Handler
{
    public function __construct(
        private PromptBuilder $promptBuilder,
        private DocumentRepositoryInterface $documentRepository,
        private RuleRepositoryInterface $ruleRepository,
        private VerificationRepositoryInterface $verificationRepository,
        private VerificationStepRepositoryInterface $verificationStepRepository,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        // save verification attempt
        $this->verificationRepository->save(
            new Verification(
                $command->id,
                $command->clientId,
                new CarbonImmutable(),
                null,
                $command->documentIds,
                $command->ruleGroupId,
                ProcessingStatus::new,
                $command->languageModel
            )
        );

        // get relevant rules list for this verification attempt
        $rules = $this->ruleRepository->getByRuleGroupId($command->ruleGroupId);

        // load documents
        $documents = [];
        foreach ($command->documentIds as $documentId) {
            $documents[] = $this->documentRepository->getById($documentId);
        }

        foreach ($rules as $rule) {
            $prompt = $rule->getPrompt();
            $finalPrompt = $this->promptBuilder->build($documents, $prompt);
            $this->verificationStepRepository->save(
                new VerificationStep(
                    Uuid::v7(),
                    new CarbonImmutable(),
                    null,
                    $command->clientId,
                    $command->id,
                    $rule->getId(),
                    ProcessingStatus::new,
                    $finalPrompt
                )
            );
        }

        $this->flusher->flush();
        //todo add tasks to queue
    }
}