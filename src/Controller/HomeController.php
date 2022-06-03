<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(WorkerRepository $workerRepository,ServiceRepository $serviceRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'workers' => $workerRepository->findAll(),
            'controller_name' => 'HomeController',
        ]);
    }
}
