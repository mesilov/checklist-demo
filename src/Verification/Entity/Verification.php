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
#[ORM\Table(name: 'verifications')]
class Verification
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    private Uuid $id;
    #[ORM\Column(name: 'client_id', type: 'uuid', unique: false, nullable: false)]
    private Uuid $clientId;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    private CarbonImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'carbon_immutable', precision: 4, nullable: true)]
    #[Ignore]
    private ?CarbonImmutable $updatedAt;
    /**
     * @var array<int, Uuid>
     */
    #[ORM\Column(name: 'document_ids', type: 'json', nullable: false)]
    private array $documentIds;
    #[ORM\Column(name: 'rule_group_id', type: 'uuid', unique: false)]
    private Uuid $ruleGroupId;
    #[ORM\Column(name: 'processing_status', type: 'string', nullable: false, enumType: ProcessingStatus::class)]
    private ProcessingStatus $processingStatus;
    #[ORM\Column(name: 'model', type: 'string', nullable: false, enumType: LanguageModel::class)]
    private LanguageModel $model;
    #[ORM\Column(name: 'result_summary', type: 'text', nullable: true)]
    private ?string $resultSummary;
    #[ORM\Column(name: 'note', type: 'text', nullable: true)]
    private ?string $note;
    #[ORM\Column(name: 'total_token_cost', type: 'integer', nullable: true, options: ['unsigned' => true])]
    private ?int $totalTokenCost;
    #[Embedded(class: UserFeedback::class, columnPrefix: "user_feedback_")]
    private UserFeedback $userFeedback;

    public function __construct(
        Uuid $id,
        Uuid $clientId,
        CarbonImmutable $createdAt,
        ?CarbonImmutable $processedAt,
        array $documentIds,
        Uuid $ruleGroupId,
        ProcessingStatus $processingStatus,
        LanguageModel $model,
        ?string $resultSummary = null,
        ?string $note = null,
        ?int $totalTokenCost = null,
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $processedAt;
        $this->documentIds = $documentIds;
        $this->ruleGroupId = $ruleGroupId;
        $this->processingStatus = $processingStatus;
        $this->model = $model;
        $this->resultSummary = $resultSummary;
        $this->note = $note;
        $this->totalTokenCost = $totalTokenCost;
        $this->userFeedback = UserFeedback::init();
    }

    public function setUserFeedback(UserFeedback $userFeedback): void
    {
        $this->userFeedback = $userFeedback;
    }

    public function markAsProcessed(
        string $resultSummary,
        int $totalTokenCost,
        ?string $comment
    ): void {
        $this->processingStatus = ProcessingStatus::finished;
        $this->resultSummary = $resultSummary;
        $this->totalTokenCost = $totalTokenCost;
        $this->note = $comment;
        $this->updatedAt = CarbonImmutable::now();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Uuid[]
     */
    public function getDocumentIds(): array
    {
        return $this->documentIds;
    }

    public function getRuleGroupId(): Uuid
    {
        return $this->ruleGroupId;
    }

    public function getProcessingStatus(): ProcessingStatus
    {
        return $this->processingStatus;
    }
}

