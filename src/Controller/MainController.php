<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MainController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();

        $events = $user->getEvents()->toArray();

        $today = new \DateTime('now');

        $past = $this->eventPastFilter($events, $today);
        $present = $this->eventPresentFilter($events, $today);
        $futur = $this->eventFuturFilter($events, $today);
    

        return $this->render('main/index.html.twig', [
            'past' => $past,
            'present' => $present,
            'futur' => $futur,
            'today' => $today,
        ]);
    }

    private function eventPastFilter($event, $today)
    {
        $eventsFiltred = array_filter($event, function ($event) use ($today) {
            return $event->getDate() < $today;
        });
        return $eventsFiltred;
    }
    private function eventPresentFilter($event, $today)
    {
        $eventsFiltred = array_filter($event, function ($event) use ($today) {
            return $event->getDate() > $today && $event->getDate()->format('m') == $today->format('m');
        });
        return $eventsFiltred;
    }
    private function eventFuturFilter($event, $today)
    {
        $eventsFiltred = array_filter($event, function ($event) use ($today) {
            return $event->getDate() > $today && $event->getDate()->format('m') != $today->format('m');
        });
        return $eventsFiltred;
    }
}
