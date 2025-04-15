<?php
// src/Controller/CartController.php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\commande_produit;
use App\Entity\Produit;
use App\Entity\CartService_; // Assure-toi d'ajouter ce use !
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart', methods: ['GET', 'POST'])]
    public function cart(Request $request, SessionInterface $session, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $session->set('panier', $data);
            return $this->json(['status' => 'ok']);
        }

        $panier = $session->get('panier', []);
        $items = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $produit = $em->getRepository(Produit::class)->find($id);
            if (!$produit) {
                continue;
            }

            $sousTotal = $produit->getPrix() * $quantity;
            $total += $sousTotal;
            $items[] = [
                'produit' => $produit,
                'quantity' => $quantity,
                'sousTotal' => $sousTotal
            ];
        }

        return $this->render('cart.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout')]
    public function checkout(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $cart = $session->get('panier', []);
        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus('en attente');
        // $commande->setUtilisateur($this->getUser());

        $entityManager->persist($commande);

        foreach ($cart as $idProduit => $quantite) {
            $produit = $entityManager->getRepository(Produit::class)->find($idProduit);
            if (!$produit) continue;

            $commandeProduit = new commande_produit();
            $commandeProduit->setCommande($commande);
            $commandeProduit->setProduit($produit);
            $commandeProduit->setQuantite($quantite);
            $commandeProduit->setPrixUnitaire($produit->getPrix());

            $entityManager->persist($commandeProduit);
        }

        $entityManager->flush();
        $session->remove('panier');

        $this->addFlash('success', 'Votre commande a bien été enregistrée !');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/cart/update/{id}/{operation}', name: 'app_cart_update', methods: ['GET'])]
    public function updateCart(int $id, string $operation, CartService_ $cartService): Response
    {
        $cart = $cartService->getCart();
        $currentQty = $cart[$id] ?? 0;

        if ($operation === 'plus') {
            $cartService->add($id, 1);
        } elseif ($operation === 'minus') {
            if ($currentQty > 1) {
                $cartService->update($id, $currentQty - 1);
            } else {
                $cartService->remove($id);
            }
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(int $id, CartService_ $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart');
    }
}
