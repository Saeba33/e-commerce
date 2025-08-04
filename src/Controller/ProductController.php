<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductHistory;
use App\Form\ProductFormType;
use App\Form\ProductHistoryFormType;
use App\Repository\ProductRepository;
use App\Repository\ProductHistoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductController extends AbstractController
{
    #region READ
    #[Route('editor/product', name: 'app_product', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    #endregion

    #region CREATE
    #[Route('editor/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function newProduct(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFileImageName);
                } catch (FileException $exception) {}
                $product->setImage($newFileImageName);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new ProductHistory();
            $stockHistory->setQuantity($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();

            $this->addFlash('success', 'Votre produit a été ajouté');
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    #endregion

    #region SHOW
    #[Route('editor/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    #endregion

    #region UPDATE
    #[Route('editor/product/edit/{id}', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function editProduct(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalName);
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFileImageName);
                } catch (FileException $exception) {}
                $product->setImage($newFileImageName);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre produit a été modifié');
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    #endregion

    #region DELETE
    #[Route('editor/product/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($product);
            $this->addFlash('danger', 'Votre produit a été supprimé.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
    }
    #endregion

    #region ADD STOCK
    #[Route('add/product/{id}/', name: 'app_product_stock_add', methods: ['GET', 'POST'])]
    public function addStock($id, EntityManagerInterface $entityManager, Request $request, ProductRepository $productRepository): Response
    {
        $stockAdd = new ProductHistory();
        $form = $this->createForm(ProductHistoryFormType::class, $stockAdd);
        $form->handleRequest($request);

        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            if($stockAdd->getQuantity() > 0){
                $newQuantity = $product->getStock() + $stockAdd->getQuantity();
                $product->setStock($newQuantity);

                $stockAdd->setCreatedAt(new DateTimeImmutable());
                $stockAdd->setProduct($product);
                $entityManager->persist($stockAdd);
                $entityManager->flush();

                $this->addFlash('success', "Le stock du produit a été modifié");
                return $this->redirectToRoute('app_product');
            } else {
                $this->addFlash('danger', "Le stock du produit ne doit pas être inférieur à zéro");
                return $this->redirectToRoute('app_product_stock_add', ['id'=>$product->getId()]);
            }
        }

        return $this->render('product/addStock.html.twig', [
            'form'=> $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('add/product/{id}/history', name: 'app_product_stock_add_history', methods: ['GET'])]
    public function showHistoryProductStock($id, ProductRepository $productRepository, ProductHistoryRepository $productHistoryRepository): Response
    {
        $product = $productRepository->find($id);
        $productAddHistory = $productHistoryRepository->findBy(['product'=>$product],['id'=>'DESC']);
        
        return $this->render('product/historyStock.html.twig',[
            "productsAdded"=>$productAddHistory
        ]);
    }
    #endregion
}