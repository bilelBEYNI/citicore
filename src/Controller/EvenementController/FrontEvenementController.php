<?php

namespace App\Controller\EvenementController;
use App\Service\CustomOpenAiClient; 
use App\Service\EventImageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementRepository;
use App\Repository\CategorieRepository;
use OpenAI\OpenAI;
use Psr\Cache\CacheItemPoolInterface;

final class FrontEvenementController extends AbstractController
{
    #[Route('/evenements', name: 'app_evenement_front')]
    public function front(
        EventImageGenerator $eventImageGenerator, 
        EvenementRepository $evenementRepository,
        CategorieRepository $categorieRepository
    ): Response
    {
        $evenements = $evenementRepository->findBy([], ['date_evenement' => 'ASC']);
        $categories = $categorieRepository->findAll();
        
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
    public function afficherEvenements(): Response
    {
        $client = OpenAI::client($_SERVER['OPENAI_API_KEY']); // Chargez la clé depuis le fichier .env
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        $evenementsAvecImages = [];
        foreach ($evenements as $event) {
            try {
                $response = $client->images()->create([
                    'prompt' => $event->getNomEvenement(), // Utilisez le nom de l'événement comme description
                    'n' => 1,
                    'size' => '512x512',
                ]);

                $imageUrl = $response['data'][0]['url']; // URL de l'image générée
                $evenementsAvecImages[] = [
                    'event' => $event,
                    'imageUrl' => $imageUrl,
                ];
            } catch (\Exception $e) {
                // Enregistrez ou affichez l'erreur pour le débogage
                error_log('Erreur OpenAI : ' . $e->getMessage());
                $evenementsAvecImages[] = [
                    'event' => $event,
                    'imageUrl' => null, // Pas d'image en cas d'erreur
                ];
            }
        }

        return $this->render('Front/evenement/FRONTevenement.html.twig', [
            'evenementsAvecImages' => $evenementsAvecImages,
        ]);
    }
}
