<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductHistory;
use App\Form\ProductFormType;
use App\Form\ProductHistoryFormType;
use App\Repository\ProductRepository;
use DateTimeImmutable;
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

            $stockHistory = new ProductHistory();
            $stockHistory->setQuantity($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $emi->persist($stockHistory);
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


    #region ADD STOCK
    #[Route('add/product/{id}/', name: 'app_product_stock_add', methods: ['GET', 'POST'])]
    public function addStock(EntityManagerInterface $emi, ProductRepository $repo, $id, Request $request): Response
    {
        $product = $repo->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        $stockAdd = new ProductHistory();
        $stockAdd->setProduct($product);
        $form = $this->createForm(ProductHistoryFormType::class, $stockAdd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la quantité à ajouter
            $quantityToAdd = $stockAdd->getQuantity();

            // Debug pour vérifier les valeurs
            dump('Form submitted and valid', $quantityToAdd, $product->getStock());

            // Ajouter la quantité au stock existant
            $product->setStock($product->getStock() + $quantityToAdd);

            // Définir la date de l'ajout
            $stockAdd->setCreatedAt(new DateTimeImmutable());

            // Sauvegarder les modifications
            $emi->persist($stockAdd);
            $emi->persist($product);
            $emi->flush();

            // Message de succès
            $this->addFlash('success', sprintf('Stock ajouté avec succès !', $quantityToAdd, $product->getName()));

            // Redirection vers la liste des produits
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/addStock.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
    #endregion
}