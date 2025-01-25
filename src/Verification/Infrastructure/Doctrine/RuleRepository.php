<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Infrastructure\Doctrine;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Rule>
 * @method Rule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rule[]    findAll()
 * @method Rule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RuleRepository extends ServiceEntityRepository implements RuleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }

    public function getById(Uuid $id): Rule
    {
        $res = $this->find($id);
        if ($res === null) {
            throw new \DomainException(sprintf('rule not found by id %s', $id->toRfc4122()));
        }

        return $res;
    }

    public function getByRuleGroupId(Uuid $ruleGroupId): array
    {
        return $this->findBy(
            [
                'groupId' => $ruleGroupId,
            ],
        );
    }


    public function save(Rule $rule): void
    {
        $this->getEntityManager()->persist($rule);
    }
}
