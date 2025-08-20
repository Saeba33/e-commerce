<?php

namespace App\Controller;

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
    public function notify(Request $request): Response
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
                $fileName = 'stripe-detail-'.uniqid().'.txt';
                $orderId = $paymentIntent->metadata->orderId;
                
                file_put_contents($fileName, $orderId);
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