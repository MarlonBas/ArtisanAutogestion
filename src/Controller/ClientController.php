<?php

namespace App\Controller;

use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ClientController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/client', name: 'app_client_index')]
    public function index(): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();

        $clients = $user->getClients()->toArray();

        return $this->render('client/indexclient.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/client/add', name: 'app_client_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class);
        $client = new Client();

        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
       

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            $client->setUser($user);
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', "Le client a été ajouté avec succès");
            return $this->redirectToRoute('app_main');
        }
        return $this -> render('client/addclient.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/client/show{name}', name: 'app_client_show', requirements: ['name' => '[a-zA-Z\s.,/]+'])]
    public function show(String $name, ClientRepository $clientRepository): Response
    {
        $client= $clientRepository->findOneByNom($name);
        if (!$client) {
            throw $this->createNotFoundException("ERREUR: les données client sont introuvable");
        }

        $documents = $client->getDocuments()->toArray();

        return $this -> render('client/viewclient.html.twig', ['client' => $client, 'documents' => $documents]);
    }

    #[Route('/client/edit{name}', name: 'app_client_edit', requirements: ['name' => '[a-zA-Z\s.,/]+'])]
    public function edit(String $name, Request $request, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response
    {
        $client = $clientRepository->findOneByNom($name);
        if (!$client) {
            throw $this->createNotFoundException("ERREUR: les données client sont introuvable");
        }
        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', "Les informations du client ont étés modifiés avec succès");
            return $this->render('client/editclient.html.twig', [
                'form' => $form->createView(), 'client' => $client]);
        }
        return $this->render('client/editclient.html.twig', [
            'form' => $form->createView(), 'client' => $client]);
    }

    #[Route('/client/remove{name}', name: 'app_client_remove', requirements: ['name' => '[a-zA-Z\s.,/]+'])]
    public function remove($name, ClientRepository $clientRepository, EntityManagerInterface $entityManager) {
        $client = $clientRepository->findOneByNom($name);
        $entityManager->remove($client);
        $entityManager->flush();
        return $this->redirectToRoute('app_client_index');
    }
}
