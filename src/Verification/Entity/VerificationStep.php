<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use B24io\Checklist\Verification\Infrastructure\Doctrine\VerificationStepRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: VerificationStepRepository::class)]
#[ORM\Table(name: 'verification_steps')]
class VerificationStep
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    private Uuid $id;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    public readonly CarbonImmutable $createdAt;
    #[ORM\Column(name: 'processed_at', type: 'carbon_immutable', precision: 4, nullable: true)]
    #[Ignore]
    private ?CarbonImmutable $processedAt;
    #[ORM\Column(name: 'client_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $clientId;
    #[ORM\Column(name: 'verification_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $verificationId;
    #[ORM\Column(name: 'rule_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $ruleId;
    #[ORM\Column(name: 'processing_status', type: 'string', nullable: false, enumType: ProcessingStatus::class)]
    private ProcessingStatus $processingStatus;
    #[ORM\Column(name: 'step_status', type: 'string', nullable: true, enumType: VerificationStepStatus::class)]
    private ?VerificationStepStatus $stepStatus;
    #[ORM\Column(name: 'prompt', type: 'text', nullable: false)]
    private string $prompt;
    #[ORM\Column(name: 'reasoning', type: 'text', nullable: true)]
    private ?string $reasoning;
    #[ORM\Column(name: 'output', type: 'text', nullable: true)]
    private ?string $output;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $tokenCost;

    /**
     * @param Uuid $id
     * @param CarbonImmutable $createdAt
     * @param CarbonImmutable|null $processedAt
     * @param Uuid $clientId
     * @param Uuid $verificationId
     * @param Uuid $ruleId
     * @param ProcessingStatus $stepStatus
     * @param ?VerificationStepStatus $stepStatus
     * @param string $prompt
     * @param string|null $reasoning
     * @param string|null $output
     * @param int|null $tokenCost
     */
    public function __construct(
        Uuid $id,
        CarbonImmutable $createdAt,
        ?CarbonImmutable $processedAt,
        Uuid $clientId,
        Uuid $verificationId,
        Uuid $ruleId,
        ProcessingStatus $processingStatus,
        string $prompt,
        ?VerificationStepStatus $stepStatus = null,
        ?string $reasoning = null,
        ?string $output = null,
        ?int $tokenCost = null
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->processedAt = $processedAt;
        $this->clientId = $clientId;
        $this->verificationId = $verificationId;
        $this->ruleId = $ruleId;
        $this->processingStatus = $processingStatus;
        $this->stepStatus = $stepStatus;
        $this->prompt = $prompt;
        $this->reasoning = $reasoning;
        $this->output = $output;
        $this->tokenCost = $tokenCost;
    }

    public function markAsFinished(
        VerificationStepStatus $verificationStatus,
        string $output,
        string $reasoning,
        int $tokenCost,

    ): void {
        $this->stepStatus = $verificationStatus;
        $this->output = $output;
        $this->reasoning = $reasoning;
        $this->tokenCost = $tokenCost;
        $this->processingStatus = ProcessingStatus::finished;
        $this->processedAt = CarbonImmutable::now();
    }

    public function markAsInProgress(): void
    {
        $this->processingStatus = ProcessingStatus::inProgress;
        $this->processedAt = CarbonImmutable::now();
    }

    public function markAsFailure(): void
    {
        $this->processingStatus = ProcessingStatus::failure;
        $this->processedAt = CarbonImmutable::now();
    }
}

