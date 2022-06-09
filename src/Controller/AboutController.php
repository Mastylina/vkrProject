<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(): Response
    {
        $labelsChunk[0] = 'Январь';
        $labelsChunk[1] = 'Февраль';
        $labelsChunk[2] = 'Март';
        $labelsChunk[3] = 'Апрель';
        $labelsChunk[4] = 'Май';
        $labelsChunk[5] = 'Июнь';
        $labelsChunk[6] = 'Июль';
        $labelsChunk[7] = 'Август';
        $labelsChunk[8] = 'Сентябрь';
        $labelsChunk[9] = 'Октябрь';
        $labelsChunk[10] = 'Ноябрь';
        $labelsChunk[11] = 'Декабрь';

        $dataChunk [0] = 11;
        $dataChunk [1] = 2;
        $dataChunk [2] = 15;
        $dataChunk [3] = 6;
        $dataChunk [4] = 1;
        $dataChunk [5] = 14;
        $dataChunk [6] = 15;
        $dataChunk [7] = 18;
        $dataChunk [8] = 19;
        $dataChunk [9] = 2;
        $dataChunk [10] = 6;
        $dataChunk [11] = 7;

        $options = [
            'type' => 'bar',
            'data' => [
                'labels' => $labelsChunk,
                'datasets' => [
                    [
                        'data' => $dataChunk
                    ],
                ]
            ],
            'options' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Рейтинг'
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
