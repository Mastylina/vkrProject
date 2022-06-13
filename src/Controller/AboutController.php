<?php

namespace App\Controller;

use App\Repository\FeedbackRepository;
use App\Repository\ReservationRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(ServiceRepository $serviceRepository, ReservationRepository $reservationRepository, FeedbackRepository $feedbackRepository): Response
    {
        $services = $serviceRepository->findAll();
        $nameService = array();
        $counts = array();
        $averages = array();

        foreach ($services as $service) {
            $nameService[] = $service->getName();
            $reservations = $reservationRepository->findByServiceForReport($service);
            $feedbacks = $feedbackRepository->findByService($service);
            $summ = 0;
            foreach ($feedbacks as $feedback) {
                 $summ += $feedback->getEstimation();
            }
            if (count($feedbacks) === 0){
                $averages[] = 0;
            } else {
                $averages[]= $summ/count($feedbacks);
            }

            $counts[] = count($reservations);
        }


        $options = [
            'type' => 'bar',
            'data' => [
                'labels' => $nameService,
                'datasets' => [
                    [
                        'data' => $counts
                    ],
                ]
            ],
            'options' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Рейтинг посещаемости процедур'
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
        return $this->render('about/index.html.twig', [
            'controller_name' => 'AboutController',
            'chartsOptions' => $chartsOptions
        ]);
    }
}
