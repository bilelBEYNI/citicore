<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Repository\DemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DemandeController extends AbstractController
{ 
    

    
    #[Route('/dashboard/demandes', name: 'app_demande_index')]
    public function utilisateurindex(DemandeRepository $demandeRepository, EntityManagerInterface $entityManager): Response
    {    
        $demandes = $demandeRepository->findAll();
    
   
        return $this->render('back/Communication/Demandes.html.twig', [
            'demandes' => $demandes
        ]);
    }
    
    #[Route('/dashboard/demande/delete/{id}', name: 'app_demande_delete')]
    public function delete(int $id, DemandeRepository $demandeRepository, EntityManagerInterface $em): Response
    {
        // Trouver la demande par son ID
        $demande = $demandeRepository->find($id);
    
        if (!$demande) {
            $this->addFlash('error', 'Demande introuvable.');
            return $this->redirectToRoute('app_demande_index');
        }
    
        // Supprimer la demande
        $em->remove($demande);
        $em->flush();
    
        $this->addFlash('success', 'Demande supprimée avec succès.');
    
        return $this->redirectToRoute('app_demande_index');
    }

    #[Route('/dashboard/demande/ajouter', name: 'app_demande_new')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->add('ajouter', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($demande);
            $em->flush();

            $this->addFlash('success', 'demande ajouté avec succès.');
            return $this->redirectToRoute('app_demande_index');
        }

        return $this->render('back/Communication/addDemande.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
   

    


}
