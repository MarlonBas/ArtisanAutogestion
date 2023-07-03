<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/add', name: 'app_client_show')]
    public function add(): Response
    {
        
        return $this -> render('client/add.html.twig');
    }

    #[Route('/client/show{name}', name: 'app_client_show', requirements: 'a-zA-Z\s.,')]
    public function show(String $name, ClientRepository $clientRepository): Response
    {
        $client= $clientRepository->findOneByName($name);
        if (!$client) {
            throw $this->createNotFoundException("ERREUR: les données client sont introuvable");
        }
        return $this -> render('client/infoclient.html.twig', ['client' => $client]);
    }

    #[Route('/client/edit{name}', name: 'app_client_edit', requirements: 'a-zA-Z\s.,')]
    public function edit(String $name, Request $request, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response
    {
        $client = $clientRepository->findOneByName($name);
        if (!$client) {
            throw $this->createNotFoundException("ERREUR: les données client sont introuvable");
        }
        $form = $this->createForm(ClientFormType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->addFlash('success', "Les info clients ont étés modifiés");
            $entityManager->flush();
            return $this->render('client/edit.html.twig', [
                'clientForm' => $form->createView(), 'client' => $client]);
        }
        return $this->render('client/edit.html.twig', [
            'clientForm' => $form->createView(), 'client' => $client]);
    }
}
