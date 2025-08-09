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
    #[Route('/editor/sub-category', name: 'app_sub_category', methods: ['GET'])]
    public function index(SubCategoryRepository $subCategoryRepository): Response
    {
        return $this->render('sub_category/index.html.twig', [
            'subCategories' => $subCategoryRepository->findAll(),
        ]);
    }
    #endregion

    #region CREATE
    #[Route('/editor/sub-category/new', name: 'app_sub_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subCategory = new SubCategory();
        $form = $this->createForm(SubCategoryFormType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sous-catégorie a été ajoutée');
            return $this->redirectToRoute('app_sub_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sub_category/new.html.twig', [
            'subCategory' => $subCategory,
            'form' => $form,
        ]);
    }
    #endregion

    #region UPDATE
    #[Route('/editor/sub-category/edit/{id}', name: 'app_sub_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubCategory $subCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubCategoryFormType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre sous-catégorie a été modifiée');
            return $this->redirectToRoute('app_sub_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sub_category/edit.html.twig', [
            'subCategory' => $subCategory,
            'form' => $form,
        ]);
    }
    #endregion

    #region DELETE
    #[Route('/editor/sub-category/delete/{id}', name: 'app_sub_category_delete', methods: ['POST'])]
    public function delete(Request $request, SubCategory $subCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subCategory->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($subCategory);
            $entityManager->flush();
            $this->addFlash('danger', 'Votre sous-catégorie a été supprimée.');
        }

        return $this->redirectToRoute('app_sub_category', [], Response::HTTP_SEE_OTHER);
    }
    #endregion
}
