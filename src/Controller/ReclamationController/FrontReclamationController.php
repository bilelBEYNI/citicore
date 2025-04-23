<?php

namespace App\Controller\ReclamationController;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/reclamation')]
class FrontReclamationController extends AbstractController
{
    #[Route('', name: 'front_reclamation_index', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        // Récupère le CIN stocké en session
        $cin = $request->getSession()->get('Cin_Utilisateu');
        if (!$cin) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réclamations.');
        }

        // Filtre les réclamations par CIN
        $reclamations = $reclamationRepository->findBy(['Cin_Utilisateu' => $cin]);

        return $this->render('front/Reclamation/Reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'front_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère le CIN en session
        $cin = $request->getSession()->get('Cin_Utilisateu');
        if (!$cin) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter une réclamation.');
        }

        // Création de la réclamation et attribution du CIN
        $reclamation = new Reclamation();
        $reclamation->setCin_Utilisateur($cin);

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('front_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/Reclamation/Reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'front_reclamation_show', methods: ['GET'])]
    public function show(Request $request, Reclamation $reclamation): Response
    {
        // Vérifie que la réclamation appartient à l'utilisateur via la session
        $cin = $request->getSession()->get('Cin_Utilisateu');
        if (!$cin || $reclamation->getCin_Utilisateur() !== $cin) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette réclamation.');
        }

        return $this->render('front/Reclamation/Reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
