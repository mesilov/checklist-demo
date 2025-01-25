<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\UseCase\AddVerification;

use B24io\Checklist\Verification\Entity\LanguageModel;
use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $verificationId,
        public Uuid $clientId,
        /**
         * @var Uuid[]
         */
        public array $documentIds,
        public UUid $ruleGroupId,
        public LanguageModel $languageModel
    ) {
    }
}