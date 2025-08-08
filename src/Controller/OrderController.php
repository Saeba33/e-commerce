<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use App\Form\OrderFormType;
use App\Entity\OrderProducts;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    #region READ ORDER
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $emi, Cart $cart): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this -> createForm(OrderFormType::class, $order);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($data['total'])) {
                $order->setTotalPrice($data['total']);
                $order->setCreatedAt(new \DateTimeImmutable());
                $emi->persist($order);
                $emi->flush();
                foreach ($data['cart'] as $value) {
                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQuantity($value['quantity']);
                    $emi->persist($orderProduct);
                    $emi->flush();
                }
            }

            if ($order->isPayOnDelivery()) {

                $session->set('cart', []);

            }

            $this->addFlash('success', 'Commande enregistrée !');

            return $this->redirectToRoute('app_cart');
        }

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form -> createView(),
            'total' => $data['total']
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

}
