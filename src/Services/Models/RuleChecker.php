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

    public function run(VerificationStep $step): VerificationStep
    {
        $this->logger->debug('RuleChecker.run', [
            'stepId' => $step->getId()->toRfc4122(),
        ]);

        dd($step->getPrompt());


    }
}

