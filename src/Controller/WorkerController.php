<?php

namespace App\Controller;

use App\Entity\FeedbackWorker;
use App\Entity\User;
use App\Entity\Worker;
use App\Form\RegistrationFormType;
use App\Form\WorkerType;
use App\Form\FeedbackWorkerType;
use App\Repository\FeedbackWorkerRepository;
use App\Repository\KpiRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Cassandra\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/worker')]
class WorkerController extends AbstractController
{
    #[Route('/', name: 'app_worker_index', methods: ['GET'])]
    public function index(WorkerRepository $workerRepository, FeedbackWorkerRepository $feedbackWorkerRepository): Response
    {
        $workers = $workerRepository->findAll();
        $nameWorker = array();
        $averages = array();

        foreach ($workers as $worker) {
            $nameWorker[] = $worker->getUserWorker()->getName();
            $feedbacks = $feedbackWorkerRepository->findByWorker($worker);
            $summ = 0;
            foreach ($feedbacks as $feedback) {
                $summ += $feedback->getEstimation();
            }
            if (count($feedbacks) === 0){
                $averages[] = 0;
            } else {
                $averages[]= $summ/count($feedbacks);
            }
        }


        $options = [
            'type' => 'bar',
            'data' => [
                'labels' => $nameWorker,
                'datasets' => [
                    [
                        'data' => $averages
                    ],
                ]
            ],
            'options' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Рейтинг мастеров'
                ],
                'scales' => [
                    'xAxes' => [
                        [
                            'ticks' => [
                                'fontSize' => 8
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $chartsOptions = urlencode(json_encode($options));
        return $this->render('worker/index.html.twig', [
            'controller_name' => 'WorkerController',
            'chartsOptions' => $chartsOptions,
            'workers' => $workerRepository->findAll(),
        ]);
    }

    #[Route('/newUser', name: 'app_user_worker_new', methods: ['GET', 'POST'])]
    public function newUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_WORKER']);
            $userRepository->add($user, true);
            return $this->redirectToRoute('app_worker_new', ['user' => $user->getId()]);
        }


        return $this->renderForm('worker/newUser.html.twig', ['registrationForm' => $form,]);
    }

    #[Route('/newWorker/{user}', name: 'app_worker_new', methods: ['GET', 'POST'])]
    public function newWorker(Request $request, WorkerRepository $workerRepository, User $user): Response
    {
        $worker = new Worker();
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $originalFilename = $brochureFile->getClientOriginalName();
                $filename = md5(uniqid('', true)) . $originalFilename;
                // this is needed to safely include the file name as part of the URL
                $brochureFile->move(
                    $this->getParameter('brochur_directory'),
                    $filename
                );
            }

            $worker->setUserWorker($user);
            $worker->setPhoto($filename);
            $workerRepository->add($worker, true);
            return $this->redirectToRoute('app_worker_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('worker/newWorker.html.twig', ['form' => $form,]);
    }

    #[ Route('/{id}', name: 'app_worker_show', methods: ['GET', 'POST'])]
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
            } else {
                $feedbacks = $worker->getFeedbackWorkers();
                $kpi = $kpiRepository->findByWorker($worker);
                return $this->render('worker/show.html.twig', [
                    'worker' => $worker,
                    'feedbacks' => $feedbacks,
                    'kpi' => $kpi,
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
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $originalFilename = $brochureFile->getClientOriginalName();
                $filename = md5(uniqid('', true)) . $originalFilename;
                // this is needed to safely include the file name as part of the URL
                $brochureFile->move(
                    $this->getParameter('brochur_directory'),
                    $filename
                );
            }
            $worker->setPhoto($filename);
            $workerRepository->add($worker, true);

            return $this->redirectToRoute('app_worker_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('worker/edit.html.twig', [
            'worker' => $worker,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_worker_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Worker $worker, WorkerRepository $workerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $worker->getId(), $request->request->get('_token'))) {
            $workerRepository->remove($worker, true);
        }

        return $this->redirectToRoute('app_worker_index', [], Response::HTTP_SEE_OTHER);
    }
}
