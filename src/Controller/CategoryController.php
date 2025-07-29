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
    #[Route('/category', name: 'app_category')]
    public function listCategories(CategoryRepository $repo): Response
    {
        $categories = $repo->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }

    #[Route('/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $emi, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($category);
            $emi->flush();

            $this->addFlash('notice', 'Catégorie créée avec succès');
            return $this->redirectToRoute('app_category');

        }
        return $this->render('category/newCategory.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form->createView()
        ]);
    }


    #[Route('/category/update/{id}', name: 'app_category_update')]
    public function updateCategroy(Request $request, EntityManagerInterface $emi, $id, CategoryRepository $repo): Response
    {
        $category = $repo->find($id);
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($category);
            $emi->flush();

            $this->addFlash('notice', 'Catégorie modifiée avec succès');
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/updateCategory.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete')]
    public function deleteUser(EntityManagerInterface $emi, $id, CategoryRepository $repo): Response
    {
        $category = $repo->find($id);
        $emi->remove($category);
        $emi->flush();

        $this->addFlash('notice', 'Catégorie supprimée avec succès');
        return $this->redirectToRoute('app_category');
    }

}