<?php

namespace App\Service;

use App\Repository\ProductRepository;

class Cart 
{
    public function __construct(private readonly ProductRepository $productRepository) {
        
    }

    public function getCart($session) {
        $cart = $session->get('cart', []);
    }
}