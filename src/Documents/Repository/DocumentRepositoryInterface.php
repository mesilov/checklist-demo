<?php

declare(strict_types=1);

namespace B24io\Checklist\Documents\Repository;

use B24io\Checklist\Documents\Entity\Document;
use Symfony\Component\Uid\Uuid;

interface DocumentRepositoryInterface
{
    public function getById(Uuid $id): Document;

    public function save(Document $document): void;
}
