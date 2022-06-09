<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Diary;
use App\Form\DiaryType;
use App\Repository\DiaryRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/diary')]
class DiaryController extends AbstractController
{
    #[Route('/choice', name: 'app_diary_choice', methods: ['GET'])]
    public function choice( ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findByMaster($this->getUser()->getWorker());

        foreach ($reservations as $reservation) {

            $clients[]=$reservation->getClient();
        }
        $clients = array_unique($clients);
        return $this->render('diary/choiceClient.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/index/{client}', name: 'app_diary_index', methods: ['GET'])]
    public function index( Client $client, DiaryRepository $diaryRepository): Response
    {

        $records = $diaryRepository->findByClientWorker($client, $this->getUser()->getWorker());
        return $this->render('diary/index.html.twig', [
            'client' => $client,
            'records' => $records,
        ]);
    }

    #[Route('/new_record/{client}', name: 'app_diary_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Client $client, DiaryRepository $diaryRepository): Response
    {
        $diary = new Diary();
        $form = $this->createForm(DiaryType::class, $diary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diary->setClient($client);
            $diary->setWorker($this->getUser()->getWorker());
            $diaryRepository->add($diary, true);

            return $this->redirectToRoute('app_diary_index', ['client'=> $client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diary/new.html.twig', [
            'diary' => $diary,
            'form' => $form,
            'client' => $client,
        ]);
    }

    #[Route('/{id}', name: 'app_diary_show', methods: ['GET'])]
    public function show(Diary $diary): Response
    {
        return $this->render('diary/show.html.twig', [
            'diary' => $diary,
        ]);
    }

    #[Route('/{id}/{client}/edit', name: 'app_diary_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, Diary $diary, DiaryRepository $diaryRepository): Response
    {
        $form = $this->createForm(DiaryType::class, $diary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diaryRepository->add($diary, true);

            return $this->redirectToRoute('app_diary_index', ['client' => $client], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diary/edit.html.twig', [
            'diary' => $diary,
            'form' => $form,
            'client' => $client,
        ]);
    }

    #[Route('/{id}/{client}', name: 'app_diary_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, Diary $diary, DiaryRepository $diaryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $diary->getId(), $request->request->get('_token'))) {
            $diaryRepository->remove($diary, true);
        }

        return $this->redirectToRoute('app_diary_index', ['client' => $client->getId()], Response::HTTP_SEE_OTHER);
    }
}
