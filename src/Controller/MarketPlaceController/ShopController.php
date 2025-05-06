<?php
namespace App\Controller\MarketPlaceController;


use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShopController extends AbstractController
{
    private $session;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->session = $requestStack->getSession();
        $this->em = $em;
    }

    #[Route('/shop', name: 'app_shop')]
    public function shop(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        $panier = $session->get('panier', []);
        $cartCount = array_sum($session->get('panier', []));

        return $this->render('Front/MarketPlace/index.html.twig', [
            'produits' => $produits,
            'cartCount' => $cartCount,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function addToCart(int $id, Request $request, ProduitRepository $repo): Response
    {
        $produit = $repo->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }
    
        $panier = $this->session->get('panier', []);
        
        if (!isset($panier[$id])) {
            $panier[$id] = 1;
            $this->session->set('panier', $panier);
        }
    
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'cartCount' => count($panier),
            ]);
        }
    
        return $this->redirectToRoute('app_shop');
    }
    
    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
    public function removeFromCart(int $id): Response
    {
        $panier = $this->session->get('panier', []);
        if (isset($panier[$id])) {
            unset($panier[$id]);
            $this->session->set('panier', $panier);
        }
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(ProduitRepository $produitRepository): Response
    {
        $panier = $this->session->get('panier', []);
        $items = [];
        $total = 0.0;

        foreach ($panier as $id => $quantity) {
            $produit = $produitRepository->find($id);
            if (!$produit) {
                continue;
            }

            $sousTotal = $produit->getPrix() * $quantity;
            $items[] = [
                'produit'   => $produit,
                'quantity'  => $quantity,
                'sousTotal' => $sousTotal,
            ];
            $total += $sousTotal;
        }

        return $this->render('Front/MarketPlace/cart.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['GET'])]
    public function checkout(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        if (empty($panier)) {
            return $this->redirectToRoute('app_cart');
        }

        // Création d'une nouvelle commande
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $this->em->persist($commande);
        $this->em->flush(); // Persister la commande avant d'ajouter les produits

        // Ajout des produits à la commande
        foreach ($panier as $id => $qty) {
            $produit = $this->em->getRepository(Produit::class)->find($id);
            if (!$produit) {
                continue;
            }

            $ligne = new CommandeProduit();
            $ligne->setCommande($commande);
            $ligne->setProduit($produit);
            $ligne->setQuantite($qty);
            $this->em->persist($ligne);
        }

        // Enregistrement des lignes de commande
        $this->em->flush();

        // Suppression du panier
        $session->remove('panier');


        return $this->redirectToRoute('app_shop');
    }

    #[Route('/produit/{id}', name: 'app_produit_detail', methods: ['GET'])]
    public function detail(Produit $produit): Response
    {
        return $this->render('Front/MarketPlace/details.html.twig', [
            'produit' => $produit,
        ]);
    }
    #[Route('/cart/ajax/add/{id}', name: 'app_cart_ajax_add', methods: ['GET'])]
    public function addToCartAjax(Produit $produit): JsonResponse
    {
        $panier = $this->session->get('panier', []);
        $id = $produit->getIdProduit();
    
        if (!isset($panier[$id])) {
            $panier[$id] = 1;
            $this->session->set('panier', $panier);
        }
    
        $cartCount = count($panier);
        return new JsonResponse([
            'cartCount' => $cartCount,
        ]);
    }
    
}