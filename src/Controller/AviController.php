<?php

namespace App\Controller;

use App\Entity\Avi;
use App\Form\AviType;
use App\Repository\AviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avis')]
class AviController extends AbstractController
{
    #[Route('/', name: 'app_avi_index', methods: ['GET'])]
    public function index(AviRepository $aviRepository): Response
    {
        return $this->render('avi/index.html.twig', [
            'avis' => $aviRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_avi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $avi = new Avi();
        $form = $this->createForm(AviType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avi->setCreatedAt(new \DateTime());
            $em->persist($avi);
            $em->flush();

            return $this->redirectToRoute('app_avi_index');
        }

        return $this->render('avi/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avi_show', methods: ['GET'])]
    public function show(Avi $avi): Response
    {
        return $this->render('avi/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_avi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avi $avi, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AviType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_avi_index');
        }

        return $this->render('avi/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avi_delete', methods: ['POST'])]
    public function delete(Request $request, Avi $avi, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $avi->getId(), $request->get('_token'))) {
            $em->remove($avi);
            $em->flush();
        }

        return $this->redirectToRoute('app_avi_index');
    }
}
