<?php

namespace App\Repository;

use App\Entity\Sim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Sim|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sim|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sim[]    findAll()
 * @method Sim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sim::class);
    }

//    /**
//     * @return Sim[] Returns an array of Sim objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sim
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
