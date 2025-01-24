<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\UpdateRule;

use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $uuid,
        public string $name,
        public string $rule,
        public string $prompt,
        public int $weight,
        public ?string $comment
    ) {
    }
}