<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #region READ
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();
        $products = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'categories' => $categories,
            'products' => $products,
        ]);
    }
    #endregion

    #region READ - SHOW PRODUCT
    #[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct(Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $lastProductsAdded = $productRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('home/show.html.twig', [
            'product' => $product,
            'products' => $lastProductsAdded,
            'categories' => $categoryRepository->findAll()
        ]);
    }
    #endregion

    #region FILTER - BY SUBCATEGORY
    #[Route('/product/subcategory/{id}/filter', name: 'app_home_product_filter', methods: ['GET'])]
    public function filterBySubCategory(int $id, SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository): Response
    {
        $subCategory = $subCategoryRepository->find($id);
        $products = $subCategory->getProducts();

        return $this->render('home/filter.html.twig', [
            'products' => $products,
            'subCategory' => $subCategory,
            'categories' => $categoryRepository->findAll()
        ]);
    }
    #endregion
}
