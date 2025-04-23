<?php

namespace App\Controller\CommuniactionController;

use App\Entity\Demande;
use App\Entity\Avis;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use App\Repository\AviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class DemandeController extends AbstractController
{
    #[Route('/dashboard/demandes', name: 'app_demande_index')]
    public function index(DemandeRepository $demandeRepository): Response
    {
        // Récupérer toutes les demandes
        $demandes = $demandeRepository->findAll();

        return $this->render('back/Communication/Demandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/dashboard/demandes/tri', name: 'app_demande_tri')]
    public function triDemandes(DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findBy([], ['Demande_id' => 'ASC']); // Tri par ID croissant

        return $this->render('back/Communication/Demandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/dashboard/demande/delete/{id}', name: 'app_demande_delete')]
    public function delete(int $id, DemandeRepository $demandeRepository, EntityManagerInterface $em): Response
    {
        $demande = $demandeRepository->find($id);

        if (!$demande) {
            $this->addFlash('error', 'Demande introuvable.');
        } else {
            $em->remove($demande);
            $em->flush();
            $this->addFlash('success', 'Demande supprimée avec succès.');
        }

        return $this->redirectToRoute('app_demande_index');
    }

    #[Route('/dashboard/demande/ajouter', name: 'app_demande_new')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($demande);
            $em->flush();
            $this->addFlash('success', 'Demande ajoutée avec succès.');
            return $this->redirectToRoute('app_demande_index');
        }

        return $this->render('back/Communication/addDemande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/demande/modifier-statut/{id}', name: 'app_demande_update_statut', methods: ['POST'])]
    public function updateStatut(int $id, Request $request, DemandeRepository $demandeRepository, EntityManagerInterface $em): Response
    {
        $demande = $demandeRepository->find($id);

        if (!$demande) {
            $this->addFlash('error', 'Demande non trouvée.');
            return $this->redirectToRoute('app_demande_index');
        }

        $nouveauStatut = $request->request->get('statut');

        if ($nouveauStatut) {
            $demande->setStatut($nouveauStatut);
            $em->flush();
            $this->addFlash('success', 'Statut mis à jour avec succès.');
        }

        return $this->redirectToRoute('app_demande_index');
    }

    #[Route('/demandes', name: 'app_demande_front')]
    public function front(DemandeRepository $demandeRepository): Response
    {
        // Utiliser la méthode personnalisée pour récupérer les demandes acceptées
        $demandes = $demandeRepository->findAcceptedDemandes();

        return $this->render('Front/Communication/demandefront.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/demandes/ajouter', name: 'app_demande_add_front')]
    public function addFront(Request $request, EntityManagerInterface $em): Response
    {
        $demande = new Demande();
        $demande->setStatut('En attente'); // Statut par défaut

        $form = $this->createForm(DemandeType::class, $demande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($demande);
            $em->flush();

            $this->addFlash('success', 'Demande ajoutée avec succès.');

            return $this->redirectToRoute('app_demande_front');
        }

        return $this->render('Front/Communication/addDemande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/demandes/{id}/avis', name: 'app_demande_avis')]
    public function avis(int $id, DemandeRepository $demandeRepository): Response
    {
        $demande = $demandeRepository->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('La demande n\'existe pas.');
        }

        return $this->render('Front/Communication/demandeAvis.html.twig', [
            'demande' => $demande,
            'avis' => $demande->getAvis(), // Assurez-vous que l'entité Demande a une relation avec Avis
        ]);
    }

    #[Route('/demandes/{id}/avis/ajouter', name: 'app_avis_add')]
    public function addAvis(int $id, Request $request, EntityManagerInterface $em, DemandeRepository $demandeRepository): Response
    {
        $demande = $demandeRepository->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('La demande n\'existe pas.');
        }

        $avis = new Avis();
        $avis->setDemande($demande);
        $avis->setDate_avis(new \DateTime()); // Date actuelle

        $form = $this->createFormBuilder($avis)
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre avis',
            ])
            ->add('Utilisateur_id', IntegerType::class, [
                'label' => 'Votre ID utilisateur',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été ajouté avec succès.');

            return $this->redirectToRoute('app_demande_front');
        }

        return $this->render('Front/Communication/addAvis.html.twig', [
            'form' => $form->createView(),
            'demande' => $demande,
        ]);
    }


    #[Route('/demandes/{id}/update-status', name: 'app_demande_update_status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Récupérer la demande via le gestionnaire d'entités
        $demande = $entityManager->getRepository(Demande::class)->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée.');
        }

        // Récupérer le nouveau statut depuis la requête
        $nouveauStatut = $request->request->get('statut');

        if ($nouveauStatut) {
            $demande->setStatut($nouveauStatut);
            $entityManager->flush(); // Sauvegarder les modifications
            $this->addFlash('success', 'Statut mis à jour avec succès.');
        }

        return $this->redirectToRoute('app_demande_index');
    }

    #[Route('/demande/{id}/avis', name: 'demande_avis')]
    public function avisParDemande(int $id, AviRepository $aviRepository): Response
    {
        $avis = $aviRepository->findBy(['Demande_id' => $id]);

        return $this->render('Front/Communication/avisParDemande.html.twig', [
            'avis' => $avis,
        ]);
    }
}
