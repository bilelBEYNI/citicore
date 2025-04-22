<?php
// src/Entity/CartService.php
namespace App\Entity;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;

class CartService
{
    private $session;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->session = $requestStack->getSession();
        $this->entityManager = $entityManager;
    }

    public function getCart(): array
    {
        // Supposons que tu stockes en session un array de rawItems
        $rawItems = $this->session->get('cart', []); 
        $items = [];
    
        foreach ($rawItems as $productId => $data) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($productId);
            $unitPrice = $produit->getPrix(); // ou $data['unitPrice'] si tu stockes le prix en session
    
            $items[] = [
                'produit'   => $produit,           // objet Produit
                'quantity'  => $data['quantity'],  // quantité
                'prix'      => $unitPrice,         // prix unitaire
                'sousTotal' => $data['quantity'] * $unitPrice,
            ];
        }
    
        $totalHT   = array_sum(array_column($items, 'sousTotal'));
        $vatAmount = $totalHT * 0.19;
        $totalTTC  = $totalHT + $vatAmount;
    
        return [
            'items'     => $items,
            'totalHT'   => $totalHT,
            'vatAmount' => $vatAmount,
            'totalTTC'  => $totalTTC,
        ];
    }
}
