<?php

namespace App\Repository;

use App\Entity\SimBill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SimBill|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimBill|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimBill[]    findAll()
 * @method SimBill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimBillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SimBill::class);
    }

//    /**
//     * @return SimBill[] Returns an array of SimBill objects
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
    public function findOneBySomeField($value): ?SimBill
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
