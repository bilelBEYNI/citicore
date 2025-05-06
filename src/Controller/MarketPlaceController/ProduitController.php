<?php
// src/Controller/MarketPlaceController/ProduitController.php

namespace App\Controller\MarketPlaceController;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Service\GeminiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\String\Slugger\SluggerInterface;


class ProduitController extends AbstractController
{
    public function __construct(
        private ProduitRepository $repo,
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator,
        private GeminiService $geminiService
    ) {}

    #[Route('/produit', name: 'app_produit_', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $qb = $this->repo->createQueryBuilder('p');
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('back/MarketPlace/Produit/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/produit/{idProduit}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'idProduit')] Produit $produit
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $produit->getIdProduit(), $request->request->get('_token'))) {
            $this->em->remove($produit);
            $this->em->flush();
        } else {
        }

        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/produit/clear', name: 'clear', methods: ['POST'])]
    public function clearAll(Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('clear_all', $request->request->get('_token'))) {
            $qb = $this->em->createQueryBuilder()
                ->delete(Produit::class, 'p');
            $qb->getQuery()->execute();
        } else {
        }

        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/produit/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $produit->setPhoto($newFilename);
                } catch (FileException $e) {
                    return $this->render('back/MarketPlace/Produit/new.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('back/MarketPlace/Produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
/*
    #[Route('/generate-description', name: 'app_produit_generate_description', methods: ['GET'])]
    public function generateDescription(Request $request, GeminiService $geminiService): JsonResponse
    {
        $nom = $request->query->get('nom', '');
        if (empty($nom)) {
            return $this->json(['error' => 'Nom du produit manquant'], 400);
        }

        $description = $geminiService->generateProductDescription($nom);

        if (null === $description) {
            return $this->json(['error' => 'Échec de génération.'], 500);
        }

        return $this->json(['description' => $description]);
    }*/

    #[Route('/produit/{idProduit}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'idProduit')] Produit $produit,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );

                    if ($produit->getPhoto()) {
                        $oldPhotoPath = $this->getParameter('uploads_directory') . '/' . $produit->getPhoto();
                        if (file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }

                    $produit->setPhoto($newFilename);
                } catch (FileException $e) {
                    return $this->render('back/MarketPlace/Produit/edit.html.twig', [
                        'form' => $form->createView(),
                        'produit' => $produit,
                    ]);
                }
            }

            try {
                $entityManager->flush();
                return $this->redirectToRoute('app_produit_index');
            } catch (\Exception $e) {
            }
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            // Débogage des erreurs en mode dev
            if ($this->getParameter('kernel.environment') === 'dev') {
                dump($form->getErrors(true));
            }
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('back/MarketPlace/Produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }
}