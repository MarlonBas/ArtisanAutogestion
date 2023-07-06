<?php

namespace App\Controller;

use App\Form\ParametresFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class ParametresController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/parametres', name: 'app_parametres')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();
        $parametres = $user->getParametres();

        if (!$parametres) {
            $form = $this->createForm(ParametresFormType::class);
        } else {
            $form = $this->createForm(ParametresFormType::class, $parametres);
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parametres = $form->getData();
            $parametres->setUser($user);
            /*$user->setParametres($parametres);*/
            $entityManager->persist($parametres);
            /*$entityManager->persist($user);*/
            $entityManager->flush();
            $this->addFlash('success', "Paramétres modifiés avec succès");
            return $this->render('parametres/index.html.twig', [
                'form' => $form->createView(),]);
        }
        return $this->render('parametres/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
