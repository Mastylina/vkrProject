<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function add(Reservation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByday($dateDay, $master)//резервации в конкретный день к конкретному мастеру
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.dateReservation = :date')
            ->andWhere('l.worker = :worker')
            ->setParameter('date', $dateDay)
            ->setParameter('worker', $master)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByWorkerSchedule($dateDay, $master)//График мастера от сегодняшнего дня
    {
        return $this->createQueryBuilder('l')
            ->orWhere('l.dateReservation >= :dateToday')
            ->andWhere('l.worker = :worker')

            ->setParameter('dateToday', $dateDay)
            ->setParameter('worker', $master)
            ->orderBy('l.startTime', 'ASC')
            ->orderBy('l.dateReservation', 'ASC')


            ->getQuery()
            ->getResult()
            ;
    }

    public function findByClientFuture($client)//возвращает будщие бронирования
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation > :date')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->orderBy('l.dateReservation', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByClientCurrent1($client)//возвращает сегоднешние прошедшие бронирования
    {
        $time = new \DateTime();
        $time->format('H:i');
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation = :date')
            ->andWhere('l.endTime < :timeEnd')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->setParameter('timeEnd', $time)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByClientCurrent2($client)//возвращает сегоднешние текущие бронирования
    {
        $time = new \DateTime();
        $time->format('H:i');
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation = :date')
            ->andWhere('l.endTime >= :time')
            ->andWhere('l.startTime <= :time')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->setParameter('time', $time)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByClientCurrent3($client)//возвращает сегоднешние предстоящие бронирования
    {
        $time = new \DateTime();
        $time->format('H:i');
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation = :date')
            ->andWhere('l.startTime > :time')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->setParameter('time', $time)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByClientPast($client)//возвращает прошедшие бронирования
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation < :date')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
