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
use App\Form\ArchiveSearchType; 

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
        $documents = $user->getDocuments()->toArray();

        $devisEnCours = $this->documentTypeFilter($documents, "devisEnCours");
        $devisEnvoyes = $this->documentTypeFilter($documents, "devisEnvoyes");
        $devisAcceptes = $this->documentTypeFilter($documents, "devisAcceptes");
        $facturesEnCours = $this->documentTypeFilter($documents, "facturesEnCours");
        $facturesEnvoyees = $this->documentTypeFilter($documents, "facturesEnvoyees");
        $facturesPayees = $this->documentTypeFilter($documents, "facturesPayees");

        return $this->render('document/indexdocument.html.twig', [
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

    #[Route('/document/archiveindex', name: 'app_document_archiveindex')]
    public function archiveIndex(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();

        $documents = $user->getDocuments()->toArray();
        $devis = $this->documentTypeFilter($documents, "devisArchive");
        $factures = $this->documentTypeFilter($documents, "factureArchive");
        
        $form = $this->createForm(ArchiveSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();
            //$form = $this->createForm(ArchiveSearchType::class, $search);
            $devis = $this->documentSearchFilter($devis, $search);
            $factures = $this->documentSearchFilter($factures, $search);
        }

        return $this->render('document/archivedocument.html.twig', [
            'devis' => $devis,
            'factures' => $factures,
            'form' => $form,
        ]);
    }

    private function documentSearchFilter($documents, $search)
    {
        $documentsFiltres = array_filter($documents, function ($document) use ($search) {
            $find = true;
            if ($search->getSearchString() != null) {
                if (strpos(strtolower($document->getClient()->getNom()), strtolower($search->getSearchString())) !== false) {
                    $find = true;
                }
                else if (strpos(strtolower($document->getNumero()), strtolower($search->getSearchString())) !== false) {
                    $find = true;
                }
                else {
                    $find = false;
                }
            }
            if ($document->getDate() < $search->getDateStart()) {
                $find = false;
            }
            else if ($document->getDate() > $search->getDateEnd()) {
                $find = false;
            }
            return $find;
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

        $count = count($user->getDocuments()->toArray());


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->getData();
            $document->setUser($user);
            if ($user->getParametres()->isModeMicro()) {
                $document->setTva(0);
            }
            $document->setNumero($document->getClient()->getId().$document->getDate()->format('ym').$count.$user->getId());
            $entityManager->persist($document);
            $entityManager->flush();
            return $this->redirectToRoute('app_designation_add', ['id' => $document->getId()]);
        }
        return $this -> render('document/adddocument.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    #[Route('/document/remove{id}', name: 'app_document_remove')]
    public function remove($id, DocumentRepository $documentRepository, EntityManagerInterface $entityManager): Response
    {
        $document = $documentRepository->find($id);
        $entityManager->remove($document);
        $entityManager->flush();
        return $this->redirectToRoute('app_document_index');
    }

    #[Route('/document/archive{id}', name: 'app_document_archive')]
    public function archive($id, DocumentRepository $documentRepository, EntityManagerInterface $entityManager): Response
    {
        $document = $documentRepository->find($id);
        $type = $document->getType();
        if (strpos($type, "devis") !== false) {
            $newType = "devisArchive";
        }
        if (strpos($type, "facture") !== false) {
            $newType = "factureArchive";
        }
        $document->setType($newType);
        $entityManager->persist($document);
        $entityManager->flush();
        return $this->redirectToRoute('app_document_index');
    }

    #[Route('/document/edit{id}', name: 'app_document_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, DocumentRepository $documentRepository): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
       
        $document = $documentRepository->find($id);
        $date = $document->getDate()->format('d-m-Y');
        $type = $document->getType();
        if (strpos($type, "devis") !== false) {
            $typeName = "Devis";
        }
        if (strpos($type, "facture") !== false) {
            $typeName = "Facture";
        }
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $document = $form->getData();
            $document->setUser($user);
            if ($user->getParametres()->isModeMicro()) {
                $document->setTva(0);
            }
            $entityManager->persist($document);
            $entityManager->flush();
            if ($document->getType() == "devisEnCours") {
            $this->addFlash('success', "Le devis à été modifié avec succès");
            }
            if ($document->getType() == "factureEnCours") {
            $this->addFlash('success', "La facture à été modifié avec succès");
            }
            return $this->redirectToRoute('app_designation_add', ['id' => $document->getId()]);
        }
        return $this->render('document/editdocument.html.twig', [
            'document' => $document,
            'date' => $date,
            'type' => $typeName,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/document{id}', name: 'app_document_show')]
    public function show(int $id, DocumentRepository $documentRepository): Response
    {
        $document = $documentRepository->find($id);
        $date = $document->getDate()->format('d-m-Y');
        $type = $document->getType();
        $edit = false;
        if ($type == "devisEnCours" || $type == "facturesEnCours") {
            $edit = true;
        }
        if (strpos($type, "devis") !== false) {
            $typeName = "Devis";
        }
        if (strpos($type, "facture") !== false) {
            $typeName = "Facture";
        }

        $designations = $document->getDesignations()->toArray();
        $totalHT = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $totalHT = $totalHT + $designations[$i]->getPrixHorsTax();
        }
        $totalTTC = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $totalTTC = $totalTTC + $designations[$i]->getPrixTotal();
        }

        return $this->render('document/viewdocument.html.twig', [
            'document' => $document,
            'date' => $date,
            'edit' => $edit,
            'type' => $typeName,
            'designations' => $designations,
            'totalHT' => $totalHT,
            'totalTTC' => $totalTTC,
        ]);
    }

    #[Route('/document/move{id}/{direction}', name: 'app_document_move')]
    public function move(int $id, String $direction, DocumentRepository $documentRepository, EntityManagerInterface $entityManager)
    {
        $document = $documentRepository->find($id);
        if ($direction == "right") {
            if ($document->getType() == "devisEnCours") {
                $newtype = "devisEnvoyes";
            }
            if ($document->getType() == "devisEnvoyes") {
                $newtype = "devisAcceptes";
            }
            if ($document->getType() == "devisAcceptes") {
                $newDocument = $document->cloneDocument();
                $newDocument->setType("facturesEnCours");
                $entityManager->persist($newDocument);
                $entityManager->flush();
                return $this->redirectToRoute('app_document_index');
            }
            if ($document->getType() == "facturesEnCours") {
                $newtype = "facturesEnvoyees";
            }
            if ($document->getType() == "facturesEnvoyees") {
                $newtype = "facturesPayees";
            }
        }
        if ($direction == "left") {
            if ($document->getType() == "devisEnvoyes") {
                $newtype = "devisEnCours";
            }
            if ($document->getType() == "devisAcceptes") {
                $newtype = "devisEnvoyes";
            }
            if ($document->getType() == "facturesEnCours") {
                return $this->redirectToRoute('app_document_remove', ['idDocumentRepository $documentRepository)' => $document->getId()]);
            }
            if ($document->getType() == "facturesEnvoyees") {
                $newtype = "facturesEnCours";
            }
            if ($document->getType() == "facturesPayees") {
                $newtype = "facturesEnvoyees";
            }
        }
        $document->setType($newtype);
        $entityManager->persist($document);
        $entityManager->flush();
        return $this->redirectToRoute('app_document_index');
    }

    #[Route('/document/copy{id}', name: 'app_document_copy')]
    public function copy(int $id, DocumentRepository $documentRepository, EntityManagerInterface $entityManager) 
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();

        $count = count($user->getDocuments()->toArray());

        $document = $documentRepository->find($id);
        $newDocument = $document->cloneDocument();
        if (strpos($document->getType(), "devis") !== false) {
            $newDocument->setType("devisEnCours");
        }
        if (strpos($document->getType(), "facture") !== false) {
            $newDocument->setType("facturesEnCours");
        }
        $newDocument->setNumero($document->getClient()->getId().$document->getDate()->format('ym').$count.$user->getId());
        $entityManager->persist($newDocument);
        $entityManager->flush();
        return $this->redirectToRoute('app_document_index');
    }
}
