<?php

namespace App\Controller\ReclamationController;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Service\SMSRecService; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class FrontReclamationController extends AbstractController
{
    #[Route('/front/reclamation', name: 'front_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $cin = $this->getUser()->getCin();

        // Filtre selon le nom exact de la propriété dans l'entité
        $reclamations = $repo->findBy(
            ['Cin_Utilisateur' => $cin],
            ['Date_Creation'   => 'DESC']
        );

        return $this->render('front/Reclamation/Reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/front/reclamation/new', name: 'front_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em,SMSRecService $sms ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $cin = $this->getUser()->getCin();

        $reclamation = new Reclamation();
        $reclamation->setCin_Utilisateur($cin);
        // Laisse le form gérer Date_Creation et Date_Resolution si tu les as dans ton ReclamationType

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reclamation);
            $em->flush();

            // Préparation du SMS
            $sujet   = $reclamation->getSujet();
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

            return $this->redirectToRoute('front_reclamation_index', [], Response::HTTP_SEE_OTHER);
        } 
        else {
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos saisies.');
        }

        return $this->render('front/Reclamation/Reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form'        => $form->createView(),
        ]);
    }

    #[Route('/front/reclamation/{ID_Reclamation}', name: 'front_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
        $cin = $this->getUser()->getCin();

        // Vérifie bien que le Cin_Utilisateur (avec underscore) correspond
        if ($reclamation->getCin_Utilisateur() !== $cin) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette réclamation.');
        }

        return $this->render('front/Reclamation/Reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
