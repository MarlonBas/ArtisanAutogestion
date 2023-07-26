<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/event/add', name: 'app_event_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class);
        $event = new Event();

        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
       

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $event->setUser($user);
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', "Evénement ajouté avec succès");
            return $this->redirectToRoute('app_main');
        }
        return $this -> render('event/addevent.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/event/remove{id}', name: 'app_event_remove')]
    public function remove($id, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);
        $entityManager->remove($event);
        $entityManager->flush();
        return $this->redirectToRoute('app_main');
    }

    #[Route('/event/edit{id}', name: 'app_event_show')]
    public function edit(int $id, Request $request, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException("ERREUR: l'événement n'a pas été trouvé");
        }
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', "Evénement mis à jour avec succès");
            return $this->redirectToRoute('app_main');
        }
        return $this->render('event/editevent.html.twig', [
            'form' => $form->createView(), 'event' => $event]);
    }
}
