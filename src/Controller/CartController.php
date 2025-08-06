<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {

    }
    #region READ
    #[Route('/cart', name: 'app_cart', methods:['GET'])]
    public function index(SessionInterface $session): Response
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


        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'items' => $cartWidthData,
            'total' => $total,
        ]);
    }
    #endregion

    #region CREATE
    #[Route("/cart/add/{id}/", name: "app_cart_new", methods: ['GET'])]
    public function addProductToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
    #endregion


    #region UPDATE
    #endregion


    #region DELETE
    #endregion

}