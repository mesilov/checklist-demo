<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\StructuredOutput;

class VerificationResult
{
    public function __construct(
        public ?string $isAnswerToQuestionPositive = null,
        public ?string $finalConclusionInText = null,
        public ?string $fragmentOfDocumentWithConfirmationQuote = null,
        public ?string $humanReadablePositionOfConfirmationQuoteInDocument = null,
        /**
         * @var ReasoningStep[]
         */
        public array $reasoningSteps = []
    ) {
    }
}