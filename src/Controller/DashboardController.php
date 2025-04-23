<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('back/Dashboard.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
    
   
/*
    #[Route('/dashboard/utilisateur', name: 'utilisateur')]
    public function charts(): Response
    {
        return $this->render('back/utilisateur/Utilisateur.html.twig');
    }
    
    #[Route('/dashboard/utilisateurs', name: 'app_user_index')]
    public function utilisateurindex(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findAll();

        return $this->render('back/utilisateur/Utilisateur.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/dashboard/utilisateursd', name: 'InterfaceClient')]
    public function InterfaceClient(): Response
    {
        return $this->render('../front/home/index.html.twig');
    }*/

}
