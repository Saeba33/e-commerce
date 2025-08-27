<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use App\Form\OrderFormType;
use App\Entity\OrderProducts;
use App\Service\StripePayment;
use Symfony\Component\Mime\Email;
use App\Repository\OrderRepository;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    #region CREATE
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, Cart $cart, CityRepository $cityRepository): Response
    {
        $cartData = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($cartData['total'])) {
                // Récupérer la ville sélectionnée dans le formulaire
                $city = $form->get('city')->getData();
                
                if (!$city) {
                    $this->addFlash('error', 'Ville non trouvée');
                    return $this->render('order/index.html.twig', [
                        'form' => $form,
                        'total' => $cartData['total'],
                    ]);
                }

                // Calculer les frais de livraison et stocker les valeurs figées (snapshot)
                $shippingCost = $order->isPickup() ? 0 : $city->getShippingCost();
                $order->setShippingCost($shippingCost);
                $order->setCity($city->getName());

                $totalPrice = $cartData['total'] + $shippingCost;
                $order->setTotalPrice($totalPrice);
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setIsPaymentCompleted(false);
                $entityManager->persist($order);
                $entityManager->flush();
                foreach ($cartData['cart'] as $value) {
                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQuantity($value['quantity']);

                    $orderProduct->setProductName($value['product']->getName());
                    $orderProduct->setCurrentPrice($value['product']->getPrice());
                    $entityManager->persist($orderProduct);
                    $entityManager->flush();
                }

                $paymentStripe = new StripePayment();
                $paymentStripe->startPayment($cartData, $order->getShippingCost(), $order->getId());
                $stripeRedirectUrl = $paymentStripe->getStripeRedirectUrl();

                return $this->redirect($stripeRedirectUrl);
            }
        }



        return $this->render('order/index.html.twig', [
            'form' => $form,
            'total' => $cartData['total'],
        ]);
    }
    #endregion

    #region MESSAGE
    #[Route('/order_message', name: 'order_message')]
    public function orderMessage(): Response
    {
        return $this->render('order/order_message.twig');
    }
    #endregion

    #region ORDERS LIST
    #[Route('/editor/order/{type}', name: 'app_orders_show', defaults: ['type' => 'all'])]
    public function getAllORder($type, Request $request, OrderRepository $orderRepository, \Knp\Component\Pager\PaginatorInterface $paginator): Response
    {
        
        if ($type === 'delivered') {
            $data = $orderRepository->findBy(['isDelivered' => true], ['id' => 'DESC']);
        } elseif ($type === 'not-delivered') {
            $data = $orderRepository->findBy([
                'isDelivered' => null,
                'isPaymentCompleted' => true
            ], ['id' => 'DESC']);
        } elseif ($type === 'pending-payment') {
            $data = $orderRepository->findBy([
                'isPaymentCompleted' => false
            ], ['id' => 'DESC']);
        } elseif ($type === 'pickup') {
            $data = $orderRepository->findBy([
                'isPickup' => true
            ], ['id' => 'DESC']);
        } elseif ($type === 'delivery') {
            $data = $orderRepository->findBy([
                'isPickup' => false
            ], ['id' => 'DESC']);
        } elseif ($type === 'is-completed') {
            // Compatibilité avec l'ancien système
            $data = $orderRepository->findBy(['isDelivered' => true], ['id' => 'DESC']);
        } elseif ($type === 'pay-on-stripe-not-delivred') {
            // Compatibilité avec l'ancien système
            $data = $orderRepository->findBy([
                'isDelivered' => null,
                'isPickup' => false,
                'isPaymentCompleted' => true
            ], ['id' => 'DESC']);
        } elseif ($type === 'pay-on-stripe-is-delivred') {
            // Compatibilité avec l'ancien système
            $data = $orderRepository->findBy([
                'isDelivered' => true,
                'isPickup' => false,
                'isPaymentCompleted' => true
            ], ['id' => 'DESC']);
        } elseif ($type === 'no_delivery') {
            // Compatibilité avec l'ancien système
            $data = $orderRepository->findBy([
                'isDelivered' => null,
                'isPickup' => false,
                'isPaymentCompleted' => false
            ], ['id' => 'DESC']);
        } else {
            // Par défaut : toutes les commandes
            $data = $orderRepository->findBy([], ['id' => 'DESC']);
        }

        $orders = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('order/orders.html.twig', [
            'orders' => $orders,
            'currentFilter' => $type,
        ]);
    }
    #endregion

    #region SHIPPING COST
    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function getShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
    }
    #endregion

    #region SHOW ORDER
    #[Route('/editor/order/{id}', name: 'app_order_show')]
    public function showOrder(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
    #endregion


    #region MAJ ORDER STATUT
    #[Route('/editor/order/{id}/is-completed/update', name: 'app_orders_is_completed_update')]
    public function isCompletedUpdate($id, Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $order = $orderRepository->find($id);
    $order->setIsDelivered(true);
        $entityManager->flush();
        $this->addFlash('success', 'Modification effectuée, la commande a pris le statut irée');
        return $this->redirect($request->headers->get('referer'));

    }
    #endregion

    #region DELETE ORDER

#[Route('/editor/order/{id}/delete', name: 'app_order_delete')]
#[Route('/editor/order/{id}/delete/{type}', name: 'app_order_delete_type')]
public function deleteOrder(Request $request, Order $order, EntityManagerInterface $entityManager): Response
{

    $entityManager->remove($order);
    $entityManager->flush();

    $this->addFlash('success', 'Commande supprimée.');

    $referer = $request->headers->get('referer');
    if ($referer) {
        return $this->redirect($referer);
    }

    return $this->redirectToRoute('app_orders_show', ['type' => 'all']);
}
    #endregion




}