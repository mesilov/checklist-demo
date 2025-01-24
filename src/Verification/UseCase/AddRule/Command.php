<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddRule;

use B24io\Checklist\Verification\Entity\RuleStatus;
use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $id,
        public Uuid $clientId,
        public UUid $groupId,
        /**
         * @var Uuid[]
         */
        public array $documentTypeIds,
        public RuleStatus $status,
        public string $name,
        public string $rule,
        public string $prompt,
        public int $weight,
        public ?string $comment
    ) {
    }
}