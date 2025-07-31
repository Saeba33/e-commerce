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
    #[Route('admin/category', name: 'app_category')]
    public function listCategories(CategoryRepository $CategoryRepository): Response
    {
        $categories = $CategoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }

    #[Route('admin/category/new', name: 'app_category_new')]
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


    #[Route('admin/category/edit/{id}', name: 'app_category_edit')]
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

    #[Route('admin/category/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(EntityManagerInterface $emi, Category $category): Response
    {
        $emi->remove($category);
        $emi->flush();

        $this->addFlash('error', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('app_category');
    }

}