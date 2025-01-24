<?php

declare(strict_types=1);

namespace B24io\Checklist\Documents\UseCase\AddNewDocument;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Documents\Repository\DocumentRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use Carbon\CarbonImmutable;

class Handler
{
    public function __construct(
        private DocumentRepositoryInterface $documentRepository,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $this->documentRepository->save(
            new Document(
                $command->id,
                $command->clientId,
                $command->documentTypeId,
                new CarbonImmutable(),
                $command->text
            )
        );
        $this->flusher->flush();
    }
}