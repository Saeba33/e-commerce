<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


final class ProductController extends AbstractController
{
    #region READ
    #[Route('editor/product', name: 'app_product')]
    public function listProducts(ProductRepository $repo): Response
    {
        $products = $repo->findAll();

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products
        ]);
    }
    #endregion

    #region CREATE
    #[Route('editor/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function newProduct(EntityManagerInterface $emi, Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalName);
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move($this->getParameter('image_directory'), $newFileImageName);

                } catch (FileException $exception) {
                    //gestion du message d'erreur
                }
                    $product->setImage($newFileImageName);
            }

            $emi->persist($product);
            $emi->flush();

            $this->addFlash('success', 'Produit créé avec succès');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/new.html.twig', [
            'controller_name' => 'ProductController',
            'form' => $form->createView()
        ]);
    }
    #endregion

    #region UPDATE
    #[Route('editor/product/edit/{id}', name: 'app_product_edit')]
    public function editProduct(Request $request, EntityManagerInterface $emi, Product $product): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/edit.html.twig', [
            'controller_name' => 'ProductController',
            'form' => $form->createView()
        ]);
    }    
    #endregion
    
    #region DELETE
    #[Route('editor/product/delete/{id}', name: 'app_product_delete')]
    public function deleteProduct(EntityManagerInterface $emi, Product $product): Response
    {
        $emi->remove($product);
        $emi->flush();

        $this->addFlash('error', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('app_product');
    }
    #endregion
}