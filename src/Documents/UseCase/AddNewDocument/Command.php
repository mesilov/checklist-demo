<?php

declare(strict_types=1);

namespace B24io\Checklist\Documents\UseCase\AddNewDocument;

use Symfony\Component\Uid\Uuid;

readonly class Command
{
    public function __construct(
        public Uuid $id,
        public Uuid $clientId,
        public Uuid $documentTypeId,
        public string $text,
    ) {
    }
}
