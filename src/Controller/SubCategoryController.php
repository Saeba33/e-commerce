<?php

namespace App\Controller;

use App\Entity\SubCategory;
use App\Form\SubCategoryFormType;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubCategoryController extends AbstractController
{
    #region READ
    #[Route('/admin/subcategory', name: 'app_subcategory')]
    public function index(SubCategoryRepository $subCategoryRepository): Response
    {
        $subCategories = $subCategoryRepository->findAll();

        return $this->render('sub_category/index.html.twig', [
            'controller_name' => 'SubCategoryController',
            'sub_categories' => $subCategories
        ]);
    }
    #endregion

    #region CREATE
    #[Route('/admin/subcategory/new', name: 'app_subcategory_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $subCategory = new SubCategory();
        $form = $this->createForm(SubCategoryFormType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Sous-catégorie créée avec succès');
            return $this->redirectToRoute('app_subcategory');
        }

        return $this->render('sub_category/new.html.twig', [
            'controller_name' => 'SubCategoryController',
            'form' => $form->createView()
        ]);
    }
    #endregion

    #region UPDATE
    #[Route('/admin/subcategory/edit/{id}', name: 'app_subcategory_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, SubCategory $subCategory): Response
    {
        $form = $this->createForm(SubCategoryFormType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Sous-catégorie modifiée avec succès.');
            return $this->redirectToRoute('app_subcategory');
        }

        return $this->render('sub_category/edit.html.twig', [
            'controller_name' => 'SubCategoryController',
            'form' => $form->createView()
        ]);
    }
    #endregion

    #region DELETE
    #[Route('/admin/subcategory/delete/{id}', name: 'app_subcategory_delete')]
    public function delete(EntityManagerInterface $entityManager, SubCategory $subCategory): Response
    {
        $entityManager->remove($subCategory);
        $entityManager->flush();

        $this->addFlash('error', 'Sous-catégorie supprimée avec succès.');
        return $this->redirectToRoute('app_subcategory');
    }
    #endregion
}
