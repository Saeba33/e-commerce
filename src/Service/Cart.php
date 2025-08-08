<?php

namespace App\Service;

use App\Repository\ProductRepository;

class Cart
{
    public function __construct(private readonly ProductRepository $productRepository)
    {

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
}