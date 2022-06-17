<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\Worker;
use App\Form\InfoReservationType;
use App\Form\ReservationType;
use App\Repository\ClientRepository;
use App\Repository\ReservationRepository;
use App\Repository\WorkerRepository;
use Doctrine\DBAL\Types\DateType;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;

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
            $thisDate = new \DateTime;

            if ($data->getWorker()->getUserWorker()->getRoles() != ['ROLE_ADMIN'] and $data->getDateReservation()->format('Y-m-d') >= $thisDate->format('Y-m-d')) {

                return $this->forward('App\Controller\ReservationController::choiceTime', [
                    'data' => $data,
                ]);
            } else if ($data->getWorker()->getUserWorker()->getRoles() == ['ROLE_ADMIN']) {
                $message = 'Данный сотрудник не оказывает эту услугу';
            } else if ($data->getDateReservation()->format('Y-m-d') < $thisDate->format('Y-m-d') ) {

                $message = 'Нельзя выбирать прошедшую дату';
            }

            return $this->renderForm('reservation/_form.html.twig', [
                'reservation' => $reservation,
                'form' => $form,
                'message' => $message,
            ]);
        }


return $this->renderForm('reservation/_form.html.twig', ['reservation' => $reservation,
'form' => $form,
'message' => null]);
}

#[
Route('reservation/choiceTime', name: 'app_choice_time')]
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
        '18:30', '19:00', '19:30', '20:00', '20:30'
    );
    $test[] = 0;
    foreach ($reservations as $reservation) {
        $key = array_search($reservation->getStartTime()->format('H:i'), $arrayTime);//находим индекс начала записи
        $test[] = $key;
        if ($key !== false) {//если нашли
            while ($arrayTime[$key] !== $reservation->getEndTime()->format('H:i')) {//проставляем пометку 'ocupied' пока не дойдем до времени конца бронирования
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
    public function insertIntoBD(Request $request, Worker $worker, Service $service, $date, $time, ReservationRepository $repository, MailerInterface $mailer): Response
{
    $arrayTime = array(
        '11:00', '11:30', '12:00', '12:30', '13:00',
        '13:30', '14:00', '14:30', '15:00', '15:30',
        '16:00', '16:30', '17:00', '17:30', '18:00',
        '18:30', '19:00', '19:30', '20:00', '20:30', '21:00');
    //находим время записи
    $key = array_search($time, $arrayTime);

    //рассчитываем сколько отрезков в 30 мин занимает процедура
    $timeWork = $service->getExecutionTime();
    list($hours, $minute) = explode(':', $timeWork->format('H:i'));
    $minute += $hours * 60;
    $countStep = $minute / 30;
    //находим время окончания процедуры
    $timeEnd = new \DateTime($arrayTime[$key + $countStep]);
    $timeStart = new \DateTime($time);
//начало
    $AllSums = $repository->findByClientMoneySummaAll($this->getUser()->getClient());//назодим сколько потратил человек за всё время
    //сложить все суммы которые я нашла в бд
    $summa = 0;
    foreach ($AllSums as $AllSum) {
        $summa += $AllSum->getPrice();
    }

    //Добавить в БД поле сумма за бронь summa
    if ($summa > 3000 && $summa < 15999) {

        $discont = 2;
    } elseif ($summa > 16000 && $summa < 25999) {
        $discont = 5;
    } elseif ($summa > 26000 && $summa < 35999) {
        $discont = 10;
    } else {
        $discont = 0;
    }
    // Расчёт итоговой суммы за бронь
    $itog1 = $service->getPrice() * $discont / 100;
    $itog2 = $service->getPrice() - $itog1;
    //конец
    $reservation = new Reservation();
    $reservation->setEndTime($timeEnd);
    $reservation->setClient($this->getUser()->getClient());
    $reservation->setService($service);
    $reservation->setWorker($worker);
    $reservation->setChecked(false);
    $reservation->setStartTime($timeStart);
    $reservation->setPrice($itog2);
    $reservation->setDateReservation(new \DateTime($date));
    $form = $this->createForm(InfoReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $repository->add($reservation);

        $email = (new Email())
            ->from('tema-man1@yandex.ru')
            ->to($this->getUser()->getEmail())
            ->subject('Вы записаны на услугу!')
            ->text($this->renderView(
                'reservation/info1.html.twig', [
                'reservation' => $reservation,
            ]));

        $mailer->send($email);

        return $this->redirectToRoute('app_reservation_client', ['client' => $this->getUser()->getClient()], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('reservation/info.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
        'discont' => $discont,
    ]);
}
    #[Route('/reportPDFWorkerSchedule', name: 'app_schedule_report', methods: ['GET'])]
    public function report(ReservationRepository $reservationRepository): Response
{

    // Configure Dompdf according to your needs
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'arial');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);

    // Retrieve the HTML generated in our twig file
    $schedules = $reservationRepository->findByWorkerSchedule(date('Y-m-d'), $this->getUser()->getWorker()->getId());
    $html = $this->renderView('reservation/reportPDF.html.twig', [
        'schedules' => $schedules,
    ]);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html, 'UTF-8');

    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("mypdf.pdf", [
        "Attachment" => true
    ]);
    return $this->render('reservation/reportPDF.html.twig', [
        'schedules' => $reservationRepository->findByWorkerSchedule(date('Y-m-d'), $this->getUser()->getWorker()->getId()),
    ]);
}

    #[Route('reservation/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
{
    if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
        $reservationRepository->remove($reservation, true);
    }
    $client = $this->getUser()->getClient();
    return $this->redirectToRoute('app_reservation_client', ['client' => $client], Response::HTTP_SEE_OTHER);
}

    #[Route('reservation/client/{client}', name: 'app_reservation_client', methods: ['GET',])]
    public function reservationClient(Client $client, ReservationRepository $reservationRepository): Response
{
    $reservationsFuture = $reservationRepository->findByClientFuture($client);
    $reservationsPast = $reservationRepository->findByClientPast($client);
    $reservationsCurrent1 = $reservationRepository->findByClientCurrent1($client);
    $reservationsCurrent2 = $reservationRepository->findByClientCurrent2($client);
    $reservationsCurrent3 = $reservationRepository->findByClientCurrent3($client);

    return $this->render('reservation/reservationClient.html.twig', [
        'reservationsFuture' => $reservationsFuture,
        'reservationsPast' => $reservationsPast,
        'reservationsCurrent1' => $reservationsCurrent1,
        'reservationsCurrent2' => $reservationsCurrent2,
        'reservationsCurrent3' => $reservationsCurrent3,
    ]);
}

    #[Route('reservation/pageAdmin', name: 'app_reservation_index', methods: ['GET'])]
    public function pageAdmin(ReservationRepository $reservationRepository): Response
{
    $reservations = $reservationRepository->findByChecked();
    return $this->render('reservation/page_admin.html.twig', [
        'reservations' => $reservations,
    ]);
}
    #[Route('reservation/setCheck/{reservation}', name: 'app_reservation_set_check', methods: ['GET', 'POST'])]
    public function setCheck(ReservationRepository $reservationRepository, Reservation $reservation): Response
{
    $reservation->setChecked('true');
    $reservationRepository->add($reservation, true);
    return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
}
}
