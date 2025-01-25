<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Infrastructure\Doctrine;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\Verification;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Verification>
 * @method Verification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verification[]    findAll()
 * @method Verification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationRepository extends ServiceEntityRepository implements VerificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verification::class);
    }

    public function getById(Uuid $id): Verification
    {
        $res = $this->find($id);
        if ($res === null) {
            throw new \DomainException(sprintf('verification not found by id %s', $id->toRfc4122()));
        }

        return $res;
    }

    public function save(Verification $verification): void
    {
        $this->getEntityManager()->persist($verification);
    }
}
