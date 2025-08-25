<?php

namespace App\Controller;

use App\Entity\ProductStockHistory;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StripeController extends AbstractController
{
    #region SUCCESSFULLY PAYMENT
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function success(SessionInterface $session): Response
    {

        $session->set('cart', []);

        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    #endregion

    #region CANCELED PAYMENT
    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    #endregion

    #region NOTIFY
    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function notify(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {

        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        $endpoint = $_SERVER['STRIPE_SECRET_ENDPOINT'];
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpoint
            );
            
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
            
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->orderId;
                $order = $orderRepository->find($orderId);

                $cartPrice = $order->getTotalPrice();
                $stripeTotalAmount = $paymentIntent->amount / 100;
                if ($cartPrice == $stripeTotalAmount) {
                    $order->setIsPaymentCompleted(1);
                    
                    // Décrémenter le stock pour chaque produit de la commande
                    foreach ($order->getOrderProducts() as $orderProduct) {
                        $product = $orderProduct->getProduct();
                        $quantity = $orderProduct->getQuantity();
                        
                        // Vérifier que le stock est suffisant
                        if ($product->getStock() >= $quantity) {
                            // Décrémenter le stock
                            $newStock = $product->getStock() - $quantity;
                            $product->setStock($newStock);
                            
                            // Créer une entrée dans l'historique de stock
                            $stockHistory = new ProductStockHistory();
                            $stockHistory->setQuantity(-$quantity); // Quantité négative pour indiquer la diminution
                            $stockHistory->setProduct($product);
                            $stockHistory->setOrigin('order_' . $orderId); // Format: order_123
                            $stockHistory->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                            
                            $entityManager->persist($stockHistory);
                        }
                        // Note: En cas de stock insuffisant, on pourrait annuler la commande
                        // ou envoyer un email à l'administrateur
                    }
                    
                    $entityManager->flush();
                }
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                break;
            default:
                break;
        }

        return new Response('evenement recu avec succes', 200);
    }
    #endregion
}