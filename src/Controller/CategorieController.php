<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategorieController extends AbstractController
{
    #[Route('/dashboard/categorie', name: 'app_categorie_index')]
    public function index(CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        // Récupérer toutes les catégories depuis la base de données
        $categories = $categorieRepository->findAll();

        // Utiliser DQL pour obtenir le nombre d'événements par catégorie
        $query = $em->createQuery(
            'SELECT c.nomCategorie, COUNT(e.id_evenement) as eventCount 
             FROM App\Entity\Categorie c 
             LEFT JOIN App\Entity\Evenement e WITH e.categorie = c 
             GROUP BY c.id'
        );
        
        $categoriesStats = [];
        $results = $query->getResult();
        
        foreach ($results as $result) {
            $categoriesStats[] = [
                'nom' => $result['nomCategorie'],
                'count' => $result['eventCount']
            ];
        }

        // Passer les catégories et les statistiques au template
        return $this->render('back/Evenement/Categorie.html.twig', [
            'categories' => $categories,
            'categoriesStats' => $categoriesStats
        ]);
    }

    #[Route('/dashboard/categorie/show/{id}', name: 'app_categorie_show')]
    public function show(int $id, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        return $this->render('back/Evenement/ShowCategorie.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/dashboard/categorie/ajouter', name: 'app_categorie_add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image_url')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le fichier ne peut pas être déplacé
                }

                $categorie->setImageUrl($newFilename);
            }

            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('back/Evenement/addCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/categorie/edit/{id}', name: 'app_categorie_edit')]
    public function edit(Request $request, int $id, CategorieRepository $categorieRepository, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $this->handleImageUpload($form, $categorie, $slugger);

            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès.');
            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('back/Evenement/editCategorie.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    #[Route('/dashboard/categorie/delete/{id}', name: 'app_categorie_delete')]
    public function delete(int $id, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            $this->addFlash('error', 'Catégorie introuvable.');
            return $this->redirectToRoute('app_categorie_index');
        }

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('app_categorie_index');
    }

    private function handleImageUpload($form, Categorie $categorie, SluggerInterface $slugger): void
    {
        $imageFile = $form->get('image_url')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'), // Utilise le paramètre défini
                    $newFilename
                );
                $categorie->setImageUrl($newFilename); // Met à jour l'URL de l'image
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
            }
        }
    }
}