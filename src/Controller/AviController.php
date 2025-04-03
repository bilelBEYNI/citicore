<?php

namespace App\Controller;

use App\Entity\Avi;
use App\Form\AviType;
use App\Repository\AviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avi')]
final class AviController extends AbstractController
{
    #[Route(name: 'app_avi_index', methods: ['GET'])]
    public function index(AviRepository $aviRepository): Response
    {
        return $this->render('avi/index.html.twig', [
            'avis' => $aviRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_avi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avi = new Avi();
        $form = $this->createForm(AviType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('app_avi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avi/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{avis_id}', name: 'app_avi_show', methods: ['GET'])]
    public function show(Avi $avi): Response
    {
        return $this->render('avi/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    #[Route('/{avis_id}/edit', name: 'app_avi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avi $avi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AviType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avi/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{avis_id}', name: 'app_avi_delete', methods: ['POST'])]
    public function delete(Request $request, Avi $avi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getAvis_id(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avi_index', [], Response::HTTP_SEE_OTHER);
    }
}
