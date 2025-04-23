<?php

namespace App\Controller\UtilisateurController;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\UtilisateurRepository;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        // Si l'utilisateur est déjà connecté, envoyez un email et redirigez-le
        if ($this->getUser()) {
            $user = $this->getUser(); // Récupère l'utilisateur connecté

            // Envoyer un email à l'utilisateur connecté
            $email = (new Email())
                ->from('achrefkachai023@gmail.com') // Adresse de l'expéditeur
                ->to($user->getEmail()) // Adresse de l'utilisateur connecté
                ->subject('Connexion réussie')
                ->text(sprintf('Bonjour %s, vous vous êtes connecté avec succès à votre compte.', $user->getCin()));
            $mailer->send($email);
            // Redirigez l'utilisateur en fonction de son rôle
            return $this->redirectBasedOnRole($user);
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
        $user = $this->getUser();
        $cin = $user->getCin(); // Récupère le CIN de l'utilisateur connecté
        return $this->render('back/dashboard.html.twig', [
            'controller_name' => 'Admin Dashboard',
        ]);
    }

    #[Route('/participant/dashboard', name: 'participant_dashboard')]
    public function participantDashboard(UtilisateurRepository $utilisateurRepository ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT'); // Vérifie que l'utilisateur a le rôle ROLE_PARTICIPANT
        $organisateurs = $utilisateurRepository->findBy(['Role' => 'Organisateur']);
        return $this->render('front/utilisateur/participant.html.twig', [
        'organisateurs' => $organisateurs,
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