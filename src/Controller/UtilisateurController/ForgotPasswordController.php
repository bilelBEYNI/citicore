<?php

namespace App\Controller\UtilisateurController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, SessionInterface $session): Response
    {
        // Vérifier si on est à l'étape 2
        $step = 1;
        if ($request->isMethod('POST')) {
            // Si on est à l'étape 1 (demande CIN)
            if (!$session->get('reset_code')) {
                // Étape 1 : Récupérer le CIN et envoyer le code de vérification
                $cin = $request->request->get('cin');
                $resetCode = rand(100000, 999999); // Code de vérification aléatoire
                $session->set('reset_code', $resetCode);
                $session->set('cin', $cin);
                // Envoi du code par email (ajouter un vrai envoi dans une vraie application)
                
                $step = 2;
            } else {
                // Étape 2 : Vérification du code et changement de mot de passe
                $enteredCode = $request->request->get('verification_code');
                $newPassword = $request->request->get('new_password');
                
                $storedResetCode = $session->get('reset_code');
                $cin = $session->get('cin');

                if ($enteredCode == $storedResetCode) {
                    // Réinitialiser le mot de passe ici
                    // Logique pour mettre à jour le mot de passe de l'utilisateur

                    // Supprimer le code et CIN de la session
                    $session->remove('reset_code');
                    $session->remove('cin');
                    
                    return $this->redirectToRoute('login');
                } else {
                    $step = 2;
                    $error = "Le code de vérification est incorrect.";
                }
            }
        }

        return $this->render('Front/security/forgot_password.html.twig', [
            'step' => $step,
            'cin' => $session->get('cin'),
            'error' => $error ?? null,
        ]);
    }
}