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
class Document
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(type: 'uuid', unique: false)]
    private Uuid $clientId;

    #[ORM\Column(type: 'uuid', unique: false)]
    public readonly Uuid $documentTypeId;

    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    public readonly CarbonImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    private CarbonImmutable $updatedAt;

    #[ORM\Column(name: 'status', type: 'string', nullable: false, enumType: DocumentStatus::class)]
    private DocumentStatus $status;

    #[ORM\Column(type: 'text', nullable: false)]
    public readonly string $text;
    #[ORM\Column(type: 'text', nullable: true)]
    private string $verificationSummary;
    #[ORM\Column(type: 'integer', nullable: true)]
    private int $tokenCost;

    public function __construct(
        ?Uuid $id,
        Uuid $clientId,
        Uuid $documentTypeId,
        CarbonImmutable $createdAt,
        CarbonImmutable $updatedAt,
        DocumentStatus $status,
        string $text,
        string $verificationSummary,
        ?int $tokenCost = 0
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->documentTypeId = $documentTypeId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->status = $status;
        $this->text = $text;
        $text->verificationSummary = $verificationSummary;
    }

    public
    function markAsProcessed(
        string $verificationSummary,
        int $tokenCost
    ): void {
        $this->verificationSummary = $verificationSummary;
        $this->tokenCost = $tokenCost;
        $this->updatedAt = new CarbonImmutable();
    }
}