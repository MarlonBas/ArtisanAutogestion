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

class DesignationController extends AbstractController
{
    #[Route('/designation{id}', name: 'app_designation_add')]
    public function add(int $id, Request $request, EntityManagerInterface $entityManager, DocumentRepository $documentRepository): Response
    {
        $document = $documentRepository->find($id);
        $date = $document->getDate()->format('d-m-Y');
        $designations = $document->getDesignations()->toArray();
        $total = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $total = $total + $designations[$i]->getPrixTotal();
        }
        
        $form = $this->createForm(DesignationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $designation = $form->getData();
            
            $designation->setDocument($document);
            $designation->setPrixHorsTax($designation->getPrixUnitaire()*$designation->getQuantite());
            $designation->setPrixTotal($designation->getPrixHorsTax()+$designation->getPrixHorsTax()*(1/$designation->getTva()));

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
