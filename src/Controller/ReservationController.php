<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\Worker;
use App\Form\InfoReservationType;
use App\Form\ReservationType;
use App\Repository\ClientRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('reservation/', name: 'app_reservation')]
    public function index(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->forward('App\Controller\ReservationController::choiceTime', [
                'data' => $data,
            ]);
        }

        return $this->renderForm('reservation/_form.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('reservation/choiceTime', name: 'app_choice_time')]
    public function choiceTime(ReservationRepository $reservationRepository, $data): Response
    {
        $reservations = $reservationRepository->findByday($data->getDateReservation(), $data->getWorker());
        $checkedThisDay = true;
        if (date('Y-m-d') != $data->getDateReservation()->format('Y-m-d')) {
            $checkedThisDay = false;
        }
        $arrayTime = array(
            '11:00', '11:30', '12:00', '12:30', '13:00',
            '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30', '18:00',
            '18:30', '19:00', '19:30', '20:00', '20:30',
        );
        foreach ($reservations as $reservation) {
            $key = array_search($reservation->getStartTime()->format('H:i'), $arrayTime);
            if ($key !== false) {
                while ($arrayTime[$key] !== $reservation->getEndTime()->format('H:i')) {
                    $arrayTime[$key] = 'occupied';
                    $key++;
                }
                $arrayTime[$key] = 'occupied';
            }
        }
        //рассчитываем сколько отрезков в 30 мин занимает процедура
        $timeWork = $data->getService()->getExecutionTime();
        $lenght = count($arrayTime);
        list($hours, $minute) = explode(':', $timeWork->format('H:i'));
        $minute += $hours * 60;
        $countStep = $minute / 30;

        for ($i = 0; $i < $lenght; $i++) {
            if ($arrayTime[$i] != 'occupied') {
                if ($i < $lenght - ($countStep - 1)) {
                    for ($j = $i + 1, $count = 1; $count < $countStep; $count++, $j++) {
                        if ($arrayTime[$j] == 'occupied') {
                            $arrayTime[$i] = 'occupied';
                            break;
                        }
                    }
                } else {
                    $arrayTime[$i] = 'occupied';
                }
            }
        }

        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
            'times' => $arrayTime,
            'thisTime' => date('H:i'),
            'reservations' => $reservations,
            'data' => $data,
            'checkedThisDay' => $checkedThisDay,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    #[Route('reservation/{worker}/{service}/{date}/{time}', name: 'app_reservation_info')]
    public function insertIntoBD(Request $request,  Worker $worker, Service $service,$date,$time, ReservationRepository $repository): Response
    {
        $arrayTime = array(
            '11:00', '11:30', '12:00', '12:30', '13:00',
            '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30', '18:00',
            '18:30', '19:00', '19:30', '20:00', '20:30', );
        //находим время записи
        $key = array_search($time, $arrayTime);

        //рассчитываем сколько отрезков в 30 мин занимает процедура
        $timeWork = $service->getExecutionTime();
        list($hours, $minute) = explode(':', $timeWork->format('H:i'));
        $minute += $hours * 60;
        $countStep = $minute / 30;
        //находим время окончания процедуры
        $timeEnd = new \DateTime($arrayTime[$key+$countStep]);
        $timeStart = new \DateTime($time);

        $reservation = new Reservation();
        $reservation->setEndTime($timeEnd);
        $reservation->setClient($this->getUser()->getClient());
        $reservation->setService($service);
        $reservation->setWorker($worker);
        $reservation->setChecked(false);
        $reservation->setStartTime($timeStart);
        $reservation->setDateReservation(new \DateTime($date));
        $form = $this->createForm(InfoReservationType::class, $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->add($reservation);
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/info.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }





    #[Route('reservation/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
