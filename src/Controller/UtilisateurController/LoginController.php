<?php

namespace App\Controller\UtilisateurController;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirigez-le en fonction de son rôle
        if ($this->getUser()) {
            return $this->redirectBasedOnRole($this->getUser());
        }

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastCin = $authenticationUtils->getLastUsername();

        // Créer le formulaire
        $form = $this->createForm(LoginFormType::class, [
            'cin' => $lastCin, // Pré-remplir le champ CIN avec la dernière valeur saisie
        ]);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Vérifie que l'utilisateur a le rôle ROLE_ADMIN

        return $this->render('back/dashboard.html.twig', [
            'controller_name' => 'Admin Dashboard',
        ]);
    }

    #[Route('/participant/dashboard', name: 'participant_dashboard')]
    public function participantDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT'); // Vérifie que l'utilisateur a le rôle ROLE_PARTICIPANT
        //$user = $this->getUser(); // Récupère l'utilisateur connecté
        //$cin = $user->getCin(); // Récupère le CIN de l'utilisateur connecté

        return $this->render('front/utilisateur/participant.html.twig', [
            'controller_name' => 'Participant Dashboard',
        ]);
    }

    private function redirectBasedOnRole($user): Response
    {
        $roles = $user->getRoles(); // Récupère les rôles de l'utilisateur
        dump($roles); // Debug : affiche les rôles dans la barre de débogage Symfony

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->redirectToRoute('admin_dashboard');
        }

        if (in_array('ROLE_PARTICIPANT', $roles, true)) {
            return $this->redirectToRoute('participant_dashboard');
        }

        return $this->redirectToRoute('home');
    }
}