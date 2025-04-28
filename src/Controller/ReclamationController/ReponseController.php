<?php

namespace App\Controller\ReclamationController;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use App\Form\ReponseType;
use App\Service\SMSRecService;
use App\Service\MailRecService;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;

#[Route('/reponse')]
final class ReponseController extends AbstractController
{
    #[Route(name: 'app_reponse_index', methods: ['GET'])]
    public function index(Request $request, ReponseRepository $reponseRepository,ReclamationRepository $reclamationRepository,PaginatorInterface $paginator): Response
    {
        $qb = $reponseRepository->createQueryBuilder('r')
            ->join('r.reclamation', 'rec')
            ->addSelect('rec');

      // Filtrage par réclamation
        if ($recId = $request->query->get('reclamation_id')) {
            $qb->andWhere('rec.ID_Reclamation = :recId')
               ->setParameter('recId', $recId);
        }

        // Filtrage par statut
        if ($statut = $request->query->get('statut')) {
            $qb->andWhere('r.Statut = :statut')
               ->setParameter('statut', $statut);
        }
        
        // Recherche par contenu ou sujet
        if ($q = $request->query->get('q')) {
            $qb->andWhere('r.Contenu LIKE :q OR rec.Sujet LIKE :q')
               ->setParameter('q', '%'.$q.'%');
        }

        // Tri dynamique
        $sort = $request->query->get('sort', 'r.DateReponse');
        $direction = $request->query->get('direction', 'desc');
        $qb->orderBy($sort, $direction);

        // Pagination
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            [
                'pageParameterName' => 'page',
                'sortFieldParameterName' => 'sort',
                'sortDirectionParameterName' => 'direction',
                'defaultSortFieldName' => 'r.DateReponse',
                'defaultSortDirection' => 'desc',
            ]

        );

        return $this->render('back/Reclamation/Reponse/index.html.twig', [
            'reponses'      => $pagination,
            'reclamations'  => $reclamationRepository->findAll(),
            'currentRec'    => $recId,
            'currentQuery'  => $q,
            'currentStatus' => $statut,
            'currentSort'   => $sort,
            'currentDirection' => $direction,
        ]);
    }
    
    #[Route('/reponses/stats', name: 'app_reponse_stats', methods: ['GET'])]
    public function stats(ReponseRepository $repo, Request $request): Response
    {
        // Récupérer les données de type et de statut
        $byType = $repo->countByType();
        $byStatus = $repo->countByStatus();
    
        // Traitement pour rediriger quand un type ou un statut est sélectionné
        $typeFilter = $request->query->get('Type');
        $statusFilter = $request->query->get('status');
    
        // Si un type ou statut est sélectionné, filtrez les résultats
        if ($typeFilter) {
            $reclamations = $repo->findByType($typeFilter);
        } elseif ($statusFilter) {
            $reclamations = $repo->findByStatus($statusFilter);
        } else {
            $reclamations = []; // Ou toutes les réclamations, selon ce que vous souhaitez afficher par défaut
        }
    
        // Créez les graphiques
        $chartByType = new PieChart();
        $chartByType->getData()->setArrayToDataTable(
            array_merge([['Type', 'Nombre']], array_map(fn($row) => [$row['type'], (int) $row['count']], $byType))
        );
        $chartByType->getOptions()->setTitle('Réponses par Type de Réclamation');
        $chartByType->getOptions()->getLegend()->setPosition('bottom');
    
        $chartByStatus = new ColumnChart();
        $chartByStatus->getData()->setArrayToDataTable(
            array_merge([['Statut', 'Nombre']], array_map(fn($row) => [$row['statut'], (int) $row['count']], $byStatus))
        );
        $chartByStatus->getOptions()->setTitle('Réponses par Statut');
        $chartByStatus->getOptions()->getHAxis()->setTitle('Statut');
        $chartByStatus->getOptions()->getVAxis()->setTitle('Nombre');
    
        return $this->render('back/Reclamation/Reponse/stats.html.twig', [
            'chartByType'   => $chartByType,
            'chartByStatus' => $chartByStatus,
            'reclamations'  => $reclamations,  // Passer les réclamations filtrées
            'typeFilter'    => $typeFilter,    // Passer le type filtré pour afficher dans la vue
            'statusFilter'  => $statusFilter,  // Passer le statut filtré pour afficher dans la vue
        ]);
    }

    #[Route('/reponses/new/{id}', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager, SMSRecService $sms, MailRecService $mailService): Response
    {
        $reponse = new Reponse();
        $reponse->setReclamation($reclamation);
        $reponse->setDateReponse(new \DateTime());

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($reponse);
                $entityManager->flush();

                // 2) Préparation du SMS
                $sujet  = $reclamation->getSujet();
                $status = $reponse->getStatut(); 
                $message = sprintf(
                    'Votre réclamation « %s » a reçu une réponse (statut : %s).',
                    $sujet,
                    $status
                );

                // Envoie le SMS
                $smsSent = $sms->sendSms('+21692581168', $message); 

                if ($smsSent) {
                    $this->addFlash('success', 'Réponse créée et SMS envoyé avec succès.');
                } 
                else {
                    $this->addFlash('warning', 'Réponse créée, mais échec de l\'envoi du SMS.');
                }
               

                // Au moment de planifier le rappel :
                if ($status === 'En Cours') {
                    $adminEmail  = 'medinishyheb11@gmail.com';
                    $mailSubject = sprintf('Rappel : gérer la réponse à « %s »', $sujet);
                    $mailText    = sprintf(
                        'La réponse à la réclamation « %s » est toujours En Cours. Merci de la traiter.',
                        $sujet
                    );
                
                    // scheduleReminder() renvoie true ou false
                    $scheduled = $mailService->scheduleReminder($adminEmail, $mailSubject, $mailText, 10);
                
                    if ($scheduled) {
                        $this->addFlash('success', 'Rappel email planifié avec succès pour l’admin.');
                    } 
                    else{
                        $this->addFlash('warning', 'Impossible de planifier le rappel email pour l’admin.');
                    }
                }


                return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
            }
            else {
                $this->addFlash('error', 'Le formulaire contient des erreurs , veuillez vérifier vos saisies.');
            }
        }

        return $this->render('back/Reclamation/Reponse/new.html.twig', [
            'reponse'      => $reponse,
            'reclamation'  => $reclamation,
            'form'         => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('back/Reclamation/Reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Réponse modifiée avec succès.');

                return $this->redirectToRoute('app_reponse_index');
            } 
            else {
                $this->addFlash('error', 'Le formulaire contient des erreurs.');
            }
        }

        return $this->render('back/Reclamation/Reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        // Correction : utiliser $request->request->get('_token') pour récupérer le token CSRF
        if ($this->isCsrfTokenValid('delete'.$reponse->getID_Reponse(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
            $this->addFlash('success', 'Réponse supprimée avec succès.');
        } 
        else {
            $this->addFlash('error', 'Jeton CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
}
