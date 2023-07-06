<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    #[Route('/document/create', name: 'app_document_create')]
    public function create(): Response
    {
        return $this->render('document/create.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
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
