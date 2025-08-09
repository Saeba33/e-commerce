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
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
    #endregion

    #region CREATE
    #[Route('/admin/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

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
    #[Route('/admin/category/edit/{id}', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Category $category): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

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
    #[Route('/admin/category/delete/{id}', name: 'app_category_delete')]
    public function delete(EntityManagerInterface $entityManager, Category $category): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('error', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('app_category');
    }
    #endregion
}
