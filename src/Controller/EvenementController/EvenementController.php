<?php

namespace App\Controller\EvenementController;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EvenementType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EvenementController extends AbstractController
{ 
    

    
    #[Route('/dashboard/evenement', name: 'app_evenement_index')]
    public function utilisateurindex(EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
    {
   
        $evenements = $evenementRepository->findAll();
    
        // Passer les données à la vue
        return $this->render('back/Evenement/Evenement.html.twig', [
            'evenements' => $evenements
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
            return $this->redirectToRoute('app_evenement_index'); // à adapter selon ta route de liste
        }
    
        // Suppression de l'événement
        $em->remove($evenement);
        $em->flush();
    
        // Message de succès
        $this->addFlash('success', 'Événement supprimé avec succès.');
    
        // Redirection vers la liste des événements
        return $this->redirectToRoute('app_evenement_index'); // à adapter si ton nom de route est différent
    }

    #[Route('/dashboard/evenement/ajouter', name: 'app_evenement_new')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->add('ajouter', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($evenement);
            $em->flush();

            $this->addFlash('success', 'Événement ajouté avec succès.');
            return $this->redirectToRoute('app_evenement_index');
        }

        return $this->render('back/evenement/addEvenement.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/dashboard/evenement/edit/{id}', name: 'app_evenement_edit')]
    public function edit($id, EvenementRepository $evenementRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'événement par son ID
        $evenement = $evenementRepository->find($id);
    
        // Vérifier s'il existe
        if (!$evenement) {
            throw $this->createNotFoundException('Événement introuvable');
        }
    
        // Créer le formulaire
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->add('Modifier', SubmitType::class, [
            'label' => 'Modifier',
            'attr' => ['class' => 'btn btn-primary']
        ]);
    
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
