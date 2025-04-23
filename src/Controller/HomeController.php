<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface; 
use App\Entity\Utilisateur;
use App\Repository\DemandeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        return $this->render('front/utilisateur/Login.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    private $entityManager;

    // Injecter l'EntityManagerInterface dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/se/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $cin = $request->request->get('Cin');
            $password = $request->request->get('password');
    
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['Cin' => $cin]);
    
            if ($user && $user->getMotDePasse() === $password) {
                if (in_array('Admin', $user->getRoles())) {
                    return $this->redirectToRoute('back/utilisateur/Utilisateur.html.twig');
                } else {
                    return $this->redirectToRoute('/');
                }
            }
    
            $this->addFlash('error', 'CIN ou mot de passe incorrect.');
        }
    
        // Affiche la page login.html.twig
        return $this->render('back/utilisateur/Utilisateur.html.twig');
    }
    #[Route('/demandes', name: 'app_demande_front')]
    public function front(DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findAll();

        return $this->render('Front/Communication/demandefront.html.twig', [
            'demandes' => $demandes,
        ]);
    }
    
}
