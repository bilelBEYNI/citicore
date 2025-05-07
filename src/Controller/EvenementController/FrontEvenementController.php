<?php

namespace App\Controller\EvenementController;

use App\Service\EventImageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EvenementRepository;
use App\Repository\CategorieRepository;

final class FrontEvenementController extends AbstractController
{
    #[Route('/evenements', name: 'app_evenement_front')]
    public function front(
        EventImageGenerator $eventImageGenerator, 
        EvenementRepository $evenementRepository,
        CategorieRepository $categorieRepository,
        Request $request
    ): Response
    {
        // Récupération de la catégorie sélectionnée dans les paramètres de la requête
        $categorieId = $request->query->get('categorie');

        // Récupération des événements filtrés par catégorie si nécessaire
        if ($categorieId) {
            $evenements = $evenementRepository->findBy(['categorie' => $categorieId], ['date_evenement' => 'ASC']);
        } else {
            $evenements = $evenementRepository->findBy([], ['date_evenement' => 'ASC']);
        }

        // Récupération de toutes les catégories
        $categories = $categorieRepository->findAll();
        
        // Création de la liste des événements avec images
        $evenementsAvecImages = [];
        foreach ($evenements as $event) {
            $imagePath = $eventImageGenerator->generate($event->getNomEvenement());
            $evenementsAvecImages[] = [
                'event'    => $event,
                'imageUrl' => $imagePath,
            ];
        }

        return $this->render('Front/evenement/FRONTevenement.html.twig', [
            'evenementsAvecImages' => $evenementsAvecImages,
            'categories' => $categories,
            'currentCategorieId' => $categorieId, // Passer l'ID de la catégorie sélectionnée
        ]);
    }

    #[Route('/evenements/json', name: 'app_evenement_json')]
    public function getEvenementsJson(EvenementRepository $evenementRepository): Response
    {
        $evenements = $evenementRepository->findBy([], ['date_evenement' => 'ASC']);
        $data = array_map(function($event) {
            return [
                'id' => $event->getId_Evenement(),
                'title' => $event->getNomEvenement(),
                'start' => $event->getDateEvenement() ? $event->getDateEvenement()->format('Y-m-d\TH:i:s') : null,
                'url' => $this->generateUrl('app_evenement_show', ['id' => $event->getId_Evenement()]),
                'extendedProps' => [
                    'lieu' => $event->getLieuEvenement(),
                    'categorie' => $event->getCategorie() ? $event->getCategorie()->getNomCategorie() : null,
                ]
            ];
        }, $evenements);

        return $this->json($data);
    }

    #[Route('/evenements/calendrier', name: 'app_evenement_calendrier')]
    public function calendrier(): Response
    {
        return $this->render('Front/evenement/CalenderEvenement.html.twig');
    }

    #[Route('/evenements/afficher', name: 'app_evenement_afficher')]
    public function afficherEvenements(
        EventImageGenerator $imageGenerator,
        EvenementRepository $evenementRepository
    ): Response
    {
        // Récupère tous les événements
        $evenements = $evenementRepository->findAll();

        $evenementsAvecImages = [];
        foreach ($evenements as $event) {
            // Génère ou récupère l'image locale via EventImageGenerator
            $path = $imageGenerator->generate($event->getNomEvenement());
            // Le chemin renvoyé commence par '/uploads/events/...'
            $evenementsAvecImages[] = [
                'event'    => $event,
                'imageUrl' => $path,
            ];
        }

        return $this->render('Front/evenement/FRONTevenement.html.twig', [
            'evenementsAvecImages' => $evenementsAvecImages,
        ]);
    }
    #[Route('/evenements/filtrer', name: 'app_evenement_filtrer')]
public function filterByCategory(Request $request, EvenementRepository $evenementRepository): Response
{
    // Récupérer l'ID de la catégorie sélectionnée
    $categorieId = $request->query->get('categorieId');
    
    // Récupérer les événements filtrés par catégorie
    $evenements = $categorieId ? 
        $evenementRepository->findBy(['categorie' => $categorieId], ['date_evenement' => 'ASC']) :
        $evenementRepository->findBy([], ['date_evenement' => 'ASC']); // Tous les événements

    $evenementsAvecImages = [];
    foreach ($evenements as $event) {
        // Génère ou récupère l'image de l'événement
        $imagePath = '/uploads/events/' . $event->getNomEvenement() . '.jpg'; // Adapte à ton générateur d'image
        $evenementsAvecImages[] = [
            'title'       => $event->getNomEvenement(),
            'start'       => $event->getDateEvenement()->format('d/m/Y H:i'),
            'lieu'        => $event->getLieuEvenement(),
            'categorie'   => $event->getCategorie() ? $event->getCategorie()->getNomCategorie() : 'Aucune',
            'description' => $event->getDescriptionEvenement(),
            'imageUrl'    => $imagePath,
            'url'         => $this->generateUrl('app_evenement_show', ['id' => $event->getId_Evenement()])
        ];
    }

    return $this->json($evenementsAvecImages);
}

}
