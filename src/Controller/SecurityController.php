<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirigez-le selon son rôle
        if ($this->getUser()) {
            return $this->redirectBasedOnRole($this->getUser());
        }

        // Récupérer l'erreur et le dernier CIN
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastCin = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_cin' => $lastCin,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion
    }

    private function redirectBasedOnRole($user): Response
    {
        $roles = $user->getRoles(); // Récupère les rôles de l'utilisateur
        dump($roles); // Affiche les rôles dans la barre de débogage Symfony

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->redirectToRoute('admin_dashboard'); // Redirige vers le tableau de bord Admin
        }

        if (in_array('ROLE_PARTICIPANT', $roles, true)) {
            return $this->redirectToRoute('participant_dashboard'); // Redirige vers le tableau de bord Participant
        }

        // Si aucun rôle spécifique n'est trouvé, redirige vers une page par défaut
        return $this->redirectToRoute('home');
    }
}



