<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\ActivateRule;

use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $ruleId,
        public string $comment
    ) {
    }
}