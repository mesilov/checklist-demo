<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use B24io\Checklist\Verification\Infrastructure\Doctrine\VerificationStepRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: VerificationStepRepository::class)]
#[ORM\Table(name: 'verification_steps')]
class VerificationStep
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    private Uuid $id;
    #[ORM\Column(name: 'processing_status', type: 'string', nullable: false, enumType: ProcessingStatus::class)]
    private ProcessingStatus $processingStatus;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    public readonly CarbonImmutable $createdAt;
    #[ORM\Column(name: 'processed_at', type: 'carbon_immutable', precision: 4, nullable: true)]
    #[Ignore]
    private ?CarbonImmutable $processedAt;
    #[ORM\Column(name: 'client_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $clientId;

    // input arguments
    #[ORM\Column(name: 'verification_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $verificationId;
    #[ORM\Column(name: 'rule_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $ruleId;
    #[ORM\Column(name: 'prompt', type: 'text', nullable: false)]
    private string $prompt;

    // output
    #[Embedded(class: VerificationStepResult::class, columnPrefix: "result_")]
    private VerificationStepResult $verificationStepResult;
    // output statistics
    #[Embedded(class: TokensUsage::class, columnPrefix: "stat_")]
    private TokensUsage $tokensUsage;
    #[ORM\Column(name: 'duration', type: 'smallint', nullable: true)]
    private ?int $duration;
    #[ORM\Column(name: 'model_version', type: 'string', nullable: true)]
    private ?string $modelVersion;

    public function __construct(
        Uuid $id,
        CarbonImmutable $createdAt,
        ?CarbonImmutable $processedAt,
        Uuid $clientId,
        Uuid $verificationId,
        Uuid $ruleId,
        ProcessingStatus $processingStatus,
        string $prompt
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->processedAt = $processedAt;
        $this->clientId = $clientId;
        $this->verificationId = $verificationId;
        $this->ruleId = $ruleId;
        $this->processingStatus = $processingStatus;
        $this->prompt = $prompt;
        $this->verificationStepResult = VerificationStepResult::init();
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getVerificationId(): Uuid
    {
        return $this->verificationId;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRuleId(): Uuid
    {
        return $this->ruleId;
    }

    public function markAsFinished(
        VerificationStepResult $verificationStepResult,
        TokensUsage $tokensUsage,
        int $duration,
        string $modelVersion
    ): void {
        $this->verificationStepResult=$verificationStepResult;
        $this->tokensUsage = $tokensUsage;
        $this->duration = $duration;
        $this->modelVersion = $modelVersion;

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

