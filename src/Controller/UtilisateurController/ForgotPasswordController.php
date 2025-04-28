<?php
// src/Controller/UtilisateurController/ForgotPasswordController.php
namespace App\Controller\UtilisateurController;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(
        Request $request,
        SessionInterface $session,
        MailerInterface $mailer,
        EntityManagerInterface $em
    ): Response {
        $step  = 1;
        $cin   = $session->get('reset_cin');
        $error = null;

        if ($request->isMethod('POST')) {
            // --- Étape 1 : saisie du CIN ---
            if ($request->request->get('cin')) {
                $cin = trim($request->request->get('cin'));

                // Récupérer l'utilisateur pour son email
                $user = $em->getRepository(Utilisateur::class)
                           ->findOneBy(['Cin' => $cin]);

                if (!$user) {
                    $error = 'Aucun utilisateur trouvé avec ce CIN.';
                } else {
                    // Générer & stocker le code
                    $code = random_int(100000, 999999);
                    $session->set('reset_cin',   $cin);
                    $session->set('reset_code',  $code);
                    $session->set('reset_email', $user->getEmail());

                    // Envoyer le mail de vérification
                    $emailMessage = (new Email())
                        ->from('noreply@votre-domaine.tld')
                        ->to($user->getEmail())
                        ->subject('Votre code de vérification')
                        ->text(sprintf(
                            "Bonjour %s,\n\nVotre code de vérification est : %d\n\nCordialement.",
                            $user->getNom() ?? $cin,
                            $code
                        ));
                    $mailer->send($emailMessage);

                    $step = 2;
                }
            }
            // --- Étape 2 : saisie du code et nouveau mot de passe ---
            elseif ($request->request->get('verification_code')) {
                $enteredCode = (int) trim($request->request->get('verification_code'));
                $storedCode  = (int) $session->get('reset_code');

                if ($enteredCode !== $storedCode) {
                    $error = 'Le code de vérification est incorrect.';
                    $step  = 2;
                } else {
                    // Récupérer l'utilisateur
                    $cin  = $session->get('reset_cin');
                    $user = $em->getRepository(Utilisateur::class)
                               ->findOneBy(['Cin' => $cin]);

                    if (!$user) {
                        $error = 'Utilisateur introuvable.';
                        $step  = 2;
                    } else {
                        // Hasher et mettre à jour le mot de passe
                        $newPassword = $request->request->get('new_password');
                        $hashed      = password_hash($newPassword, PASSWORD_BCRYPT);
                        $user->setMotDePasse($hashed);

                        // Sauvegarder en base
                        $em->flush();

                        // Nettoyer la session
                        $session->remove('reset_cin');
                        $session->remove('reset_code');
                        $session->remove('reset_email');

                        $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                        return $this->redirectToRoute('login');
                    }
                }
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'step'  => $step,
            'cin'   => $cin,
            'error' => $error,
        ]);
    }
}
