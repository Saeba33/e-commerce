<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    #region READ
    #[Route('admin/category', name: 'app_category')]
    public function listCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
    #endregion

    #region CREATE
    #[Route('admin/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function newCategory(EntityManagerInterface $emi, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($category);
            $emi->flush();

            $this->addFlash('success', 'Catégorie créée avec succès');
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form->createView()
        ]);
    }
    #endregion

    #region UPDATE
    #[Route('admin/category/edit/{id}', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(Request $request, EntityManagerInterface $emi, Category $category): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès.');
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/edit.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form->createView()
        ]);
    }
    #endregion

    #region DELETE
    #[Route('admin/category/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(EntityManagerInterface $emi, Category $category): Response
    {
        $emi->remove($category);
        $emi->flush();

        $this->addFlash('error', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('app_category');
    }
    #endregion
}