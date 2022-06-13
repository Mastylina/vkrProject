<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function add(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByNotRead($client, $worker)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.recipient = :client')
            ->andWhere('m.sender = :worker')
            ->andWhere('m.checkReading = false')
            ->setParameter('worker', $worker)
            ->setParameter('client', $client)
            ->orderBy('m.dateAndTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findByNotReadforWorker($worker, $client)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.recipient = :worker')
            ->andWhere('m.sender = :client')
            ->andWhere('m.checkReading = false')
            ->setParameter('worker', $worker)
            ->setParameter('client', $client)
            ->orderBy('m.dateAndTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByClientByWorker($client, $worker)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.client = :client')
            ->andWhere('m.worker = :worker')
            ->setParameter('worker', $worker)
            ->setParameter('client', $client)
            ->orderBy('m.dateAndTime', 'DESC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
