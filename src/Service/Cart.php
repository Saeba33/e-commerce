<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    
    public function __construct(private readonly ProductRepository $productRepository, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    public function getCart($session): array
    {

        $cart = $session->get('cart', []);
        $cartWidthData = [];

        foreach ($cart as $id => $quantity) {
            $cartWidthData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = array_sum(array_map(function ($item) {
            return $item['product'] -> getPrice() * $item['quantity'];
        }, $cartWidthData));

        return [
            'cart' => $cartWidthData,
            'total' => $total,
        ];
    }

    /**
     * Calculates the total number of items in the cart.
     */
    public function getCartQuantity(): int
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        // array_sum calculates the total of all quantities in the cart array
        return array_sum($cart);
    }
}