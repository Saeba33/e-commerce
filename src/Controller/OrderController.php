<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Form\OrderFormType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class OrderController extends AbstractController
{
    #region READ ORDER
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartWidthData = [];

        foreach ($cart as $id => $quantity) {
            $cartWidthData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = array_sum(array_map(function ($item) {
            return $item['product'] -> getPrice() * $item['quantity'];
        }, $cartWidthData));

        $order = new Order();
        $form = $this -> createForm(OrderFormType::class, $order);
        $form -> handleRequest($request);


        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form -> createView(),
            'total' => $total
        ]);
    }
    #endregion

    #region READ SHIIPING COST
    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
    }
    #endregion

    #region ENVOI FORMULAIRE
    // #[Route('/city/{id}/', name: 'app_city_shipping_cost')]
    // public function validateOrder(City $city): Response
    // {
   

    //     return ;
    // }
    #endregion
}