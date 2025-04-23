<?php
namespace App\Controller\MarketPlaceController;


use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriController extends AbstractController
{
    #[Route('/favoris', name: 'app_favoris')]
    public function index(RequestStack $requestStack, ProduitRepository $produitRepository): Response
    {
        $session = $requestStack->getSession();
        $favorisIds = $session->get('favoris', []);
        $produits = $produitRepository->findBy(['id_produit' => $favorisIds]);

        return $this->render('Front/MarketPlace/favoris.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/favoris/toggle/{id}', name: 'app_favoris_toggle')]
    public function toggle(int $id, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $favoris = $session->get('favoris', []);

        if (in_array($id, $favoris)) {
            $favoris = array_diff($favoris, [$id]);
        } else {
            $favoris[] = $id;
        }

        $session->set('favoris', $favoris);

        return $this->redirect($_SERVER['HTTP_REFERER'] ?? $this->generateUrl('app_shop'));
    }
}
