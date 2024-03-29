<?php

namespace App\Repository;

use App\Entity\Activation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Activation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activation[]    findAll()
 * @method Activation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Activation::class);
    }

//    /**
//     * @return Activation[] Returns an array of Activation objects
//     */
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
    public function findOneBySomeField($value): ?Activation
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
