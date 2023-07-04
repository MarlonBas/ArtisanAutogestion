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
        $user = $token->getUser();

        $clients = $user->getClients()->toArray();

        return $this->render('main/index.html.twig', [
            'clients' => $clients,
        ]);
    }
}
