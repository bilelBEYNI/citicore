<?php
// src/Entity/CartService_.php
namespace App\Entity;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService_
{
    private const CART_KEY = 'panier';
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getCart(): array
    {
        return $this->session->get(self::CART_KEY, []);
    }

    public function add(int $id, int $qty = 1): void
    {
        $cart = $this->getCart();
        $cart[$id] = ($cart[$id] ?? 0) + $qty;
        $this->session->set(self::CART_KEY, $cart);
    }

    public function update(int $id, int $qty): void
    {
        $cart = $this->getCart();
        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }
        $this->session->set(self::CART_KEY, $cart);
    }

    public function remove(int $id): void
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->session->set(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        $this->session->remove(self::CART_KEY);
    }
}
