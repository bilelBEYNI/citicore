<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
 

    #[Route('/participant', name: 'participant_dashboard')]
    public function participantDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');
    
        $user = $this->getUser();
        $cin = $user->getCin();
    
        return $this->render('Front/security/login.html.twig', [
            'controller_name' => 'ParticipantController',
            'cin' => $cin,
        ]);
    }
    
}
