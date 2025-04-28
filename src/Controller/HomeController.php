<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface; 
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\ReclamationRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/se', name: 'se_connecter')]
    public function se_connecter(): Response
    {
        return $this->render('front/utilisateur/SignUp.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    private $entityManager;

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        
        $error = $authenticationUtils->getLastAuthenticationError();
       
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Front/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/front/reclamations', name: 'Reclamation')]
    public function indexReclamation(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        // Récupère le CIN stocké en session
        $cin = $request->getSession()->get('Cin_Utilisateu');
        if (!$cin) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réclamations.');
        }

        // Filtre les réclamations par CIN
        $reclamations = $reclamationRepository->findBy(['Cin_Utilisateu' => $cin]);

        return $this->render('Front/Reclamation/Reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    
    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        
    }
    
}
