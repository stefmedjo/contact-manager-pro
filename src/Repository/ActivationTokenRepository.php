<?php

namespace App\Repository;

use App\Entity\ActivationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActivationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivationToken[]    findAll()
 * @method ActivationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivationToken::class);
    }

    // /**
    //  * @return ActivationToken[] Returns an array of ActivationToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivationToken
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
