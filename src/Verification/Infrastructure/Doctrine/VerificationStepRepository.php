<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Infrastructure\Doctrine;

use B24io\Checklist\Verification\Entity\VerificationStep;
use B24io\Checklist\Verification\Repository\VerificationStepRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<VerificationStep>
 * @method VerificationStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerificationStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerificationStep[]    findAll()
 * @method VerificationStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationStepRepository extends ServiceEntityRepository implements VerificationStepRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationStep::class);
    }

    public function getById(Uuid $id): VerificationStep
    {
        $res = $this->find($id);
        if ($res === null) {
            throw new \DomainException(sprintf('verification step not found by id %s', $id->toRfc4122()));
        }

        return $res;
    }

    /**
     * @param Uuid $verificationId
     * @return VerificationStep[]
     */
    public function getByVerificationId(Uuid $verificationId): array
    {
        return $this->findBy([
            'verificationId' => $verificationId
        ]);
    }

    public function save(VerificationStep $verificationStep): void
    {
        $this->getEntityManager()->persist($verificationStep);
    }
}
