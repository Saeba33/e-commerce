<?php

namespace App\Controller;

use App\Service\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    #region READ
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session, Cart $cart): Response
    {
        $cartData = $cart->getCart($session);

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'items' => $cartData['cart'],
            'total' => $cartData['total'],
        ]);
    }
    #endregion

    #region CREATE
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function addProduct(int $id, SessionInterface $session): Response
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

    #region DELETE - SINGLE PRODUCT
    #[Route('/cart/remove/{id}', name: 'app_cart_remove_product', methods: ['GET'])]
    public function removeProduct(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
    #endregion

    #region DELETE - ALL CART
    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['GET'])]
    public function clear(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
    #endregion


}
