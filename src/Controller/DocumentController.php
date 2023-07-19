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

    #[Route('/document', name: 'app_document_index')]
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

        return $this->render('document/indexdocument.html.twig', [
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

    #[Route('/document/create', name: 'app_document_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
       
        $form = $this->createForm(DocumentType::class);
        $document = new Document();

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
            return $this->redirectToRoute('app_designation_add', ['id' => $document->getId()]);
        }
        return $this -> render('document/adddocument.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/document/edit{id}', name: 'app_document_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('document/editdoucment.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('/document{id}', name: 'app_document_show')]
    public function show(int $id, DocumentRepository $documentRepository): Response
    {
        $document = $documentRepository->find($id);
        return $this->render('document/viewdocument.html.twig', [
            'document' => $document,
        ]);
    }
}
