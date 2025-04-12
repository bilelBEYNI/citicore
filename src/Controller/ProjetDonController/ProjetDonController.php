<?php
namespace App\Controller\ProjetDonController;

use App\Entity\ProjetDon;
use App\Form\ProjetDonType;
use App\Repository\ProjetDonRepository;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projet/don')]
final class ProjetDonController extends AbstractController
{
    #[Route(name: 'app_projet_don_index', methods: ['GET'])]
    public function index(ProjetDonRepository $projetDonRepository): Response
    {
        return $this->render('projet_don/index.html.twig', [
            'projet_dons' => $projetDonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_projet_don_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AssociationRepository $associationRepository): Response
    {
        $projetDon = new ProjetDon();

        // Fetch associations from the repository
        $associations = $associationRepository->findAll();

        // Create the form and pass associations
        $form = $this->createForm(ProjetDonType::class, $projetDon, [
            'associations' => $associations,  // Pass associations to the form
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projetDon);
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet_don/new.html.twig', [
            'projet_don' => $projetDon,
            'form' => $form,
        ]);
    }

    #[Route('/{id_Projet_Don}', name: 'app_projet_don_show', methods: ['GET'])]
    public function show(ProjetDon $projetDon): Response
    {
        return $this->render('projet_don/show.html.twig', [
            'projet_don' => $projetDon,
        ]);
    }

    #[Route('/{id_Projet_Don}/edit', name: 'app_projet_don_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjetDon $projetDon, EntityManagerInterface $entityManager, AssociationRepository $associationRepository): Response
    {
        // Fetch associations from the repository
        $associations = $associationRepository->findAll();

        // Create the form and pass associations
        $form = $this->createForm(ProjetDonType::class, $projetDon, [
            'associations' => $associations,  // Pass associations to the form
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet_don/edit.html.twig', [
            'projet_don' => $projetDon,
            'form' => $form,
        ]);
    }

    #[Route('/{id_Projet_Don}', name: 'app_projet_don_delete', methods: ['POST'])]
    public function delete(Request $request, ProjetDon $projetDon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projetDon->getId_Projet_Don(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projetDon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_don_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id_Projet_Don}/donner', name: 'app_projet_don_donner', methods: ['GET', 'POST'])]
public function donner(Request $request, ProjetDon $projetDon, EntityManagerInterface $entityManager): Response
{
    // Handle the donation form submission
    if ($request->isMethod('POST')) {
        // Retrieve the donation amount from the request
        $montant = floatval($request->request->get('montant'));

        // Ensure that the amount is positive
        if ($montant > 0) {
            // Update the montantRecu for the project
            $projetDon->setMontantRecu($projetDon->getMontantRecu() + $montant);
            $entityManager->flush(); // Save the updated amount

            // Flash success message
            $this->addFlash('success', 'Merci pour votre don !');

            // Redirect to the same page to refresh the project details
            return $this->redirectToRoute('app_projet_don_donner', ['id_Projet_Don' => $projetDon->getId_Projet_Don()]);
        } else {
            $this->addFlash('error', 'Le montant doit être supérieur à zéro.');
        }
    }

    // Render the page with the donation form (assuming your front end handles it)
    return $this->render('front_projet/donner.html.twig', [
        'projet_don' => $projetDon,
    ]);
}

}
