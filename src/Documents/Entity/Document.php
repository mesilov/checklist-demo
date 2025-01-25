<?php

declare(strict_types=1);

namespace B24io\Checklist\Documents\Entity;

use B24io\Checklist\Documents\Infrastructure\Doctrine\DocumentRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: 'documents')]
class Document
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    private Uuid $id;
    #[ORM\Column(name: 'client_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $clientId;
    #[ORM\Column(name: 'document_type_id', type: 'uuid', unique: false, nullable: false)]
    private readonly Uuid $documentTypeId;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    private readonly CarbonImmutable $createdAt;
    #[ORM\Column(name: 'text', type: 'text', nullable: false)]
    private readonly string $text;

    public function __construct(
        Uuid $id,
        Uuid $clientId,
        Uuid $documentTypeId,
        CarbonImmutable $createdAt,
        string $text,
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->documentTypeId = $documentTypeId;
        $this->createdAt = $createdAt;
        $this->text = $text;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDocumentTypeId(): Uuid
    {
        return $this->documentTypeId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}