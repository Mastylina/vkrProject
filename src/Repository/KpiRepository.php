<?php

namespace App\Repository;

use App\Entity\Kpi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Kpi>
 *
 * @method Kpi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kpi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kpi[]    findAll()
 * @method Kpi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KpiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kpi::class);
    }

    public function add(Kpi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Kpi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByWorker($worker)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.worker = :worker')
            ->setParameter('worker', $worker)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Kpi[] Returns an array of Kpi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('k')
//            ->andWhere('k.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('k.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Kpi
//    {
//        return $this->createQueryBuilder('k')
//            ->andWhere('k.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
