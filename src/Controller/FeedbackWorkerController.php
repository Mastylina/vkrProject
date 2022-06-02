<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackWorkerController extends AbstractController
{
    #[Route('/feedback/worker', name: 'app_feedback_worker')]
    public function index(): Response
    {
        return $this->render('feedback_worker/index.html.twig', [
            'controller_name' => 'FeedbackWorkerController',
        ]);
    }
}
