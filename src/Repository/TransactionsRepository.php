<?php

namespace App\Repository;

use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transactions>
 */
class TransactionsRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transactions::class);
    }

    public function findByFromUser($fromUserId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.from_user_id = :fromUserId')
            ->setParameter('fromUserId', $fromUserId)
            ->getQuery()
            ->getResult()
            ;
    }
}
