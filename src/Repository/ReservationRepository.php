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
    public function findByMaster($worker)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.worker = :worker')
            ->setParameter('worker', $worker)
            ->getQuery()
            ->getResult();
    }
    public function findByMasterPerMonth($worker)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.worker = :worker')
            ->andWhere('l.dateReservation <= :now')
            ->andWhere('l.dateReservation >= :noNow')
            ->andWhere('l.checked = true')
            ->setParameter('now', date('Y-m-d', strtotime('today')))
            ->setParameter('noNow', date('Y-m-d', strtotime('-1 month', strtotime('today'))))
            ->setParameter('worker', $worker)
            ->getQuery()
            ->getResult();
    }
    public function findByMonth()
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.dateReservation <= :now')
            ->andWhere('l.dateReservation >= :noNow')
            ->andWhere('l.checked = true')
            ->setParameter('now', date('Y-m-d', strtotime('today')))
            ->setParameter('noNow', date('Y-m-d', strtotime('-1 month', strtotime('today'))))
            ->getQuery()
            ->getResult();
    }

    public function findByWorker($worker, $client)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.worker = :worker')
            ->andWhere('l.client = :client')
            ->setParameter('worker', $worker)
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();
    }
    public function findByServiceForReport($service)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.service = :service')
            ->andWhere ('l.checked = true')
            ->setParameter('service', $service)
            ->getQuery()
            ->getResult();
    }
    public function findByService($service, $client)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.service = :service')
            ->andWhere('l.client = :client')
            ->setParameter('service', $service)
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();
    }

    public function remove(Reservation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByday($dateDay, $master)//???????????????????? ?? ???????????????????? ???????? ?? ?????????????????????? ??????????????
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.dateReservation = :date')
            ->andWhere('l.worker = :worker')
            ->setParameter('date', $dateDay)
            ->setParameter('worker', $master)
            ->getQuery()
            ->getResult();
    }

    public function findByWorkerSchedule($dateDay, $master)//???????????? ?????????????? ???? ???????????????????????? ??????
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.dateReservation >= :dateToday')
            ->andWhere('l.worker = :worker')
            ->setParameter('dateToday', $dateDay)
            ->setParameter('worker', $master)
            ->addOrderBy('l.dateReservation', 'ASC')
            ->addOrderBy('l.startTime', 'ASC')

            ->getQuery()
            ->getResult();
    }

    public function findByClientMoneySummaAll($client)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.checked = true')
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();
    }

    public function findByClientFuture($client)//???????????????????? ???????????? ????????????????????????
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation > :date')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->orderBy('l.dateReservation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByClientCurrent1($client)//???????????????????? ?????????????????????? ?????????????????? ????????????????????????
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
            ->getResult();
    }

    public function findByClientCurrent2($client)//???????????????????? ?????????????????????? ?????????????? ????????????????????????
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
            ->getResult();
    }

    public function findByClientCurrent3($client)//???????????????????? ?????????????????????? ?????????????????????? ????????????????????????
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
            ->getResult();
    }

    public function findByClientPast($client)//???????????????????? ?????????????????? ????????????????????????
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.dateReservation < :date')
            ->setParameter('client', $client)
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getResult();
    }
    public function findByChecked()//???????????????????? ???? ???????????????????????????? ????????????????????
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.checked = :check')
            ->setParameter('check', 'false')
            ->addOrderBy('l.dateReservation', 'ASC')
            ->getQuery()
            ->getResult();
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
