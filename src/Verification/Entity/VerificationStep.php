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
    /**
     * @see https://platform.openai.com/docs/api-reference/completions/object#completions/object-system_fingerprint
     */
    #[ORM\Column(name: 'system_fingerprint', type: 'string', nullable: true)]
    private ?string $systemFingerprint;

    #[Embedded(class: UserFeedback::class, columnPrefix: "stat_")]
    private TokensUsage $tokensUsage;

    #[ORM\Column(name: 'duration', type: 'integer', nullable: true)]
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
        string $prompt,
        ?VerificationStepStatus $stepStatus = null,
        ?string $reasoning = null,
        ?string $output = null,
        ?string $systemFingerprint = null,
        TokensUsage $tokensUsage = null,
        ?int $duration = null,
        ?string $modelVersion = null
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
        $this->systemFingerprint = $systemFingerprint;
        $this->tokensUsage = $tokensUsage;
        $this->duration = $duration;
        $this->modelVersion = $modelVersion;
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
        VerificationStepStatus $verificationStatus,
        string $output,
        string $reasoning,
        TokensUsage $tokensUsage,
        int $duration,
        string $modelVersion
    ): void {
        $this->stepStatus = $verificationStatus;
        $this->output = $output;
        $this->reasoning = $reasoning;
        $this->tokensUsage = $tokensUsage;
        $this->processingStatus = ProcessingStatus::finished;
        $this->processedAt = CarbonImmutable::now();
        $this->duration = $duration;
        $this->modelVersion = $modelVersion;
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

