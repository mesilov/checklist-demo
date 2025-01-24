<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddRule;

use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $uuid,
        /**
         * @var Uuid[]
         */
        public array $documentTypeIds,
        public string $name,
        public string $rule,
        public string $prompt,
        public int $weight,
        public ?string $comment
    ) {
    }
}