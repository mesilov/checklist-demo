<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use B24io\Checklist\Verification\Infrastructure\Doctrine\VerificationStepRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: VerificationStepRepository::class)]
class VerificationStep
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    public readonly CarbonImmutable $createdAt;
    #[ORM\Column(name: 'processed_at', type: 'carbon_immutable', precision: 4, nullable: true)]
    #[Ignore]
    private ?CarbonImmutable $processedAt;
    #[ORM\Column(type: 'uuid', unique: false)]
    private Uuid $clientId;
    #[ORM\Column(type: 'uuid', unique: false)]
    private Uuid $documentId;
    #[ORM\Column(type: 'uuid', unique: false)]
    private Uuid $ruleId;
    #[ORM\Column(name: 'status', type: 'string', nullable: false, enumType: VerificationStatus::class)]
    private VerificationStatus $status;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $output;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $tokenCost;

    /**
     * @param Uuid|null $id
     * @param CarbonImmutable $createdAt
     * @param Uuid $clientId
     * @param Uuid $documentId
     * @param Uuid $ruleId
     * @param VerificationStatus $status
     * @param string|null $output
     * @param int|null $tokenCost
     */
    public function __construct(
        ?Uuid $id,
        CarbonImmutable $createdAt,
        Uuid $clientId,
        Uuid $documentId,
        Uuid $ruleId,
        VerificationStatus $status,
        ?string $output,
        ?int $tokenCost
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->clientId = $clientId;
        $this->documentId = $documentId;
        $this->ruleId = $ruleId;
        $this->status = $status;
        $this->output = $output;
        $this->tokenCost = $tokenCost;
    }

    public function markAsProcessed(VerificationStatus $status, string $output, int $tokenCost): void
    {
        $this->output = $output;
        $this->status = $status;
        $this->tokenCost = $tokenCost;
        $this->processedAt = CarbonImmutable::now();
    }
}

