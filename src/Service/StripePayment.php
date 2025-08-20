<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePayment
{
    private $redirectUrl;

    public function __construct()
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        Stripe::setApiVersion('2025-07-30.basil');
    }

    public function startPayment($cart, $shippingCost, $orderId)
    {
        $cartProducts = $cart['cart'];

        $products = [
            [
                'quantity' => 1,
                'price' => $shippingCost,
                'name' => "Frais de livraison"
            ]
        ];

        foreach ($cartProducts as $value) {
            $productItem = [];
            $productItem['name'] = $value['product']->getName();
            $productItem['price'] = $value['product']->getPrice();
            $productItem['quantity'] = $value['quantity'];
            $products[] = $productItem;
        }

        $session = Session::create([
            'line_items' => array_map(fn (array $product) => [
                'quantity' => (int) $product['quantity'],
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product['name']
                    ],
                    'unit_amount' => (int) round($product['price'] * 100),
                ],
            ], $products),
            'mode' => 'payment',
            'cancel_url' => 'http://127.0.0.1:8000/pay/cancel',
            'success_url' => 'http://127.0.0.1:8000/pay/success',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR', 'GB'],
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'orderId' => $orderId
                    ]
            ]
        ]);

        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl()
    {
        return $this->redirectUrl;
    }
}