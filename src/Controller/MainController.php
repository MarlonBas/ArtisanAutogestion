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

        $clients = $user->getClients()->toArray();
        $documents = $user->getDocuments()->toArray();

        $devisEnCours = $this->documentTypeFilter($documents, "devisEnCours");
        $devisEnvoyes = $this->documentTypeFilter($documents, "devisEnvoyes");
        $devisAcceptes = $this->documentTypeFilter($documents, "devisAcceptes");
        $facturesEnCours = $this->documentTypeFilter($documents, "facturesEnCours");
        $facturesEnvoyees = $this->documentTypeFilter($documents, "facturesEnvoyees");
        $facturesPayees = $this->documentTypeFilter($documents, "facturesPayees");

        return $this->render('main/index.html.twig', [
            'clients' => $clients, 
            'devisEnCours' => $devisEnCours,
            'devisEnvoyes' => $devisEnvoyes,
            'devisAcceptes' => $devisAcceptes,
            'facturesEnCours' => $facturesEnCours,
            'facturesEnvoyees' => $facturesEnvoyees,
            'facturesPayees' => $facturesPayees,
        ]);
    }

    private function documentTypeFilter($documents, $type)
    {
        $documentsFiltres = array_filter($documents, function ($document) use ($type) {
            return $document->getType() === $type;
        });
        return $documentsFiltres;
    }
}
