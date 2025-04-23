<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
     #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Vérifie que l'utilisateur a le rôle ROLE_ADMIN

        return $this->render('back/dashboard.html.twig', [
            'controller_name' => 'Admin Dashboard',
        ]);
    }

    #[Route('/participant/dashboard', name: 'participant_dashboard')]
public function afficherOrganisateurs(UtilisateurRepository $utilisateurRepository, FeedbackRepository $feedbackRepository): Response
{
    // Récupérer les organisateurs
    $organisateurs = $utilisateurRepository->findBy(['Role' => 'Organisateur']);

    // Récupérer les feedbacks pour chaque organisateur
    foreach ($organisateurs as $organisateur) {
        $organisateur->feedbacks = $feedbackRepository->findBy(['Cin_Organisateur' => $organisateur->getCin()]);
    }

    return $this->render('front/utilisateur/participant.html.twig', [
        'organisateurs' => $organisateurs,
    ]);
}


    #[Route('/redirect-after-login', name: 'redirect_after_login')]
    public function redirectAfterLogin(): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('admin_dashboard'); // Redirige vers le tableau de bord Admin
        }

        if (in_array('ROLE_PARTICIPANT', $user->getRoles(), true)) {
            return $this->redirectToRoute('participant_dashboard'); // Redirige vers le tableau de bord Participant
        }

        return $this->redirectToRoute('home'); // Redirige vers la page d'accueil par défaut
    }
}
