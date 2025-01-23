<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use B24io\Checklist\Verification\Infrastructure\Doctrine\RuleRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RuleRepository::class)]
class Rule
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;
    #[ORM\Column(name: 'created_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    public readonly CarbonImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'carbon_immutable', precision: 4, nullable: false)]
    #[Ignore]
    private CarbonImmutable $updatedAt;

    #[ORM\Column(name: 'status', type: 'string', nullable: false, enumType: RuleStatus::class)]
    private RuleStatus $status;

    #[ORM\Column(type: 'text', nullable: false)]
    public readonly string $name;
    #[ORM\Column(type: 'text', nullable: false)]
    public readonly string $rule;
    #[ORM\Column(type: 'text', nullable: false)]
    public readonly string $prompt;

    #[ORM\Column(type: 'text', nullable: true)]
    public readonly ?string $comment;

    #[ORM\Column(type: 'int', nullable: false)]
    public readonly int $weight;
}

