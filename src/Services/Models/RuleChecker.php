<?php

declare(strict_types=1);

namespace B24io\Checklist\Services\Models;


use B24io\Checklist\Verification\Entity\VerificationStep;
use B24io\Checklist\Verification;
use Psr\Log\LoggerInterface;

class RuleChecker
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function run(int $seedNumber, VerificationStep $step): VerificationStep
    {
        $this->logger->debug('RuleChecker.run', [
            'stepId' => $step->getId()->toRfc4122(),
        ]);

        // structured output?
        // continue chat with different queries

        // call params

        // {"temperature": 0.2, "top_p": 0.8, "frequency_penalty": 0.0, "presence_penalty": 0.0}
        // metadata - for debug chats, add verification uuid and verification step uuid
        // see https://platform.openai.com/docs/api-reference/chat


        dd($step->getPrompt());
    }
}

