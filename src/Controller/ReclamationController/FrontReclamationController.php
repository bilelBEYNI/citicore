<?php

namespace App\Controller\ReclamationController;

use App\Entity\Utilisateur;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/reclamation' )]
class FrontReclamationController extends AbstractController
{
    #[Route('', name: 'front_reclamation_index', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        dump($this->getUser());
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $user = $this->getUser();
        $cin = $user->getCin();
        if (!$cin) {
            throw $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        }
        
        // Filtre les réclamations par CIN
        $reclamations = $reclamationRepository->findBy(['Cin_Utilisateur' => $cin]);

        return $this->render('front/Reclamation/Reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'front_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $user = $this->getUser();
        $cin = $user->getCin();
    

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
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $user = $this->getUser();
        $cin = $user->getCin();
        if (!$cin || $reclamation->getCin_Utilisateur() !== $cin) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette réclamation.');
        }

        return $this->render('front/Reclamation/Reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
