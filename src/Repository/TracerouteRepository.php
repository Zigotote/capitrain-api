<?php

namespace App\Repository;

use App\Entity\Traceroute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Traceroute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traceroute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traceroute[]    findAll()
 * @method Traceroute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TracerouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traceroute::class);
    }

    // /**
    //  * @return Traceroute[] Returns an array of Traceroute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Traceroute
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
