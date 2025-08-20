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
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, Cart $cart): Response
    {
        $cartData = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($cartData['total'])) {
                $order->setTotalPrice($cartData['total']);
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setIsPaymentCompleted(0);
                $entityManager->persist($order);
                $entityManager->flush();
                foreach ($cartData['cart'] as $value) {
                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQuantity($value['quantity']);
                    $entityManager->persist($orderProduct);
                    $entityManager->flush();
                }


                if ($order->isPayOnDelivery()) {

                    $session->set('cart', []);

                    $html = $this->renderView('mail/orderConfirm.html.twig', [
                        'order' => $order

                    ]);
                    $email = (new Email())
                    ->from('maboutique@contact.com')
                    ->to($order->getEmail())
                    ->subject('Confirmation de réception de commande')
                    ->html($html);
                    $this->mailer->send($email);

                    return $this->redirectToRoute('order_message');
                }
                $paymentStripe = new StripePayment();
                $shippingCost = $order->getCity()->getShippingCost();
                $paymentStripe->startPayment($cartData, $shippingCost, $order->getId());
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
    #[Route('/editor/order', name: 'app_orders_show')]
    public function getAllORder(Request $request, OrderRepository $orderRepository, \Knp\Component\Pager\PaginatorInterface $paginator): Response
    {
        $orders = $orderRepository->findAll();
        $orders = $paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('order/orders.html.twig', [
            'orders' => $orders,
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
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $order = $orderRepository->find($id);
        $order->setIsCompleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Modification effectuée, la commande a pris le statut irée');
        return $this->redirectToRoute('app_orders_show');
    }
    #endregion

    #region DELETE ORDER
    #[Route('/editor/order/{id}/delete', name: 'app_order_delete')]
    public function deleteOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('error', 'Commande supprimée');
        return $this->redirectToRoute('app_orders_show');
    }
    #endregion



}