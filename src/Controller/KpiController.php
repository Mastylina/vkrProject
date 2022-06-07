<?php

namespace App\Controller;

use App\Entity\Kpi;
use App\Entity\Worker;
use App\Form\KpiType;
use App\Repository\FeedbackWorkerRepository;
use App\Repository\KpiRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/kpi')]
class KpiController extends AbstractController
{
    #[Route('/', name: 'app_kpi_index', methods: ['GET'])]
    public function index(KpiRepository $kpiRepository): Response
    {
        return $this->render('kpi/index.html.twig', [
            'kpis' => $kpiRepository->findAll(),
        ]);
    }

    #[Route('/new/{worker}', name: 'app_kpi_new', methods: ['GET', 'POST'])]
    public function new(ReservationRepository $reservationRepository, Worker $worker, Request $request,
                        KpiRepository $kpiRepository, FeedbackWorkerRepository $feedbackWorkerRepository): Response
    {
        $kpi = new Kpi();
        $form = $this->createForm(KpiType::class, $kpi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservations = $reservationRepository->findByMonth();
            $summa = 0;
            foreach ($reservations as $reservation) {
               $summa += $reservation->getPrice();
            }
            $feedbacks = $feedbackWorkerRepository->findByMonth();
            $grades = 0;
            foreach ($feedbacks as $feedback){
                $grades+= $feedback->getEstimation();
            }
            $grades= $grades/ count($feedbacks);
            $kpi->setFactVolumeSales(1200);
            $kpi->setFactQualityService(8);
            $kpi->setWorker($worker);
            $kpiRepository->add($kpi, true);

            $ind1 = ($kpi->getFactVolumeSales() - $kpi->getMinVolumeSales())/ ($kpi->getPlannedVolumeSales()-$kpi->getMinVolumeSales());

            $ind2 = ($kpi->getFactQualityService()-$kpi->getMinQualityService())/($kpi->getPlannedQualityService()-$kpi->getMinQualityService());
            $salary = $ind1 * $kpi->getWeightVolumeSales() + $ind2* $kpi->getWeightQualityService();
            return $this->render('kpi/show.html.twig', [
                'salary'=>$salary,
                'kpi' => $kpi,
            ]);
        }

        return $this->renderForm('kpi/new.html.twig', [
            'kpi' => $kpi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{worker}', name: 'app_kpi_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Kpi $kpi, KpiRepository $kpiRepository, ReservationRepository $reservationRepository,
    Worker $worker, FeedbackWorkerRepository $feedbackWorkerRepository): Response
    {
        $form = $this->createForm(KpiType::class, $kpi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservations = $reservationRepository->findByMonth();
            $summa = 0;
            foreach ($reservations as $reservation) {
                $summa += $reservation->getPrice();
            }
            $feedbacks = $feedbackWorkerRepository->findByMonth();
            $grades = 0;
            foreach ($feedbacks as $feedback){
                $grades+= $feedback->getEstimation();
            }
            $grades= $grades / count($feedbacks);
            $kpi->setFactVolumeSales($summa);
            $kpi->setFactQualityService($grades);
            $kpi->setWorker($worker);
            $kpiRepository->add($kpi, true);

            $ind1 = ($kpi->getFactVolumeSales() - $kpi->getMinVolumeSales())/ ($kpi->getPlannedVolumeSales()-$kpi->getMinVolumeSales());
            $ind2 = ($kpi->getFactQualityService()-$kpi->getMinQualityService())/($kpi->getPlannedQualityService()-$kpi->getMinQualityService());
            $salary = $ind1 * $kpi->getWeightVolumeSales() + $ind2* $kpi->getWeightQualityService();
            return $this->render('kpi/show.html.twig', [
                'salary'=>$salary,
                'kpi' => $kpi,
            ]);
        }

        return $this->renderForm('kpi/new.html.twig', [
            'kpi' => $kpi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_kpi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Kpi $kpi, KpiRepository $kpiRepository): Response
    {
        $form = $this->createForm(KpiType::class, $kpi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $kpiRepository->add($kpi, true);

            return $this->redirectToRoute('app_kpi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('kpi/edit.html.twig', [
            'kpi' => $kpi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_kpi_delete', methods: ['POST'])]
    public function delete(Request $request, Kpi $kpi, KpiRepository $kpiRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $kpi->getId(), $request->request->get('_token'))) {
            $kpiRepository->remove($kpi, true);
        }

        return $this->redirectToRoute('app_kpi_index', [], Response::HTTP_SEE_OTHER);
    }
}
