<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Message;
use App\Entity\Worker;
use App\Form\MessageType;
use App\Repository\ClientRepository;
use App\Repository\MessageRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/workersChat', name: 'app_message_index', methods: ['GET'])]//для клиента
    public function index(WorkerRepository $workerRepository, MessageRepository $messageRepository): Response
    {

        $workers = $workerRepository->findAll();
        $checked = array();
        $client = $this->getUser();
        foreach ($workers as $worker) {
            $count = count($messageRepository->findByNotRead($client, $worker->getUserWorker()));
            $checked[] = [$worker, $count];
        }
        return $this->render('message/index.html.twig', [
            'workers' => $checked,
        ]);
    }

    #[Route('/clientsChat', name: 'app_message_index1', methods: ['GET'])]//для работника
    public function indexClientsChat(ClientRepository $clientRepository, MessageRepository $messageRepository): Response
    {
        $clients = $clientRepository->findAll();
        $checked = array();
        $worker = $this->getUser();
        foreach ($clients as $client) {
            $count = count($messageRepository->findByNotReadforWorker($worker, $client->getUserClient()));
            $checked[] = [$client, $count];
        }
        return $this->render('message/index1.html.twig', [
            'clients' => $checked,
        ]);
    }

    #[Route('/message/{id}', name: 'app_message', methods: ['GET', 'POST'])]
    public function message(Request $request, Worker $worker, MessageRepository $messageRepository, WorkerRepository $workerRepository): Response
    {


        //устанавливаем непрочитанные сообщения прочитанными
        $unread = $messageRepository->findByNotRead($this->getUser(), $worker->getUserWorker());

        foreach ($unread as $message) {
            $message->setCheckReading('true');
            $messageRepository->add($message, true);
        }
        $client = $this->getUser()->getClient()->getId();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDateAndTime(new \DateTime());
            $message->setWorker($worker->getId());
            $message->setClient($client);
            $message->setSender($this->getUser());
            $message->setRecipient($worker->getUserWorker());
            $message->setCheckReading('false');
            $messageRepository->add($message, true);
            return $this->redirectToRoute('app_message', ['id' => $worker->getId()], Response::HTTP_SEE_OTHER);
        }
        $comments = $messageRepository->findByClientByWorker($client, $worker->getId());


        return $this->renderForm('message/message.html.twig', [
            'comments' => $comments,
            'form' => $form,
            'worker' => $worker,
        ]);
    }

    #[Route('/message1/{id}', name: 'app_message1', methods: ['GET', 'POST'])]
    public function messageClientsChat(Request $request, Client $client, MessageRepository $messageRepository, ClientRepository $clientRepository): Response
    {
        //устанавливаем непрочитанные сообщения прочитанными

        $unread = $messageRepository->findByNotReadforWorker($this->getUser(), $client->getUserClient());

        foreach ($unread as $message) {
            $message->setCheckReading('true');
            $messageRepository->add($message, true);
        }
        $worker = $this->getUser()->getWorker()->getId();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDateAndTime(new \DateTime());
            $message->setClient($client->getId());
            $message->setWorker($worker);
            $message->setSender($this->getUser());
            $message->setRecipient($client->getUserClient());
            $message->setCheckReading('false');
            $messageRepository->add($message, true);
            return $this->redirectToRoute('app_message1', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
        }
        $comments = $messageRepository->findByClientByWorker($client->getId(), $worker);


        return $this->renderForm('message/message1.html.twig', [
            'comments' => $comments,
            'form' => $form,
            'client' => $client,
        ]);
    }

    #[Route('/last_comments/{id}', name: "last_comments", methods: ["POST"])]
    public function lastComments(Worker $worker, MessageRepository $messageRepository)
    {

        $client = $this->getUser()->getClient()->getId();
        dd($client);
        $comments = $messageRepository->findByClientByWorker($client, $worker->getId());

        $lastComments = $this->renderView('message/_messages_blocks.html.twig', [
            'comments' => $comments
        ]);

        return new Response($lastComments);
    }
    #[Route('/last_comments1/{id}', name: "last_comments1", methods: ["POST"])]
    public function lastComments1(Client $client, MessageRepository $messageRepository)
    {
        $worker = $this->getUser()->getWorker()->getId();
        $comments = $messageRepository->findByClientByWorker($worker, $client->getId());

        $lastComments = $this->renderView('message/_messages_blocks1.html.twig', [
            'comments' => $comments
        ]);

        return new Response($lastComments);
    }
}
