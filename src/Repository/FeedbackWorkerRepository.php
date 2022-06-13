<?php

namespace App\Repository;

use App\Entity\FeedbackWorker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FeedbackWorker>
 *
 * @method FeedbackWorker|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedbackWorker|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedbackWorker[]    findAll()
 * @method FeedbackWorker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackWorkerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackWorker::class);
    }

    public function add(FeedbackWorker $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FeedbackWorker $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByMonth()
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.dateAndTime <= :now')
            ->andWhere('l.dateAndTime >= :noNow')
            ->setParameter('now', date('Y-m-d', strtotime('today')))
            ->setParameter('noNow', date('Y-m-d', strtotime('-1 month', strtotime('today'))))
            ->getQuery()
            ->getResult();
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
//     * @return FeedbackWorker[] Returns an array of FeedbackWorker objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FeedbackWorker
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
