<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentRepository;
use App\Entity\Document;

class DesignationController extends AbstractController
{
    #[Route('/designation/{id}', name: 'app_designation_add')]
    public function add(int $id, Request $request, EntityManagerInterface $entityManager, DocumentRepository $documentRepository): Response
    {
        $document = $documentRepository->getDocumentById($id);



        return $this->render('designation/adddesignations.html.twig', [
            'document' => $document,

        ]);
    }
}
