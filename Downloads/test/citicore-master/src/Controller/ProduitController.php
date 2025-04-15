<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/new', name: 'app_produit_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier uploadé depuis le champ "photo"
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                try {
                    // Déplacement du fichier dans le dossier défini par uploads_directory
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion de l'erreur si le téléchargement échoue
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                }

                // Enregistrement du nom du fichier dans l'entité
                $produit->setPhoto($newFilename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');

            // Redirection vers la page d'index des produits
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}/edit', name: 'app_produit_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Produit $produit): Response
    {
        // Conserver le nom actuel de l'image
        $originalPhoto = $produit->getPhoto();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier uploadé
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                }

                // Mise à jour de l'image avec le nouveau nom
                $produit->setPhoto($newFilename);
            } else {
                // Si aucun fichier n'est uploadé, conserver l'image existante
                $produit->setPhoto($originalPhoto);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/produit/{id}/delete', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Vérification du jeton CSRF
        if ($this->isCsrfTokenValid('delete' . $produit->getIdProduit(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide.');
        }
    
        return $this->redirectToRoute('app_produit_index');
    }
    
    
}
