<?php

namespace App\Controller\EvenementController;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\EvenementType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\NotificationService;

class EvenementController extends AbstractController
{ 
    #[Route('/dashboard/evenement', name: 'app_evenement_index')]
    public function utilisateurindex(
        Request $request,
        EvenementRepository $evenementRepository,
        CategorieRepository $categorieRepository,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer les paramètres de recherche, tri et pagination
        $query = $request->query->get('q', ''); // Recherche par nom
        $lieu = $request->query->get('lieu', ''); // Filtrer par lieu
        $sort = $request->query->get('sort', 'e.id_evenement'); // Champ de tri par défaut
        $direction = $request->query->get('direction', 'asc'); // Direction de tri par défaut

        // Construire la requête avec les filtres
        $qb = $evenementRepository->createQueryBuilder('e');

        if (!empty($query)) {
            $qb->andWhere('e.nom_evenement LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if (!empty($lieu)) {
            $qb->andWhere('e.lieu_evenement LIKE :lieu')
               ->setParameter('lieu', '%' . $lieu . '%');
        }

        $qb->orderBy($sort, $direction);

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1), // Page actuelle
            10 // Nombre d'éléments par page
        );

        // Calculer les statistiques
        $totalEvenements = $evenementRepository->count([]);
        $totalCategories = $categorieRepository->count([]);

        // Passer les données à la vue
        return $this->render('back/Evenement/Evenement.html.twig', [
            'evenements' => $pagination,
            'totalEvenements' => $totalEvenements,
            'totalCategories' => $totalCategories,
            'currentQuery' => $query,
            'currentLieu' => $lieu,
            'currentSort' => $sort,
            'currentDirection' => $direction,
        ]);
    }

    #[Route('/dashboard/evenement/show/{id}', name: 'app_evenement_show')]
    public function show(int $id, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($id);
    
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }
    
        return $this->render('back/evenement/showEvenement.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/dashboard/evenement/delete/{id}', name: 'app_evenement_delete')]
    public function delete(int $id, EvenementRepository $evenementRepository, EntityManagerInterface $em): Response
    {
        // Trouver l'événement par son ID
        $evenement = $evenementRepository->find($id);
    
        // Si l'événement n'existe pas, afficher une erreur
        if (!$evenement) {
            $this->addFlash('error', 'Événement introuvable.');
            return $this->redirectToRoute('app_evenement_index');
        }
    
        // Suppression de l'événement
        $em->remove($evenement);
        $em->flush();
    
        // Message de succès
        $this->addFlash('success', 'Événement supprimé avec succès.');
    
        // Redirection vers la liste des événements
        return $this->redirectToRoute('app_evenement_index');
    }

    #[Route('/dashboard/evenement/ajouter', name: 'app_evenement_new')]
    public function add(Request $request, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($evenement);
            $em->flush();

            // Send push notification
            $notificationService->sendNewEventNotification(
                $evenement->getNomEvenement(),
                $evenement->getDateEvenement() ? $evenement->getDateEvenement()->format('Y-m-d H:i:s') : 'Date non définie',
                $evenement->getLieuEvenement()
            );

            $this->addFlash('success', 'Événement ajouté avec succès.');
            return $this->redirectToRoute('app_evenement_index');
        }

        return $this->render('back/evenement/addEvenement.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/dashboard/evenement/edit/{id}', name: 'app_evenement_edit')]
    public function edit(int $id, EvenementRepository $evenementRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'événement par son ID
        $evenement = $evenementRepository->find($id);
    
        // Vérifier s'il existe
        if (!$evenement) {
            throw $this->createNotFoundException('Événement introuvable');
        }
    
        // Créer le formulaire
        $form = $this->createForm(EvenementType::class, $evenement);
    
        // Traiter la requête
        $form->handleRequest($request);
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            $this->addFlash('success', 'Événement modifié avec succès.');
            return $this->redirectToRoute('app_evenement_index');
        }
    
        // Afficher le formulaire
        return $this->render('back/evenement/editEvenement.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }
}