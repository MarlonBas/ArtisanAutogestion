<?php

namespace App\Controller;

use App\Entity\Designation;
use App\Form\DesignationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentRepository;
use App\Repository\DesignationRepository;
use App\Entity\Document;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DesignationController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/designation{id}', name: 'app_designation_add')]
    public function add(int $id, Request $request, EntityManagerInterface $entityManager, DocumentRepository $documentRepository): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
        $micro = $user->getParametres()->isModeMicro();

        $document = $documentRepository->find($id);
        $date = $document->getDate()->format('d-m-Y');
        $designations = $document->getDesignations()->toArray();
        $total = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $total = $total + $designations[$i]->getPrixTotal();
        }
        $totalHT = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $totalHT = $totalHT + $designations[$i]->getPrixHorsTax();
        }
        
        $form = $this->createForm(DesignationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $designation = $form->getData();
            $designation->setDocument($document);
            $designation->setTva($document->getTva());
            $designation->setPrixHorsTax($designation->getPrixUnitaire()*$designation->getQuantite());
            if ($designation->getUnite() == null) {
                $designation->setUnite(" ");
            }
            if ($designation->getTva() == null) {
                $designation->setTva(0);
            }

            if ($micro != true && $designation->getTva() > 0) {
                $designation->setPrixTotal($designation->getPrixHorsTax()+$designation->getPrixHorsTax()*(1/$designation->getTva()));
            }
            if ($micro == true && $designation->getTva() == 0) {
                $designation->setPrixTotal($designation->getPrixHorsTax());
            }

            $entityManager->persist($designation);
            $entityManager->flush();
            return $this->redirectToRoute('app_designation_add', ['id' => $id]);
        }

        return $this->render('designation/adddesignations.html.twig', [
            'document' => $document,
            'date' => $date,
            'designations' => $designations,
            'form' => $form,
            'total' => $total,
            'totalHT' => $totalHT,
            'micro' => $micro,
        ]);
    }

    #[Route('/designation/remove{id}', name: 'app_designation_remove')]
    public function remove(int $id, DesignationRepository $designationRepository,EntityManagerInterface $entityManager) {
        $designation = $designationRepository->find($id);
        $entityManager->remove($designation);
        $entityManager->flush();
        return $this->redirectToRoute('app_designation_add', ['id' => $designation->getDocument()->getId()]);
    }
}
