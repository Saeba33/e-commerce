<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request): Response
    {

        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form-> handleRequest($request);


        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView()
        ]);
    }
}