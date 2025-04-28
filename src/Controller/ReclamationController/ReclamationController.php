<?php

namespace App\Controller\ReclamationController;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Service\SMSRecService;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route(name: 'app_reclamation_index', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $reclamationRepository, PaginatorInterface $paginator): Response
    {
        $type = $request->query->get('type');
        $query = $request->query->get('q');
        $sort = $request->query->get('sort', 'r.ID_Reclamation');
        $direction = $request->query->get('direction', 'asc');
    
        $qb = $reclamationRepository->findFilteredReclamations($type, $query, $sort, $direction);
    
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
            [
                'pageParameterName' => 'page',
                'sortFieldParameterName' => 'sort',
                'sortDirectionParameterName' => 'direction',
                'defaultSortFieldName' => 'r.ID_Reclamation',
                'defaultSortDirection' => 'asc',
            ]
        );
    
        return $this->render('back/Reclamation/Reclamation/index.html.twig', [
            'reclamations' => $pagination,
            'currentType' => $type,
            'currentQuery' => $query,
            'currentSort' => $sort,
            'currentDirection' => $direction,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SMSRecService $sms ): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($reclamation);
                $entityManager->flush();

                // Récupère le sujet
                $sujet = $reclamation->getSujet();

                // Compose ton texte avec sprintf()
                $message = sprintf(
                    'Votre réclamation « %s » a bien été envoyée.',
                    $sujet
                );
                
                // Envoie le SMS
                $smsSent = $sms->sendSms('+21692581168', $message); 

                if ($smsSent) {
                    $this->addFlash('success', 'Réclamation créée et SMS envoyé avec succès.');
                } 
                else {
                    $this->addFlash('warning', 'Réclamation créée, mais échec de l\'envoi du SMS.');
                }

                return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
            } 
            else {
                $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos saisies.');
            }
        }

        return $this->render('back/Reclamation/Reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' =>  $form->createView(),
        ]);
    }

    #[Route('/{ID_Reclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('back/Reclamation/Reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{ID_Reclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Réclamation modifiée avec succès.');

                return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
            } 
            else {
                $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos modifications.');
            }
        }

        return $this->render('back/Reclamation/Reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_Reclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getID_Reclamation(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation supprimée avec succès.');
        }
        else {
            $this->addFlash('error', 'Jeton CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}



