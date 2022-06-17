<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Service;
use App\Entity\Worker;
use App\Entity\Client;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use App\Repository\FeedbackWorkerRepository;
use App\Repository\ReservationRepository;
use App\Repository\WorkerRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/service')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository, FeedbackRepository $feedbackRepository): Response
    {
        $services = $serviceRepository->findAll();
        $nameService = array();
        $averages = array();

        foreach ($services as $service) {
            $nameService[] = $service->getName();
            $feedbacks = $feedbackRepository->findByService($service);
            $summ = 0;
            foreach ($feedbacks as $feedback) {
                $summ += $feedback->getEstimation();
            }
            if (count($feedbacks) === 0){
                $averages[$service->getName()] = 0;
            } else {
                $averages[$service->getName()]= $summ/count($feedbacks);
            }
        }
        return $this->render('service/index.html.twig', [
            'nameService'=>$nameService,
            'averages'=>$averages,
            'services' => $services,
        ]);
    }

    #[Route('/reportPDF', name: 'app_service_report', methods: ['GET'])]
    public function report(ServiceRepository $serviceRepository, FeedbackRepository $feedbackRepository): Response
    {
        $services = $serviceRepository->findAll();
        $averages = array();

        foreach ($services as $service) {
            $feedbacks = $feedbackRepository->findByService($service);
            $summ = 0;
            foreach ($feedbacks as $feedback) {
                $summ += $feedback->getEstimation();
            }
            if (count($feedbacks) === 0){
                $averages[$service->getName()] = 0;
            } else {
                $averages[$service->getName()]= $summ/count($feedbacks);
            }
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('service/reportPDF.html.twig', [
            'services' => $serviceRepository->findAll(),
            'average' => $averages,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html,'UTF-8');

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
        return $this->render('service/reportPDF.html.twig', [
            'services' => $services,
            'average' => $averages,
        ]);
    }

    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request,WorkerRepository$workerRepository, ServiceRepository $serviceRepository,SluggerInterface $slugger): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $originalFilename = $brochureFile->getClientOriginalName();
                $filename = md5(uniqid('', true)) . $originalFilename;
                // this is needed to safely include the file name as part of the URL
                $brochureFile->move(
                    $this->getParameter('brochures_directory'),
                    $filename
                );
            }

            $service->setPhoto($filename);
            $serviceRepository->add($service, true);
            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET', 'POST'])]
    public function show(Service $service, Request $request, FeedbackRepository $feedbackRepository,ReservationRepository $reservationRepository): Response
    {
        if ($this->getUser()) {
            if ($reservationRepository->findByService($service, $this->getUser()->getClient())) {
                $feedback = new Feedback();
                $form = $this->createForm(FeedbackType::class, $feedback);
                $form->handleRequest($request);
                $feedbacks = $service->getFeedbacks();
                $workers = $service->getWorkers();
                if ($form->isSubmitted() && $form->isValid()) {
                    $feedback->setClient($this->getUser()->getClient());
                    $feedback->setDateAndTime(new \DateTime());
                    $feedback->setService($service);
                    $feedbackRepository->add($feedback, true);
                    return $this->redirectToRoute('app_service_show', ['id' => $service->getId()], Response::HTTP_SEE_OTHER);
                }
                return $this->renderForm('service/showWithFeedback.html.twig', [
                    'service' => $service,
                    'workers' => $workers,
                    'feedbacks' => $feedbacks,
                    'form' => $form,
                ]);
            }
            else {
                $workers = $service->getWorkers();
                $feedbacks = $service->getFeedbacks();
                return $this->render('service/show.html.twig', [
                    'service' => $service,
                    'workers' => $workers,
                    'feedbacks' => $feedbacks,
                ]);
            }
        }
        else{
            $workers = $service->getWorkers();
            $feedbacks = $service->getFeedbacks();
            return $this->render('service/show.html.twig', [
                'service' => $service,
                'workers' => $workers,
                'feedbacks' => $feedbacks,
            ]);
        }
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $originalFilename = $brochureFile->getClientOriginalName();

                $filename = md5(uniqid('', true)) . $originalFilename;
                // this is needed to safely include the file name as part of the URL
                $brochureFile->move(
                    $this->getParameter('brochures_directory'),
                    $filename
                );
            }

            $service->setPhoto($filename);
            $serviceRepository->add($service, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $serviceRepository->remove($service, true);
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }
}