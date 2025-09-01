<?php

namespace App\Controller;

use App\Service\Cart;
use App\Repository\CategoryRepository;
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
    public function index(SessionInterface $session, Cart $cart, CategoryRepository $categoryRepository): Response
    {
        $cartData = $cart->getCart($session);

        return $this->render('cart/index.html.twig', [
            'items' => $cartData['cart'],
            'total' => $cartData['total'],
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    #endregion

    #region ADD PRODUCTS
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function addProduct($id, SessionInterface $session): Response
    {
        return $this->addToCart($id, 1, $session);
    }

    #[Route('/cart/add/{id}/quantity/{quantity}', name: 'app_cart_add_quantity', methods: ['GET'])]
    public function addProductWithQuantity($id, $quantity, SessionInterface $session): Response
    {
        return $this->addToCart($id, $quantity, $session);
    }


    private function addToCart($productId, $quantityToAdd, SessionInterface $session): Response
    {
        // 1. Find product
        $product = $this->productRepository->find($productId);
        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_home');
        }

        // 2. Validate quantity
        if ($quantityToAdd <= 0) {
            $this->addFlash('error', 'Quantité invalide');
            return $this->redirectToRoute('app_cart');
        }

        // 3. Get current cart
        $cart = $session->get('cart', []);
        $currentQuantity = $cart[$productId] ?? 0;
        $newTotalQuantity = $currentQuantity + $quantityToAdd;

        // 4. Check stock availability
        if ($newTotalQuantity > $product->getStock()) {
            $this->addFlash(
                'error',
                "Stock insuffisant pour « {$product->getName()} ». " .
                "Disponible: {$product->getStock()}, dans le panier: {$currentQuantity}"
            );
            return $this->redirectToRoute('app_cart');
        }

        // 5. Add to cart
        $cart[$productId] = $newTotalQuantity;
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }
    #endregion

    #region REMOVE PRODUCTS
    #[Route('/cart/remove/{id}', name: 'app_cart_remove_product', methods: ['GET'])]
    public function removeProduct($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        unset($cart[$id]);
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['GET'])]
    public function clear(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
    #endregion
}