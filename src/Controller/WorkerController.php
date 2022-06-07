<?php

namespace App\Controller;

use App\Entity\FeedbackWorker;
use App\Entity\Worker;
use App\Form\WorkerType;
use App\Form\FeedbackWorkerType;
use App\Repository\FeedbackWorkerRepository;
use App\Repository\KpiRepository;
use App\Repository\ReservationRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/worker')]
class WorkerController extends AbstractController
{
    #[Route('/', name: 'app_worker_index', methods: ['GET'])]
    public function index(WorkerRepository $workerRepository): Response
    {
        return $this->render('worker/index.html.twig', [
            'workers' => $workerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_worker_new', methods: ['GET', 'POST'])]
    public function new(Request $request, WorkerRepository $workerRepository): Response
    {
        $worker = new Worker();
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workerRepository->add($worker, true);
            return $this->redirectToRoute('app_worker_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('worker/new.html.twig', [
            'worker' => $worker,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_worker_show', methods: ['GET', 'POST'])]
    public function show(KpiRepository $kpiRepository, Worker $worker, Request $request, FeedbackWorkerRepository $feedbackWorkerRepository, ReservationRepository $reservationRepository): Response
    {
        if ($this->getUser()) {
            if ($reservationRepository->findByWorker($worker, $this->getUser()->getClient())) {
                $feedback = new FeedbackWorker();
                $form = $this->createForm(FeedbackWorkerType::class, $feedback);
                $form->handleRequest($request);
                $feedbacks = $worker->getFeedbackWorkers();
                if ($form->isSubmitted() && $form->isValid()) {
                    $feedback->setClient($this->getUser()->getClient());
                    $feedback->setDateAndTime(new \DateTime());
                    $feedback->setWorker($worker);
                    $feedbackWorkerRepository->add($feedback, true);
                    return $this->redirectToRoute('app_worker_show', ['id' => $worker->getId()], Response::HTTP_SEE_OTHER);
                }

                return $this->renderForm('worker/showWithFeedback.html.twig', [
                    'worker' => $worker,
                    'feedbacks' => $feedbacks,
                    'form' => $form,
                ]);
            }
            else {
                $feedbacks = $worker->getFeedbackWorkers();
                $kpi = $kpiRepository->findByWorker($worker);
                return $this->render('worker/show.html.twig', [
                    'worker' => $worker,
                    'feedbacks' => $feedbacks,
                    'kpi'=>$kpi,
                ]);
            }
        } else {
            $feedbacks = $worker->getFeedbackWorkers();
            return $this->render('worker/show.html.twig', [
                'worker' => $worker,
                'feedbacks' => $feedbacks,
            ]);
        }
    }

    #[Route('/{id}/edit', name: 'app_worker_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Worker $worker, WorkerRepository $workerRepository): Response
    {
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workerRepository->add($worker, true);

            return $this->redirectToRoute('app_worker_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('worker/edit.html.twig', [
            'worker' => $worker,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_worker_delete', methods: ['POST'])]
    public function delete(Request $request, Worker $worker, WorkerRepository $workerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $worker->getId(), $request->request->get('_token'))) {
            $workerRepository->remove($worker, true);
        }

        return $this->redirectToRoute('app_worker_index', [], Response::HTTP_SEE_OTHER);
    }
}
