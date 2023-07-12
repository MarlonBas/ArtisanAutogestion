<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Entity\Document;
use App\Entity\User;

class DocumentController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/document/create', name: 'app_document_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DocumentType::class);
        $document = new Document();

        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
       

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->getData();
            $document->setUser($user);
            $document->setNumero($document->getDate()->format('ym'));
            $entityManager->persist($document);
            $entityManager->flush();
            if ($document->getType() == ("devisEnCours")) {
            $this->addFlash('success', "Le devis à été enregistré avec succès");
            }
            if ($document->getType() == ("factureEnCours")) {
            $this->addFlash('success', "La facture à été enregistré avec succès");
            }
            return $this->redirectToRoute('app_main');
        }
        return $this -> render('document/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/document/edit{numero}', name: 'app_document_edit', requirements: ['name' => '[a-zA-Z\s.,/]+'])]
    public function edit(): Response
    {
        return $this->render('document/edit.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('/document{numero}', name: 'app_document_show', requirements: ['name' => '[a-zA-Z\s.,/]+'])]
    public function show(): Response
    {
        return $this->render('document/show.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }
}
