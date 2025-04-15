<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetDonRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;

final class FrontProjetController extends AbstractController
{
    #[Route('/front/projet', name: 'app_front_projet')]
    public function index(Request $request, ProjetDonRepository $projetDonRepository): Response
    {
        $search = $request->query->get('search');  // Get the search query from the URL

        if ($search) {
            // If a search query is provided, filter projects by name (or other criteria)
            $projets = $projetDonRepository->findByName($search);
        } else {
            // If no search query is provided, return all projects
            $projets = $projetDonRepository->findAll();
        }

        return $this->render('front_projet/index.html.twig', [
            'projets' => $projets,
        ]);
    }
    #[Route('/front/projet/{id}/donner', name: 'app_projet_donner')]
public function donner(Request $request, ProjetDonRepository $projetDonRepository, EntityManagerInterface $em, int $id): Response
{
    $projet = $projetDonRepository->find($id);

    if (!$projet) {
        throw $this->createNotFoundException('Projet non trouvé.');
    }

    if ($request->isMethod('POST')) {
        $montant = floatval($request->request->get('montant'));
        if ($montant > 0) {
            $projet->setMontantRecu($projet->getMontantRecu() + $montant);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre don !');
            return $this->redirectToRoute('app_projet_donner', ['id' => $id]);
        }
    }

    return $this->render('front_projet/donner.html.twig', [
        'projet' => $projet
    ]);
}
#[Route('/front/projet/nosassociation', name: 'front_projet_nosassociation', methods: ['GET'])]
    public function nosAssociations(Request $request, AssociationRepository $associationRepository): Response
    {
        // Get the search query from the request
        $searchQuery = $request->query->get('search');

        // If there is a search query, filter the associations
        if ($searchQuery) {
            $associations = $associationRepository->findBySearch($searchQuery);
        } else {
            // Otherwise, get all associations
            $associations = $associationRepository->findAll();
        }

        return $this->render('front_projet/nosassociation.html.twig', [
            'associations' => $associations,
        ]);
    }
    #[Route('/front/chatbot', name: 'chatbot_page')]
public function chatbot(): Response
{
    return $this->render('front_projet/chatbot.html.twig');
}



}
