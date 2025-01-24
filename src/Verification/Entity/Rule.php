<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use B24io\Checklist\Verification\Infrastructure\Doctrine\RuleRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RuleRepository::class)]
#[ORM\Table(name: 'verification_rules')]
class Rule
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    private Uuid $id;
    #[ORM\Column(name: 'group_id', type: 'uuid', nullable: false)]
    private Uuid $groupId;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    public readonly CarbonImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    private CarbonImmutable $updatedAt;
    #[ORM\Column(name: 'status', type: 'string', nullable: false, enumType: RuleStatus::class)]
    private RuleStatus $status;
    /**
     * @var array<int, Uuid>
     */
    #[ORM\Column(name: 'document_type_ids', type: 'json', nullable: false)]
    private array $documentTypeIds;
    #[ORM\Column(name: 'name', type: 'text', nullable: false)]
    private string $name;
    #[ORM\Column(name: 'rule', type: 'text', nullable: false)]
    private string $rule;
    #[ORM\Column(type: 'text', nullable: false)]
    private string $prompt;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment;
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $weight;

    public function __construct(
        Uuid $id,
        Uuid $groupId,
        array $documentTypeIds,
        CarbonImmutable $createdAt,
        CarbonImmutable $updatedAt,
        RuleStatus $status,
        string $name,
        string $rule,
        string $prompt,
        int $weight = 1,
        ?string $comment = null
    ) {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->status = $status;
        $this->documentTypeIds = $documentTypeIds;
        $this->name = $name;
        $this->rule = $rule;
        $this->prompt = $prompt;
        $this->comment = $comment;
        $this->weight = $weight;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new CarbonImmutable();
    }

    public function changeRule(string $rule): void
    {
        $this->rule = $rule;
        $this->updatedAt = new CarbonImmutable();
    }

    public function changePrompt(string $prompt): void
    {
        $this->prompt = $prompt;
        $this->updatedAt = new CarbonImmutable();
    }


    public function changeWeight(int $weight): void
    {
        $this->weight = $weight;
        $this->updatedAt = new CarbonImmutable();
    }

    public function changeComment(string $comment): void
    {
        $this->comment = $comment;
        $this->updatedAt = new CarbonImmutable();
    }

    public function markAsDisabled(string $comment): void
    {
        $this->status = RuleStatus::disabled;
        $this->updatedAt = new CarbonImmutable();
        $this->comment = $comment;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getStatus(): RuleStatus
    {
        return $this->status;
    }

    /**
     * @return Uuid[]
     */
    public function getDocumentTypeIds(): array
    {
        return $this->documentTypeIds;
    }
}

