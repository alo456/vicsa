<?php

namespace App\Repository;

use App\Entity\DeviceBill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DeviceBill|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceBill|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceBill[]    findAll()
 * @method DeviceBill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceBillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeviceBill::class);
    }

//    /**
//     * @return DeviceBill[] Returns an array of DeviceBill objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeviceBill
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
