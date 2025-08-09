<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #region READ
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
        ]);
    }
    #endregion

    #region UPDATE - ROLE EDITOR
    #[Route('/admin/user/{id}/add/role/editor', name: 'app_user_add_role_editor')]
    public function addRoleEditor(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles(['ROLE_EDITOR']);
        $entityManager->flush();

        $this->addFlash('success', "Le rôle éditeur a bien été ajouté à l'utilisateur.");

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove/role/editor', name: 'app_user_remove_role_editor')]
    public function removeRoleEditor(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles(['ROLE_USER']);
        $entityManager->flush();

        $this->addFlash('warning', "Le rôle éditeur a bien été retiré à l'utilisateur.");

        return $this->redirectToRoute('app_user');
    }
    #endregion

    #region DELETE
    #[Route('/admin/user/{id}/delete', name: 'app_user_delete')]
    public function delete(EntityManagerInterface $entityManager, User $user): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('error', "L'utilisateur a bien été supprimé.");

        return $this->redirectToRoute('app_user');
    }
    #endregion
}