<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        return $this->render('shop/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart(Produit $produit): Response
    {
        $panier = $this->session->get('panier', []);
        $id = $produit->getIdProduit();
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $this->session->set('panier', $panier);

        // Reste sur la page shop après ajout
        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectToRoute('app_shop');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(int $id): Response
    {
        $panier = $this->session->get('panier', []);
        if (isset($panier[$id])) {
            unset($panier[$id]);
            $this->session->set('panier', $panier);
            $this->addFlash('warning', 'Produit retiré du panier.');
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

        return $this->render('shop/cart.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout')]
    public function checkout(): Response
    {
        $panier = $this->session->get('panier', []);
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        // 1) Création et persistence de la Commande
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $this->em->persist($commande);
        $this->em->flush(); // génère l'ID de la commande

        // 2) Création des lignes de commande, persist + flush + detach ligne par ligne
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
            $this->em->flush();        // attribue un ID à $ligne
            $this->em->detach($ligne);  // détache l'instance pour éviter collision
        }

        // 3) Nettoyage du panier
        $this->session->remove('panier');
        return $this->redirectToRoute('app_shop');
    }
    
#[Route('/cart/details/{id}', name: 'app_cart_details')]
public function details(int $id, ProduitRepository $produitRepository): Response
{
    // Récupérer les détails du produit avec l'ID passé dans l'URL
    $produit = $produitRepository->find($id);
    if (!$produit) {
        $this->addFlash('error', 'Produit non trouvé.');
        return $this->redirectToRoute('app_shop');
    }

    return $this->render('shop/details.html.twig', [
        'produit' => $produit,
    ]);
}
}
