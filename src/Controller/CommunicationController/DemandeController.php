<?php

namespace App\Controller\CommuniactionController;

use App\Form\AvisType;
use App\Entity\Demande;
use App\Entity\Avis;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use App\Repository\AviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Endroid\QrCode\Builder\BuilderInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class DemandeController extends AbstractController
{
    #[Route('/dashboard/demandes', name: 'app_demande_index')]
    public function index(DemandeRepository $repo): Response
    {
        $statutCounts = $repo->countByStatut();
        $demandes = $repo->findBy(['statut' => 'En attente']);

        return $this->render('back/Communication/Demandes.html.twig', [
            'demandes' => $demandes,
            'nombre_acceptee' => $statutCounts['Acceptée'] ?? 0,
            'nombre_attente'  => $statutCounts['En attente'] ?? 0,
            'nombre_refusee'  => $statutCounts['Refusée'] ?? 0,
        ]);
    }

    #[Route('/dashboard/demandes/tri', name: 'app_demande_tri')]
    public function triDemandes(DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findBy([], ['demandeId' => 'ASC']);

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
    /*
    #[Route('/dashboard/demande/modifier-statut/{id}', name: 'app_demande_update_status', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
    }*/

    #[Route('/demandes', name: 'app_demande_front')]
    public function front(DemandeRepository $demandeRepository, BuilderInterface $qrCodeBuilder): Response
    {
        $demandes = $demandeRepository->findAcceptedDemandes();

        return $this->render('Front/Communication/demandefront.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/activitées', name: 'app_demande_Activités')]
    public function activitées(DemandeRepository $demandeRepository, AviRepository $aviRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $user = $this->getUser();
        $cin = $user->getCin();
        $demandes = $demandeRepository->findBy(['cinUtilisateur' => $cin]);

        return $this->render('Front/Communication/activités.html.twig', [
            'cin' => $cin,
            'demandes' => $demandes,
        ]);
    }

    #[Route('/avis/new/{id}', name: 'avis_new')]
    public function new(int $id, DemandeRepository $demandeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = $demandeRepository->find($id);
        if (!$demande) {
            throw $this->createNotFoundException('La demande avec cet ID n\'existe pas.');
        }

        $avis = new Avis();
        $avis->setDemandeId($id);
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_front');
        }

        return $this->render('Front/Communication/new.html.twig', [
            'form' => $form->createView(),
            'demande' => $demande,
        ]);
    }

    #[Route('/demandes/ajouter', name: 'app_demande_add_front')]
    public function addFront(Request $request, EntityManagerInterface $em, DemandeRepository $demandeRepository): Response
    {
        $demande = new Demande();
        $demande->setStatut('En attente');
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
            'avis' => $demande->getAvis(),
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
        $avis->setDemandeId($id);
        $avis->setDateavis(new \DateTime());

        $form = $this->createFormBuilder($avis)
            ->add('commentaire', TextareaType::class, ['label' => 'Votre avis'])
            ->add('Utilisateur_id', IntegerType::class, ['label' => 'Votre ID utilisateur'])
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

    #[Route('/{id}/avis', name: 'demande_avis')]
    public function showAvisByDemande(Demande $demande): Response
    {
        return $this->render('demande/avis.html.twig', [
            'demande' => $demande,
            'avis' => $demande->getAvis(),
        ]);
    }

    #[Route('/demandes/{id}/update-status', name: 'app_demande_update_status', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateStatus(int $id, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $demande = $entityManager->getRepository(Demande::class)->find($id);
        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée.');
        }

        $nouveauStatut = $request->request->get('statut');
        if ($nouveauStatut) {
            $demande->setStatut($nouveauStatut);
            $entityManager->flush();
            $this->addFlash('success', 'Statut mis à jour avec succès.');
        }

        return $this->redirectToRoute('app_demande_index');
    }

    #[Route('/demande/{id}/avis', name: 'demande_avis')]
    public function avisParDemande(int $id, AviRepository $aviRepository): Response
    {
        $avis = $aviRepository->findBy(['demandeId' => $id]);

        return $this->render('back/Communication/avisParDemande.html.twig', [
            'avis' => $avis,
            'demandeId' => $id,
        ]);
    }

    #[Route('/demande/ajouter', name: 'ajouter_demande')]
    public function ajouterDemande(Request $request, EntityManagerInterface $entityManager): Response
    {
      
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('liste_demandes');
        }

        return $this->render('Front/Communication/addDemande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/back/demandes', name: 'liste_demandes')]
    public function listeDemandes(EntityManagerInterface $entityManager): Response
    {
        $demandes = $entityManager->getRepository(Demande::class)->findAll();

        return $this->render('back/Communication/Demandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/demandes/export/pdf', name: 'app_demande_export_pdf')]
    public function exportPdf(DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findAll();
        $html = $this->renderView('back/Communication/PDFDemande.html.twig', ['demandes' => $demandes]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="demandes.pdf"',
            ]
        );
    }

    #[Route('/dashboard/demandes/stats', name: 'app_demande_stats', methods: ['GET'])]
    public function stats(DemandeRepository $repo, Request $request): Response
    {
        $allStatutCounts = $repo->countByStatut();
        $statutCounts = array_filter($allStatutCounts, function($row) {
            return in_array($row['statut'], ['Acceptée', 'Refusée']);
        });
        $statutCounts = array_values($statutCounts);
        $dateCounts   = $repo->countByDate();

        $statusFilter = $request->query->get('status');
        $dateFilter   = $request->query->get('date');
        $demandes     = [];
        if ($statusFilter) {
            $demandes = $repo->findBy(['statut' => $statusFilter]);
        } elseif ($dateFilter) {
            $demandes = $repo->findBy(['dateDemande' => new \DateTime($dateFilter)]);
        }

        return $this->render('back/Communication/DemandesStats.html.twig', [
            'chartByStatutData' => $statutCounts,
            'chartByDateData'   => $dateCounts,
            'demandes'          => $demandes,
            'statusFilter'      => $statusFilter,
            'dateFilter'        => $dateFilter,
        ]);
    }
    
}
